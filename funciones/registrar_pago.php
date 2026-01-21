<?php
session_start();
include("../inc/conectar.php");
header('Content-Type: application/json');

try {
    // Validar sesión de empleado
    if (empty($_SESSION['SISTEMA']['id_empleado'])) {
        throw new Exception("No hay empleado autenticado");
    }

    // Validar datos de entrada
    $requiredFields = ['id_cuenta_cobrar', 'monto_pago', 'metodo_pago'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es requerido");
        }
    }

    if (!is_numeric($_POST['monto_pago']) || $_POST['monto_pago'] <= 0) {
        throw new Exception("Monto de pago inválido");
    }

    // Iniciar transacción
    $consulta->beginTransaction();

    // 1. Obtener saldo actual
    $stmt = $consulta->prepare("SELECT saldo_pendiente, monto_total FROM cuentas_por_cobrar WHERE id_cuenta_cobrar = ?");
    $stmt->execute([$_POST['id_cuenta_cobrar']]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cuenta) {
        throw new Exception("Cuenta por cobrar no encontrada");
    }

    $saldo_anterior = $cuenta['saldo_pendiente'];
    $nuevo_saldo = $saldo_anterior - $_POST['monto_pago'];

    // 2. Registrar el pago (con ID del empleado)
    $stmt = $consulta->prepare("INSERT INTO pagos_cuentas 
                              (id_cuenta_cobrar, monto_pago, metodo_pago, referencia, observaciones, fecha_pago, id_usuario) 
                              VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([
        $_POST['id_cuenta_cobrar'],
        $_POST['monto_pago'],
        $_POST['metodo_pago'],
        $_POST['referencia'] ?? null,
        $_POST['observaciones'] ?? null,
        $_SESSION['SISTEMA']['id_empleado'] // Usar ID de empleado de la sesión
    ]);

    $id_pago = $consulta->lastInsertId();

    // 3. Actualizar saldo pendiente
    $stmt = $consulta->prepare("UPDATE cuentas_por_cobrar SET saldo_pendiente = ? WHERE id_cuenta_cobrar = ?");
    $stmt->execute([$nuevo_saldo, $_POST['id_cuenta_cobrar']]);

    // 4. Actualizar estado de la cuenta
    $estado = ($nuevo_saldo <= 0) ? 'pagado' : (($nuevo_saldo < $cuenta['monto_total']) ? 'parcial' : 'pendiente');
    $stmt = $consulta->prepare("UPDATE cuentas_por_cobrar SET estado = ? WHERE id_cuenta_cobrar = ?");
    $stmt->execute([$estado, $_POST['id_cuenta_cobrar']]);

    // 5. Verificar vencimiento
    $hoy = date('Y-m-d');
    $stmt = $consulta->prepare("UPDATE cuentas_por_cobrar 
                              SET estado = 'vencido' 
                              WHERE id_cuenta_cobrar = ? 
                              AND fecha_vencimiento < ? 
                              AND estado != 'pagado'");
    $stmt->execute([$_POST['id_cuenta_cobrar'], $hoy]);

    // Obtener datos para respuesta
    // Reemplazar la consulta final con esta versión mejorada:
    $stmt = $consulta->prepare("SELECT 
p.*, 
c.monto_total, 
c.saldo_pendiente AS nuevo_saldo,
(c.saldo_pendiente + p.monto_pago) AS saldo_anterior,
CONCAT(cl.nombre, ' ', cl.apellido_p, ' ', cl.apellido_m) AS cliente_nombre, 
v.folio AS venta_folio, 
e.nombre AS empleado_nombre 
FROM pagos_cuentas p 
JOIN cuentas_por_cobrar c ON p.id_cuenta_cobrar = c.id_cuenta_cobrar 
JOIN clientes cl ON c.id_cliente = cl.id_cliente 
JOIN ventas v ON c.id_venta = v.id_venta 
JOIN empleados e ON v.idempleados = e.id_empleado 
WHERE p.id_pago = ?");
    $stmt->execute([$id_pago]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    // Asegurarnos de que los valores numéricos sean correctos
    $pago['saldo_anterior'] = (float) $pago['saldo_anterior'];
    $pago['nuevo_saldo'] = (float) $pago['nuevo_saldo'];
    $pago['monto_pago'] = (float) $pago['monto_pago'];

    $consulta->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pago registrado correctamente',
        'pago' => $pago,
        'empleado' => [
            'id' => $_SESSION['SISTEMA']['id_empleado'],
            'nombre' => $_SESSION['SISTEMA']['nombre']
        ]
    ]);

} catch (PDOException $e) {
    $consulta->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    if (isset($consulta)) {
        $consulta->rollBack();
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}