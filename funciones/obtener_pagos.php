<?php
include("../inc/conectar.php");
header('Content-Type: application/json');

try {
    $idCuenta = $_GET['id_cuenta_pagar'] ?? null;
    if (!$idCuenta) throw new Exception("ID de cuenta no proporcionado");

    // Obtener información de la cuenta
    $sqlCuenta = "SELECT cp.monto_total, cp.saldo_pendiente, 
                 p.nombre AS proveedor_nombre, c.folio AS compra_folio
                 FROM cuentas_por_pagar cp
                 JOIN proveedores p ON cp.id_proveedor = p.id_proveedor
                 JOIN compras c ON cp.id_compra = c.id_compra
                 WHERE cp.id_cuenta_pagar = :id_cuenta";
    
    $stmt = $consulta->prepare($sqlCuenta);
    $stmt->bindParam(':id_cuenta', $idCuenta, PDO::PARAM_INT);
    $stmt->execute();
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener todos los pagos
    $sqlPagos = "SELECT id_pago, monto_pago, metodo_pago, referencia, 
                observaciones, fecha_pago
                FROM pagos_proveedores
                WHERE id_cuenta_pagar = :id_cuenta
                ORDER BY fecha_pago ASC";
    
    $stmt = $consulta->prepare($sqlPagos);
    $stmt->bindParam(':id_cuenta', $idCuenta, PDO::PARAM_INT);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($cuenta && $pagos) {
        echo json_encode([
            'success' => true,
            'cuenta' => $cuenta,
            'pagos' => $pagos,
            'total_pagado' => array_sum(array_column($pagos, 'monto_pago'))
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>