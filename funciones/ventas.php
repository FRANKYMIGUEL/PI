<?php
session_start();
include("../inc/conectar.php");
// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');
// Función para verificar conexión a la base de datos
function verificarConexion($conexion)
{
    if (!$conexion) {
        throw new Exception("No hay conexión a la base de datos");
    }
}

// Función para obtener el folio de venta
function FolioVenta($consulta)
{
    $Auto = $consulta->query("SELECT MAX(id_venta)+1 AS autoincrement FROM ventas");
    $row = $Auto->fetch(PDO::FETCH_ASSOC);
    return ($row['autoincrement'] == "") ? 1 : $row['autoincrement'];
}

// Función para verificar existencias de un producto
function verificarExistencias($consulta, $codigo_barras)
{
    $stmt = $consulta->prepare("SELECT id_productos, nombre, existencias, precio_venta FROM productos WHERE codigo_barras = ?");
    $stmt->execute([$codigo_barras]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para validar los datos de entrada
function validarDatosEntrada($data)
{
    if (empty($data['funcion'])) {
        throw new Exception("Función no especificada");
    }

    // Validaciones específicas para cada función
    switch ($data['funcion']) {
        case 'Agregar':
            if (empty($data['codigo_barras']) || empty($data['cantidad'])) {
                throw new Exception("Datos incompletos para agregar producto");
            }
            break;

        case 'Guardar_Venta':
            if (empty($data['Detalle']) || !is_array($data['Detalle'])) {
                throw new Exception("No hay productos en la venta");
            }
            break;
    }
}

// Procesamiento principal
try {
    verificarConexion($consulta);

    if (!isset($_POST['funcion'])) {
        throw new Exception("Función no especificada");
    }

    validarDatosEntrada($_POST);

    switch ($_POST['funcion']) {
        case "Agregar":
            $producto = verificarExistencias($consulta, $_POST['codigo_barras']);

            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }

            // Validar existencias
            if ($producto['existencias'] < $_POST['cantidad']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => true,
                    'message' => 'No hay suficiente existencia para este producto.',
                    'existencias' => $producto['existencias'],
                    'disponible' => $producto['existencias']
                ]);
                exit();
            }

            // Respuesta cuando hay existencias
            header('Content-Type: application/json');
            echo json_encode([
                'error' => false,
                'html' => "<tr>
                    <td><input type='number' class='cantidad' value='" . $_POST['cantidad'] . "' min='1' max='" . $producto['existencias'] . "'></td>
                    <td id_productos='" . $producto['id_productos'] . "'>" . $_POST['codigo_barras'] . "</td>
                    <td>" . $producto['nombre'] . "</td>
                    <td>" . number_format($producto['precio_venta'], 2) . "</td>
                    <td>" . number_format(($producto['precio_venta'] * $_POST['cantidad']), 2) . "</td>
                    <td><button class='btn btn-danger eliminar' idregistros='" . $producto['id_productos'] . "' style='background-color:#cb2626; color: white; border: none;'>Eliminar</button></td>
                </tr>",
                'id_productos' => $producto['id_productos'],
                'precio_venta' => $producto['precio_venta']
            ]);
            break;

        case "VerificarExistencias":
            $producto = verificarExistencias($consulta, $_POST['codigo_barras']);

            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }

            header('Content-Type: application/json');
            echo json_encode([
                'existencias' => $producto['existencias'],
                'id_productos' => $producto['id_productos'],
                'precio_venta' => $producto['precio_venta']
            ]);
            break;

        case "Guardar_Venta":
            // Validación especial para crédito
            if ($_POST['tipo_pago'] == 'credito' && $_POST['idclientes'] == 1) {
                throw new Exception("No se puede registrar crédito para ventas de mostrador");
            }

            // Iniciar transacción
            $consulta->beginTransaction();

            // Generar folio
            $folio = str_pad(FolioVenta($consulta), 4, "0", STR_PAD_LEFT);
            $tipo_pago = $_POST['tipo_pago'];

            // Insertar venta principal
            $stmtVenta = $consulta->prepare("INSERT INTO ventas 
                (fecha, total, folio, idclientes, idempleados, empleado, efectivo, tipo_pago) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtVenta->execute([
                date('Y-m-d H:i:s'),
                $_POST['total'],
                $folio,
                $_POST['idclientes'],
                $_SESSION['SISTEMA']['id_empleado'],
                $_SESSION['SISTEMA']['nombre'],
                $_POST['efectivo'],
                $tipo_pago
            ]);

            $id_venta = $consulta->lastInsertId();

            // Insertar detalles de la venta y actualizar existencias
            $stmtDetalle = $consulta->prepare("INSERT INTO ventasdetalle 
                (id_venta, id_productos, cantidad, precio) 
                VALUES (?, ?, ?, ?)");

            // Preparar consulta para actualizar existencias
            $stmtUpdateExistencias = $consulta->prepare("UPDATE productos 
                SET existencias = existencias - ? 
                WHERE id_productos = ?");

            foreach ($_POST["Detalle"] as $val) {
                // Validar que haya suficiente existencia
                $producto = verificarExistencias($consulta, $val['codigo_barras']);

                if (!$producto) {
                    throw new Exception("Producto no encontrado: " . $val['codigo_barras']);
                }

                if ($producto['existencias'] < $val['cantidad']) {
                    throw new Exception("No hay suficiente existencia para el producto: " . $producto['nombre'] .
                        ". Disponible: " . $producto['existencias'] . ", Solicitado: " . $val['cantidad']);
                }

                // Insertar detalle de venta
                $stmtDetalle->execute([
                    $id_venta,
                    $val['id_productos'],
                    $val['cantidad'],
                    $val['precio']
                ]);

                // Actualizar existencias
                $stmtUpdateExistencias->execute([
                    $val['cantidad'],
                    $val['id_productos']
                ]);
            }

            // Proceso especial para crédito
            if ($tipo_pago == 'credito') {
                $dias_credito = intval($_POST['dias_credito'] ?? 30);

                // Validar días de crédito
                if ($dias_credito <= 0) {
                    throw new Exception("Días de crédito no válidos");
                }

                $fecha_vencimiento = date('Y-m-d', strtotime("+$dias_credito days"));

                $stmtCredito = $consulta->prepare("INSERT INTO cuentas_por_cobrar 
                    (id_venta, id_cliente, monto_total, saldo_pendiente, 
                    fecha_vencimiento, dias_credito, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?, 'pendiente', ?)");
                $stmtCredito->execute([
                    $id_venta,
                    $_POST['idclientes'],
                    $_POST['total'],
                    $_POST['total'], // Saldo inicial = monto total
                    $fecha_vencimiento,
                    $dias_credito,
                    'Venta a crédito folio ' . $folio
                ]);
            }

            // Confirmar transacción
            $consulta->commit();

            echo str_pad($folio, 4, "0", STR_PAD_LEFT);
            break;

        case "ObtenerVentasDelDia":
            $fecha_actual = date('Y-m-d');

            $query = "SELECT SUM(v.total) as total_dia
                      FROM ventas v
                      WHERE DATE_FORMAT(v.fecha, '%Y-%m-%d') = ?";

            $stmt = $consulta->prepare($query);
            $stmt->execute([$fecha_actual]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            $total_dia = $resultado['total_dia'] ?? 0;

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total_dia' => number_format($total_dia, 2),
                'fecha' => $fecha_actual
            ]);
            break;

        default:
            throw new Exception("Función no reconocida");
    }

} catch (PDOException $e) {
    // Revertir transacción si está activa
    if (isset($consulta) && $consulta->inTransaction()) {
        $consulta->rollBack();
    }

    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
    error_log("PDO Error: " . $e->getMessage());

} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    error_log("Error: " . $e->getMessage());
}
?>