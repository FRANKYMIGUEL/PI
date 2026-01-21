<?php
include("../inc/conectar.php");

if ($_POST['funcion'] == "Agregar") {
    include("../inc/conectar.php");
    $Auto = $consulta->query("SELECT * FROM productos WHERE codigo_barras LIKE '" . $_POST['codigo_barras'] . "'");
    $producto = $Auto->fetch(PDO::FETCH_ASSOC);

    $tabla = "<tr>
            <td><input type='number' class='cantidad' value='" . $_POST['cantidad'] . "'></td>
            <td id_productos='" . $producto['id_productos'] . "'>" . $_POST['codigo_barras'] . "</td>
            <td>" . $producto['nombre'] . "</td>
            <td>" . number_format($producto['precio'], 2) . "</td>
            <td>" . number_format(($producto['precio'] * $_POST['cantidad']), 2) . "</td>
            <td><button class='btn btn-danger eliminar' idregistros='" . $producto['id_productos'] . "' style='background-color:#cb2626; color: white; border: none;'>Eliminar</button></td>
        </tr>";
    echo $tabla;
    exit();
}

function FolioVenta()
{
    include("../inc/conectar.php");
    $Auto = $consulta->query("SELECT MAX(id_venta)+1 AS autoincrement FROM ventas");
    foreach ($Auto as $row)
        ;

    if ($row['autoincrement'] == "") {
        $folio = 1;
    } else {
        $folio = $row['autoincrement'];
    }
    return $folio;
}

if ($_POST['funcion'] == "Guardar_Venta") {
    file_put_contents('debug_venta.log', print_r($_POST, true), FILE_APPEND);

    try {
        // Validación básica de datos
        if (empty($_POST['Detalle']) || !is_array($_POST['Detalle'])) {
            throw new Exception("No hay productos en la venta");
        }

        // Validación especial para crédito
        if ($_POST['tipo_pago'] == 'credito' && $_POST['idclientes'] == 1) {
            throw new Exception("No se puede registrar crédito para ventas de mostrador");
        }

        // Iniciar transacción
        $consulta->beginTransaction();

        // Generar folio
        $folio = str_pad(FolioVenta(), 4, "0", STR_PAD_LEFT);
        $tipo_pago = $_POST['tipo_pago'];

        // Insertar venta principal (usando consultas preparadas)
        $stmtVenta = $consulta->prepare("INSERT INTO ventas 
                                      (fecha, total, folio, idclientes, idempleados, efectivo, tipo_pago) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtVenta->execute([
            date('Y-m-d H:i:s'),
            $_POST['total'],
            $folio,
            $_POST['idclientes'],
            $_SESSION['SISTEMA']['idempleados'],
            $_POST['efectivo'],
            $tipo_pago
        ]);

        $id_venta = $consulta->lastInsertId();

        // Insertar detalles de la venta
        $stmtDetalle = $consulta->prepare("INSERT INTO ventasdetalle 
                                        (id_venta, id_productos, cantidad, precio) 
                                        VALUES (?, ?, ?, ?)");

        foreach ($_POST["Detalle"] as $val) {
            $stmtDetalle->execute([
                $id_venta,
                $val['id_productos'],
                $val['cantidad'],
                $val['precio']
            ]);
        }

        // Proceso especial para crédito
        if ($tipo_pago == 'credito') {
            $dias_credito = intval($_POST['dias_credito'] ?? 30);
            $fecha_vencimiento = date('Y-m-d', strtotime("+$dias_credito days"));

            // Validar días de crédito
            if ($dias_credito <= 0) {
                throw new Exception("Días de crédito no válidos");
            }

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

    } catch (Exception $e) {
        // Revertir en caso de error
        if ($consulta->inTransaction()) {
            $consulta->rollBack();
        }

        http_response_code(400);
        echo "Error: " . $e->getMessage();
        error_log("Error en venta: " . $e->getMessage());
    }
    exit();
}

if ($_POST['funcion'] == "ObtenerVentasDelDia") {
    header('Content-Type: application/json');

    try {
        if (!$consulta) {
            throw new Exception("No hay conexión a la base de datos");
        }

        $fecha_actual = date('Y-m-d');

        // Consulta simplificada para obtener solo el total
        $query = "SELECT SUM(v.total) as total_dia
                  FROM ventas v
                  WHERE DATE_FORMAT(v.fecha, '%Y-%m-%d') = ?";

        $stmt = $consulta->prepare($query);
        $stmt->execute([$fecha_actual]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $total_dia = $resultado['total_dia'] ?? 0;

        $response = [
            'success' => true,
            'total_dia' => number_format($total_dia, 2),
            'fecha' => $fecha_actual
        ];

        echo json_encode($response);

    } catch (PDOException $e) {
        error_log("PDO Error en ObtenerVentasDelDia: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => 'Error de base de datos',
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
?>