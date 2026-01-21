<?php
include("../inc/conectar.php");
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['SISTEMA']['id_empleado'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

try {
    $action = $_GET['action'] ?? '';

    // 1. Verificar si hoy ya se hizo un corte
    $sqlUltimoCorteHoy = "SELECT fecha_fin_corte FROM cortes_caja 
                         WHERE DATE(fecha_fin_corte) = CURDATE() 
                         AND id_usuario = :usuario_id
                         ORDER BY fecha_fin_corte DESC LIMIT 1";
    $stmt = $consulta->prepare($sqlUltimoCorteHoy);
    $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
    $stmt->execute();
    $ultimoCorteHoy = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Determinar el período
    if ($ultimoCorteHoy && $ultimoCorteHoy['fecha_fin_corte']) {
        // Si ya hubo un corte hoy, comenzar desde ese momento
        $fechaInicio = $ultimoCorteHoy['fecha_fin_corte'];
    } else {
        // Si no hay cortes hoy, comenzar desde el inicio del día
        $fechaInicio = date('Y-m-d 00:00:00');
    }
    $fechaFin = date('Y-m-d H:i:s');

    switch ($action) {
        case 'obtener_resumen':
            // Consulta para obtener el resumen principal con filtro por usuario
            $sql = "SELECT 
                    (SELECT COALESCE(SUM(total), 0) FROM ventas 
                    WHERE fecha >= :fechaInicio AND fecha <= :fechaFin
                    AND idempleados = :usuario_id
                    AND tipo_pago = 'contado' AND fechacancelada IS NULL) AS ventas_efectivo,

                    (SELECT COALESCE(SUM(total), 0) FROM ventas 
                    WHERE fecha >= :fechaInicio AND fecha <= :fechaFin
                    AND idempleados = :usuario_id
                    AND tipo_pago != 'contado' AND fechacancelada IS NULL) AS ventas_credito,

                    (SELECT COALESCE(SUM(pc.monto_pago), 0) FROM pagos_cuentas pc
                    JOIN cuentas_por_cobrar cc ON pc.id_cuenta_cobrar = cc.id_cuenta_cobrar
                    WHERE pc.fecha_pago >= :fechaInicio AND pc.fecha_pago <= :fechaFin
                    AND pc.id_usuario = :usuario_id
                    AND cc.estado != 'pendiente') AS cobros_hoy,

                    (SELECT COALESCE(SUM(pp.monto_pago), 0) FROM pagos_proveedores pp
                    JOIN cuentas_por_pagar cp ON pp.id_cuenta_pagar = cp.id_cuenta_pagar
                    JOIN compras c ON cp.id_compra = c.id_compra
                    WHERE pp.fecha_pago >= :fechaInicio AND pp.fecha_pago <= :fechaFin
                    AND c.idempleados = :usuario_id
                    AND cp.estado != 'pendiente') AS pagos_hoy,

                    (SELECT COALESCE(SUM(total), 0) FROM compras 
                    WHERE fecha >= :fechaInicio AND fecha <= :fechaFin
                    AND idempleados = :usuario_id
                    AND fechaeliminada IS NULL AND (tipo_pago = 'contado' OR tipo_pago IS NULL)) AS compras_contado,

                    (SELECT COALESCE(SUM(total), 0) FROM compras 
                    WHERE fecha >= :fechaInicio AND fecha <= :fechaFin
                    AND idempleados = :usuario_id
                    AND fechaeliminada IS NULL AND tipo_pago != 'contado' AND tipo_pago IS NOT NULL) AS compras_credito";

            $stmt = $consulta->prepare($sql);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
            $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
            $stmt->execute();
            $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

            // Consulta para obtener los pagos a cuentas por cobrar (clientes) del usuario
            $sqlPagosClientes = "SELECT 
                    p.id_pago, 
                    p.id_cuenta_cobrar, 
                    p.monto_pago, 
                    p.fecha_pago, 
                    p.metodo_pago, 
                    p.referencia, 
                    p.observaciones,
                    c.nombre AS cliente,
                    v.folio AS folio_venta,
                    e.nombre AS empleado_nombre
                    FROM pagos_cuentas p
                    JOIN cuentas_por_cobrar cc ON p.id_cuenta_cobrar = cc.id_cuenta_cobrar
                    JOIN ventas v ON cc.id_venta = v.id_venta
                    JOIN clientes c ON cc.id_cliente = c.id_cliente
                    JOIN empleados e ON p.id_usuario = e.id_empleado
                    WHERE p.fecha_pago >= :fechaInicio AND p.fecha_pago <= :fechaFin
                    AND p.id_usuario = :usuario_id
                    ORDER BY p.fecha_pago DESC";

            $stmtPagosClientes = $consulta->prepare($sqlPagosClientes);
            $stmtPagosClientes->bindParam(':fechaInicio', $fechaInicio);
            $stmtPagosClientes->bindParam(':fechaFin', $fechaFin);
            $stmtPagosClientes->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
            $stmtPagosClientes->execute();
            $pagosClientes = $stmtPagosClientes->fetchAll(PDO::FETCH_ASSOC);

            // Consulta para obtener los pagos a proveedores del usuario
            $sqlPagosProveedores = "SELECT 
                    p.id_pago, 
                    p.id_cuenta_pagar, 
                    p.monto_pago, 
                    p.fecha_pago, 
                    p.metodo_pago, 
                    p.referencia, 
                    p.observaciones,
                    pr.nombre AS proveedor,
                    c.folio AS folio_compra
                    FROM pagos_proveedores p
                    JOIN cuentas_por_pagar cp ON p.id_cuenta_pagar = cp.id_cuenta_pagar
                    JOIN compras c ON cp.id_compra = c.id_compra
                    JOIN proveedores pr ON cp.id_proveedor = pr.id_proveedor
                    WHERE p.fecha_pago >= :fechaInicio AND p.fecha_pago <= :fechaFin
                    AND c.idempleados = :usuario_id
                    ORDER BY p.fecha_pago DESC";

            $stmtPagosProveedores = $consulta->prepare($sqlPagosProveedores);
            $stmtPagosProveedores->bindParam(':fechaInicio', $fechaInicio);
            $stmtPagosProveedores->bindParam(':fechaFin', $fechaFin);
            $stmtPagosProveedores->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
            $stmtPagosProveedores->execute();
            $pagosProveedores = $stmtPagosProveedores->fetchAll(PDO::FETCH_ASSOC);

            // Calcular efectivo esperado
            $resumen['efectivo_esperado'] = $resumen['ventas_efectivo'] + $resumen['cobros_hoy'] - $resumen['pagos_hoy'] - $resumen['compras_contado'];

            // Agregar bandera para indicar si ya hubo un corte hoy
            $resumen['ya_hubo_corte_hoy'] = ($ultimoCorteHoy && $ultimoCorteHoy['fecha_fin_corte']) ? true : false;

            echo json_encode([
                'success' => true,
                'data' => $resumen,
                'pagos_clientes' => $pagosClientes,
                'pagos_proveedores' => $pagosProveedores,
                'periodo' => [
                    'inicio' => $fechaInicio,
                    'fin' => $fechaFin
                ],
                'ya_hubo_corte_hoy' => $resumen['ya_hubo_corte_hoy']
            ]);
            break;

        case 'realizar_corte':
            $efectivoReal = filter_input(INPUT_POST, 'efectivo_real', FILTER_VALIDATE_FLOAT);
            $observaciones = filter_input(INPUT_POST, 'observaciones', FILTER_SANITIZE_STRING);

            if ($efectivoReal === false || $efectivoReal === null) {
                throw new Exception('El monto de efectivo real no es válido');
            }

            // Obtener el resumen para calcular el efectivo esperado
            $sqlResumen = "SELECT 
                    ((SELECT COALESCE(SUM(total), 0) FROM ventas 
                    WHERE fecha >= :fechaInicio AND fecha <= :fechaFin
                    AND idempleados = :usuario_id
                    AND tipo_pago = 'contado' AND fechacancelada IS NULL) +

                    (SELECT COALESCE(SUM(pc.monto_pago), 0) FROM pagos_cuentas pc
                    JOIN cuentas_por_cobrar cc ON pc.id_cuenta_cobrar = cc.id_cuenta_cobrar
                    WHERE pc.fecha_pago >= :fechaInicio AND pc.fecha_pago <= :fechaFin
                    AND pc.id_usuario = :usuario_id
                    AND cc.estado != 'pendiente') -

                    (SELECT COALESCE(SUM(pp.monto_pago), 0) FROM pagos_proveedores pp
                    JOIN cuentas_por_pagar cp ON pp.id_cuenta_pagar = cp.id_cuenta_pagar
                    JOIN compras c ON cp.id_compra = c.id_compra
                    WHERE pp.fecha_pago >= :fechaInicio AND pp.fecha_pago <= :fechaFin
                    AND c.idempleados = :usuario_id
                    AND cp.estado != 'pendiente') -

                    (SELECT COALESCE(SUM(total), 0) FROM compras 
                    WHERE fecha >= :fechaInicio AND fecha <= :fechaFin
                    AND idempleados = :usuario_id
                    AND fechaeliminada IS NULL AND (tipo_pago = 'contado' OR tipo_pago IS NULL))) AS efectivo_esperado";

            $stmt = $consulta->prepare($sqlResumen);
            $stmt->bindParam(':fechaInicio', $fechaInicio);
            $stmt->bindParam(':fechaFin', $fechaFin);
            $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
            $stmt->execute();
            $resumen = $stmt->fetch(PDO::FETCH_ASSOC);
            $efectivoEsperado = $resumen['efectivo_esperado'] ?? 0;

            $diferencia = $efectivoReal - $efectivoEsperado;

            // Insertar el registro del corte
            $sqlInsert = "INSERT INTO cortes_caja (
                fecha, 
                id_usuario,
                efectivo_esperado, 
                efectivo_real, 
                diferencia, 
                observaciones,
                fecha_inicio_corte,
                fecha_fin_corte
            ) VALUES (
                NOW(), 
                :usuario_id, 
                :efectivo_esperado, 
                :efectivo_real, 
                :diferencia, 
                :observaciones,
                :fecha_inicio,
                :fecha_fin
            )";

            $stmt = $consulta->prepare($sqlInsert);
            $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado'], PDO::PARAM_INT);
            $stmt->bindParam(':efectivo_esperado', $efectivoEsperado, PDO::PARAM_STR);
            $stmt->bindParam(':efectivo_real', $efectivoReal, PDO::PARAM_STR);
            $stmt->bindParam(':diferencia', $diferencia, PDO::PARAM_STR);
            $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);

            if ($stmt->execute()) {
                // Obtener el nuevo período (que comenzará desde este momento)
                $fechaInicioNueva = $fechaFin;
                $fechaFinNueva = date('Y-m-d H:i:s');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Corte de caja registrado correctamente',
                    'data' => [
                        'efectivo_esperado' => '0.00', // Se reinicia
                        'efectivo_real' => number_format($efectivoReal, 2),
                        'diferencia' => number_format($diferencia, 2),
                        'periodo' => [
                            'inicio' => $fechaInicioNueva,
                            'fin' => $fechaFinNueva
                        ],
                        'ya_hubo_corte_hoy' => true
                    ]
                ]);
            } else {
                throw new Exception('Error al registrar el corte: ' . implode(', ', $stmt->errorInfo()));
            }
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (PDOException $e) {
    error_log("Error PDO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'error_info' => isset($consulta) ? $consulta->errorInfo() : null
    ]);
} catch (Exception $e) {
    error_log("Error General: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>