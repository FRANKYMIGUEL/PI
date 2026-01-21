<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css">

    <style>
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
            font-size: 0.9em;
            box-shadow: 0 0 10px rgba(41, 115, 178, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background-color: #F2EFE7;
        }

        .table thead tr {
            background-color: #2973B2;
            color: white;
            text-align: left;
            font-weight: bold;
        }

        .bt_custo {
            background-color: #2973B2;
            color: white;
            border: none;
            padding: 8px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #9ACBD0;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:nth-of-type(even) {
            background-color: rgba(154, 203, 208, 0.1);
        }

        .table tbody tr:last-of-type {
            border-bottom: 2px solid #48A6A7;
        }

        .table tbody tr:hover {
            background-color: rgba(154, 203, 208, 0.3);
        }

        body {
            background-color: #f0f8ff;
            background: linear-gradient(135deg, #F2EFE7 0%, #F2EFE7 50%, #F2EFE7 100%);
        }

        .banner {
            background-color: #2973B2;
            color: white;
            padding: 5px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(41, 115, 178, 0.1);
            background-color: #F2EFE7;
        }

        .card-header {
            background-color: #2973B2;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            background-color: #F2EFE7;
            border: 1px solid #9ACBD0;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .dataTables_wrapper .dataTables_filter label {
            font-weight: bold;
        }
    </style>
</head>


<?php
session_start();
require_once 'check_session.php';
include("inc/conectar.php");

$id_cuenta = $_GET['id'] ?? 0;

// Obtener información de la cuenta por pagar
$stmt = $consulta->prepare("SELECT cp.*, 
       p.nombre AS proveedor_nombre, 
       c.folio AS compra_folio,
       c.total AS monto_total
FROM cuentas_por_pagar cp
JOIN proveedores p ON cp.id_proveedor = p.id_proveedor
JOIN compras c ON cp.id_compra = c.id_compra
WHERE cp.id_cuenta_pagar = ?");
$stmt->execute([$id_cuenta]);
$cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener historial de pagos
$stmt = $consulta->prepare("SELECT * FROM pagos_proveedores 
                           WHERE id_cuenta_pagar = ? 
                           ORDER BY fecha_pago DESC");
$stmt->execute([$id_cuenta]);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <div class="banner">
        <div class="container">
            <h1 class="text-center">Historial de Pagos a Proveedores</h1>
        </div>
    </div>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Detalle de Pagos</h4>
                <a href="cuentas_pagar.php" class="btn btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Proveedor:</strong> <?= htmlspecialchars($cuenta['proveedor_nombre']) ?></p>
                        <p><strong>Compra:</strong> <?= $cuenta['compra_folio'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Monto Total:</strong> $<?= number_format($cuenta['monto_total'], 2) ?></p>
                        <p><strong>Saldo Pendiente:</strong> $<?= number_format($cuenta['saldo_pendiente'], 2) ?></p>
                    </div>
                </div>

                <h5>Pagos Registrados</h5>
                <div class="table-responsive">
                    <table id="tablaPagos" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $pago):
                                // Obtener nombre de usuario que registró el pago
                                $stmt = $consulta->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ?");
                                $stmt->execute([$pago['id_usuario']]);
                                $usuario = $stmt->fetchColumn();
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                    <td>$<?= number_format($pago['monto_pago'], 2) ?></td>
                                    <td><?= ucfirst($pago['metodo_pago']) ?></td>
                                    <td><?= htmlspecialchars($pago['referencia'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($usuario) ?></td>
                                    <td><?= htmlspecialchars($pago['observaciones'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tablaPagos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json'
                },
                order: [[0, 'desc']] // Ordenar por fecha descendente por defecto
            });
        });
    </script>
</body>

</html>