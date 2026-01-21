<?php
session_start();
include("../inc/conectar.php");

// Función para verificar conexión a la base de datos
function verificarConexion($conexion)
{
    if (!$conexion) {
        throw new Exception("No hay conexión a la base de datos");
    }
}

// Función para obtener el folio de compra
function FolioCompra($consulta)
{
    $Auto = $consulta->query("SELECT MAX(id_compra)+1 AS autoincrement FROM compras");
    $row = $Auto->fetch(PDO::FETCH_ASSOC);
    return ($row['autoincrement'] == "") ? 1 : $row['autoincrement'];
}

try {
    verificarConexion($consulta);

    if (!isset($_REQUEST['funcion'])) {
        throw new Exception("Función no especificada");
    }

    switch ($_REQUEST['funcion']) {
        case "getFolio":
            echo str_pad(FolioCompra($consulta), 4, "0", STR_PAD_LEFT);
            break;

        case "buscarProveedor":
            $query = "%" . $_POST['query'] . "%";
            $stmt = $consulta->prepare("SELECT id_proveedor, nombre, telefono AS contacto, direccion, correo_electronico 
                        FROM proveedores 
                        WHERE nombre LIKE ? OR telefono LIKE ? 
                        LIMIT 10");
            $stmt->execute([$query, $query]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($resultados);
            break;

        case "buscarProducto":
            $query = "%" . $_POST['query'] . "%";
            $stmt = $consulta->prepare("SELECT id_productos AS id_productos, codigo_barras, nombre, precio, 
                       precio_venta, existencias, id_unidad_medida, id_categoria
                       FROM productos 
                       WHERE (codigo_barras LIKE ? OR nombre LIKE ?) 
                       AND fechabaja IS NULL
                       LIMIT 10");
            $stmt->execute([$query, $query]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($resultados);
            break;

        case "obtenerComprasDia":
            $fechaHoy = date('Y-m-d');
            $stmt = $consulta->prepare("
                    SELECT c.id_compra, c.folio, p.nombre AS proveedor, 
                           DATE_FORMAT(c.fecha, '%Y-%m-%d %H:%i:%s') AS fecha, 
                           c.total, c.tipo_pago
                    FROM compras c
                    JOIN proveedores p ON c.idproveedores = p.id_proveedor
                    WHERE DATE(c.fecha) = ? AND c.fechaeliminada IS NULL
                    ORDER BY c.fecha DESC 
                ");
            $stmt->execute([$fechaHoy]);
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($compras);
            break;

        case 'eliminarCompra':
            if (isset($_POST['id_compra'])) {
                $id_compra = $_POST['id_compra'];

                try {
                    // Iniciar transacción
                    $consulta->beginTransaction();

                    // 1. Marcar la compra como eliminada
                    $sql = "UPDATE compras SET fechaeliminada = NOW() WHERE id_compra = ?";
                    $stmt = $consulta->prepare($sql);
                    $stmt->execute([$id_compra]);

                    // Confirmar transacción
                    $consulta->commit();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Compra cancelada correctamente'
                    ]);
                } catch (PDOException $e) {
                    // Revertir transacción en caso de error
                    $consulta->rollBack();

                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al cancelar la compra: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de compra no proporcionado'
                ]);
            }
            break;

        case "Guardar_Compra":
            // Iniciar transacción
            $consulta->beginTransaction();

            // Generar folio
            $folio = str_pad(FolioCompra($consulta), 4, "0", STR_PAD_LEFT);
            $tipo_pago = $_POST['tipo_pago'];

            // Insertar compra principal
            $stmtCompra = $consulta->prepare("INSERT INTO compras 
                (fecha, total, folio, idproveedores, idempleados, empleado, tipo_pago) 
                VALUES (NOW(), ?, ?, ?, ?, ?, ?)");
            $stmtCompra->execute([
                $_POST['total'],
                $folio,
                $_POST['idproveedores'],
                $_SESSION['SISTEMA']['id_empleado'],
                $_SESSION['SISTEMA']['nombre'],
                $tipo_pago
            ]);

            $id_compra = $consulta->lastInsertId();

            // Insertar detalles de compra
            $stmtDetalle = $consulta->prepare("INSERT INTO compradetalle 
                (id_compra, id_producto, cantidad, precio_unitario, precio_venta, importe, 
                precio_original, existencias_original) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($_POST["Detalle"] as $val) {
                $importe = $val['cantidad'] * $val['precio_unitario'];
                
                // Obtener precio_venta actual y existencias del producto
                $stmtProducto = $consulta->prepare("SELECT precio_venta, existencias FROM productos WHERE id_productos = ?");
                $stmtProducto->execute([$val['id_productos']]);
                $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);
                
                $stmtDetalle->execute([
                    $id_compra,
                    $val['id_productos'],
                    $val['cantidad'],
                    $val['precio_unitario'],
                    $val['precio_venta'],
                    $importe,
                    $producto['precio_venta'], // Precio original (precio_venta anterior)
                    $producto['existencias']   // Existencias originales
                ]);
            }

            // Proceso especial para crédito
            if ($tipo_pago == 'credito') {
                $dias_credito = intval($_POST['dias_credito'] ?? 30);
                $fecha_vencimiento = date('Y-m-d', strtotime("+$dias_credito days"));

                $stmtCredito = $consulta->prepare("INSERT INTO cuentas_por_pagar
                    (id_proveedor, id_compra, fecha_vencimiento, dias_credito, 
                    monto_total, saldo_pendiente, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?, 'pendiente', ?)");

                $stmtCredito->execute([
                    $_POST['idproveedores'],
                    $id_compra,
                    $fecha_vencimiento,
                    $dias_credito,
                    $_POST['total'],
                    $_POST['total'],
                    'Compra a crédito folio ' . $folio
                ]);
            }

            // Confirmar transacción
            $consulta->commit();

            echo json_encode([
                'success' => true,
                'folio' => $folio,
                'message' => 'Compra registrada correctamente'
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
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
    error_log("PDO Error: " . $e->getMessage());

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Error: " . $e->getMessage());
}
?>