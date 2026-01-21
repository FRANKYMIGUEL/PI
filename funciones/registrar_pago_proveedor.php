<?php
session_start();
include("../inc/conectar.php");

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Habilitar CORS si es necesario
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
    // Verificar método de la solicitud
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Validar sesión
    if (empty($_SESSION['SISTEMA']['id_empleado'])) {
        throw new Exception("No hay empleado autenticado", 401);
    }

    // Validar datos requeridos
    $requiredFields = ['id_cuenta_pagar', 'monto_pago', 'metodo_pago'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Validar tipo de datos
    if (!is_numeric($input['monto_pago']) || $input['monto_pago'] <= 0) {
        throw new Exception("Monto de pago inválido", 400);
    }

    // Iniciar transacción
    $consulta->beginTransaction();

    // 1. Obtener datos completos de la cuenta
    $stmt = $consulta->prepare("SELECT 
                                cp.*,
                                p.nombre AS proveedor_nombre,
                                c.folio AS compra_folio,
                                e.nombre AS empleado_nombre
                               FROM cuentas_por_pagar cp
                               JOIN proveedores p ON cp.id_proveedor = p.id_proveedor
                               JOIN compras c ON cp.id_compra = c.id_compra
                               JOIN empleados e ON c.idempleados = e.id_empleado
                               WHERE cp.id_cuenta_pagar = ?");
    $stmt->execute([$input['id_cuenta_pagar']]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cuenta) {
        throw new Exception("La cuenta por pagar no existe", 404);
    }
    
    $saldo_anterior = (float)$cuenta['saldo_pendiente'];
    $monto_pago = (float)$input['monto_pago'];
    $nuevo_saldo = $saldo_anterior - $monto_pago;

    // 2. Registrar el pago con el ID del usuario
    $stmt = $consulta->prepare("INSERT INTO pagos_proveedores 
                              (id_cuenta_pagar, monto_pago, metodo_pago, referencia, observaciones, fecha_pago, id_usuario) 
                              VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([
        $input['id_cuenta_pagar'],
        $monto_pago,
        $input['metodo_pago'],
        $input['referencia'] ?? null,
        $input['observaciones'] ?? null,
        $_SESSION['SISTEMA']['id_empleado']
    ]);
    
    $id_pago = $consulta->lastInsertId();

    // 3. Actualizar saldo pendiente
    $stmt = $consulta->prepare("UPDATE cuentas_por_pagar 
                              SET saldo_pendiente = ? 
                              WHERE id_cuenta_pagar = ?");
    $stmt->execute([$nuevo_saldo, $input['id_cuenta_pagar']]);

    // 4. Actualizar estado de la cuenta
    $nuevo_estado = ($nuevo_saldo <= 0) ? 'pagado' : 
                   (($nuevo_saldo < $cuenta['monto_total']) ? 'parcial' : 'pendiente');
    
    $stmt = $consulta->prepare("UPDATE cuentas_por_pagar 
                              SET estado = ?
                              WHERE id_cuenta_pagar = ?");
    $stmt->execute([$nuevo_estado, $input['id_cuenta_pagar']]);

    // 5. Marcar como vencido si corresponde
    $hoy = date('Y-m-d');
    $stmt = $consulta->prepare("UPDATE cuentas_por_pagar 
                              SET estado = 'vencido'
                              WHERE id_cuenta_pagar = ? 
                              AND fecha_vencimiento < ? 
                              AND estado != 'pagado'");
    $stmt->execute([$input['id_cuenta_pagar'], $hoy]);

    // Obtener datos completos para el ticket
    $stmt = $consulta->prepare("
        SELECT 
            pp.*,
            cp.id_cuenta_pagar,
            cp.monto_total,
            cp.saldo_pendiente,
            p.nombre AS proveedor_nombre,
            c.folio AS compra_folio,
            eu.nombre AS empleado_usuario_nombre
        FROM 
            pagos_proveedores pp
        JOIN 
            cuentas_por_pagar cp ON pp.id_cuenta_pagar = cp.id_cuenta_pagar
        JOIN 
            proveedores p ON cp.id_proveedor = p.id_proveedor
        JOIN 
            compras c ON cp.id_compra = c.id_compra
        JOIN
            empleados eu ON pp.id_usuario = eu.id_empleado
        WHERE 
            pp.id_pago = ?
    ");
    $stmt->execute([$id_pago]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    // Formatear valores monetarios
    $formato_moneda = function($valor) {
        return number_format($valor, 2, '.', ',');
    };

    $consulta->commit();

    // Preparar respuesta
    $respuesta = [
        'success' => true,
        'message' => 'Pago a proveedor registrado correctamente',
        'pago' => [
            'id_pago' => $id_pago,
            'id_cuenta_pagar' => $pago['id_cuenta_pagar'],
            'fecha_pago' => date('Y-m-d H:i:s'),
            'proveedor_nombre' => $pago['proveedor_nombre'] ?? 'Proveedor no especificado',
            'compra_folio' => $pago['compra_folio'] ?? 'N/A',
            'metodo_pago' => $input['metodo_pago'] ?? 'efectivo',
            'referencia' => $input['referencia'] ?? '',
            'observaciones' => $input['observaciones'] ?? '',
            'monto_pago' => $formato_moneda($monto_pago),
            'saldo_anterior' => $formato_moneda($saldo_anterior),
            'nuevo_saldo' => $formato_moneda($nuevo_saldo),
            'empleado' => $pago['empleado_usuario_nombre'] ?? 'Usuario no identificado',
            'monto_total' => $formato_moneda($pago['monto_total'] ?? 0),
            'estado' => $nuevo_estado
        ]
    ];

    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    if (isset($consulta)) {
        $consulta->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    if (isset($consulta)) {
        $consulta->rollBack();
    }
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
