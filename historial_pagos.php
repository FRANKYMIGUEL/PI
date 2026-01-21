<?php
include("inc/conectar.php");

$id_cuenta = $_GET['id'] ?? 0;

// Obtener información de la cuenta
$stmt = $consulta->prepare("SELECT cc.*, 
       CONCAT(c.nombre, ' ', c.apellido_p, ' ', c.apellido_m) AS cliente_nombre, 
       v.folio AS venta_folio
FROM cuentas_por_cobrar cc
JOIN clientes c ON cc.id_cliente = c.id_cliente
JOIN ventas v ON cc.id_venta = v.id_venta
WHERE cc.id_cuenta_cobrar = ?");
$stmt->execute([$id_cuenta]);
$cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener historial de pagos
$stmt = $consulta->prepare("SELECT * FROM pagos_cuentas 
                           WHERE id_cuenta_cobrar = ? 
                           ORDER BY fecha_pago DESC");
$stmt->execute([$id_cuenta]);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?
    include "menu.php";
    ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Historial de Pagos</h4>
                <a href="cuentas_cobrar.php" class="btn btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <?= htmlspecialchars($cuenta['cliente_nombre']) ?></p>
                        <p><strong>Venta:</strong> <?= $cuenta['venta_folio'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Monto Total:</strong> $<?= number_format($cuenta['monto_total'], 2) ?></p>
                        <p><strong>Saldo Pendiente:</strong> $<?= number_format($cuenta['saldo_pendiente'], 2) ?></p>
                    </div>
                </div>

                <h5>Pagos Registrados</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Referencia</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $pago): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                    <td>$<?= number_format($pago['monto_pago'], 2) ?></td>
                                    <td><?= ucfirst($pago['metodo_pago']) ?></td>
                                    <td><?= htmlspecialchars($pago['referencia'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($pago['observaciones'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>