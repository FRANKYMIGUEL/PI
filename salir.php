<?php
session_start();
include("inc/conectar.php");

try {
    // Obtener la fecha del último corte COMPLETO de CUALQUIER usuario
    $sqlUltimoCorte = "SELECT fecha_fin_corte FROM cortes_caja 
                      WHERE fecha_fin_corte IS NOT NULL
                      ORDER BY fecha_fin_corte DESC LIMIT 1";
    
    $stmt = $consulta->prepare($sqlUltimoCorte);
    $stmt->execute();
    $ultimoCorte = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $fechaUltimoCorte = $ultimoCorte['fecha_fin_corte'] ?? null;

    // Verificar si hay ventas del USUARIO ACTUAL después del último corte
    $sqlVentasPosteriores = "SELECT COUNT(*) as ventas_posteriores FROM ventas 
                            WHERE idempleados = :usuario_id 
                            AND fecha > :fecha_ultimo_corte
                            AND fechacancelada IS NULL";
    
    $stmt = $consulta->prepare($sqlVentasPosteriores);
    $stmt->bindParam(':usuario_id', $_SESSION['SISTEMA']['id_empleado']);
    $stmt->bindParam(':fecha_ultimo_corte', $fechaUltimoCorte);
    $stmt->execute();
    $resultVentas = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultVentas['ventas_posteriores'] > 0) {
        $_SESSION['corte_pendiente'] = true;
        header("Location: ventas.php?error=corte_pendiente");
        exit;
    }

    session_destroy();
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    error_log("Error al verificar corte: " . $e->getMessage());
    session_destroy();
    header("Location: login.php");
    exit;
}
?>