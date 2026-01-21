<?php
include("../inc/conectar.php");
header('Content-Type: application/json');

try {
    // Validar datos
    if (empty($_POST['id_cuenta_cobrar'])) {
        throw new Exception("No se especificó la cuenta por cobrar");
    }

    if (empty($_POST['monto_pago']) || $_POST['monto_pago'] <= 0) {
        throw new Exception("Monto de pago inválido");
    }

    // Iniciar transacción
    $consulta->beginTransaction();

    // 1. Obtener el saldo actual ANTES del pago (saldo anterior)
    $stmt = $consulta->prepare("SELECT saldo_pendiente FROM cuentas_por_cobrar WHERE id_cuenta_cobrar = ?");
    $stmt->execute([$_POST['id_cuenta_cobrar']]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
    $saldo_anterior = $cuenta['saldo_pendiente'];

    // 2. Registrar el pago
    $stmt = $consulta->prepare("INSERT INTO pagos_cuentas 
                              (id_cuenta_cobrar, monto_pago, metodo_pago, referencia, observaciones, fecha_pago) 
                              VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_POST['id_cuenta_cobrar'],
        $_POST['monto_pago'],
        $_POST['metodo_pago'],
        $_POST['referencia'] ?? null,
        $_POST['observaciones'] ?? null
    ]);

    $id_pago = $consulta->lastInsertId();

    // 3. Actualizar saldo pendiente y calcular NUEVO saldo
    $nuevo_saldo = $saldo_anterior - $_POST['monto_pago'];
    
    $stmt = $consulta->prepare("UPDATE cuentas_por_cobrar 
                              SET saldo_pendiente = ? 
                              WHERE id_cuenta_cobrar = ?");
    $stmt->execute([$nuevo_saldo, $_POST['id_cuenta_cobrar']]);

    // 4. Actualizar estado de la cuenta
    $stmt = $consulta->prepare("UPDATE cuentas_por_cobrar 
                              SET estado = CASE 
                                  WHEN ? <= 0 THEN 'pagado'
                                  WHEN ? < monto_total THEN 'parcial'
                                  ELSE estado
                              END
                              WHERE id_cuenta_cobrar = ?");
    $stmt->execute([$nuevo_saldo, $nuevo_saldo, $_POST['id_cuenta_cobrar']]);

    // 5. Marcar como vencido si corresponde
    $hoy = date('Y-m-d');
    $stmt = $consulta->prepare("UPDATE cuentas_por_cobrar 
                              SET estado = 'vencido'
                              WHERE id_cuenta_cobrar = ? 
                              AND fecha_vencimiento < ? 
                              AND estado != 'pagado'");
    $stmt->execute([$_POST['id_cuenta_cobrar'], $hoy]);

    // Obtener datos para el comprobante (incluyendo los saldos)
    $stmt = $consulta->prepare("
        SELECT 
            p.*,
            c.id_cuenta_cobrar,
            c.monto_total,
            c.saldo_pendiente,
            CONCAT(cl.nombre, ' ', cl.apellido_p, ' ', cl.apellido_m) AS cliente_nombre,
            v.folio AS venta_folio
        FROM 
            pagos_cuentas p
        JOIN 
            cuentas_por_cobrar c ON p.id_cuenta_cobrar = c.id_cuenta_cobrar
        JOIN 
            clientes cl ON c.id_cliente = cl.id_cliente
        JOIN 
            ventas v ON c.id_venta = v.id_venta
        WHERE 
            p.id_pago = ?
    ");
    $stmt->execute([$id_pago]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    // Agregar los saldos calculados a la respuesta
    $pago['saldo_anterior'] = $saldo_anterior;
    $pago['nuevo_saldo'] = $nuevo_saldo;

    $consulta->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pago registrado correctamente',
        'pago' => $pago
    ]);

} catch (Exception $e) {
    $consulta->rollBack();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}