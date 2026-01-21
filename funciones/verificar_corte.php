<?php
include("../inc/conectar.php");
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['SISTEMA']['id_empleado'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

try {
    // Obtener la fecha del último corte COMPLETO
    $sqlUltimoCorte = "SELECT fecha_fin_corte FROM cortes_caja 
                      WHERE id_usuario = :usuario_id 
                      AND fecha_fin_corte IS NOT NULL
                      ORDER BY fecha_fin_corte DESC LIMIT 1";
    
    $stmt = $consulta->prepare($sqlUltimoCorte);
    $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
    $stmt->execute();
    $ultimoCorte = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $fechaUltimoCorte = $ultimoCorte['fecha_fin_corte'] ?? null;

    // Verificar si hay ventas después del último corte
    $sqlVentasPosteriores = "SELECT COUNT(*) as ventas_posteriores FROM ventas 
                            WHERE id_usuario = :usuario_id 
                            AND fecha > :fecha_ultimo_corte
                            AND fechacancelada IS NULL";
    
    $stmt = $consulta->prepare($sqlVentasPosteriores);
    $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
    $stmt->bindParam(':fecha_ultimo_corte', $fechaUltimoCorte);
    $stmt->execute();
    $resultVentas = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si hay ventas posteriores al último corte, hay corte pendiente
    $corte_pendiente = ($resultVentas['ventas_posteriores'] > 0);

    echo json_encode([
        'success' => true,
        'corte_pendiente' => $corte_pendiente,
        'message' => $corte_pendiente ? 'Hay ventas sin corte' : 'No hay ventas sin corte',
        'ultimo_corte' => $fechaUltimoCorte,
        'ventas_posteriores' => $resultVentas['ventas_posteriores']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar corte',
        'error' => $e->getMessage()
    ]);
}
?>