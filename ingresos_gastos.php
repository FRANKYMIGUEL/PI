<?php
session_start();
require_once 'check_session.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("inc/conectar.php");

function getGananciasReales($conn, $fechaInicio, $fechaFin)
{
    $sql = "SELECT 
                DATE(v.fecha) as dia,
                vd.id_productos,
                p.nombre as producto,
                SUM(vd.cantidad) as unidades_vendidas,
                SUM(vd.precio * vd.cantidad) as total_ventas,
                SUM(vd.cantidad * p.precio) as total_costo,
                SUM((vd.precio * vd.cantidad) - (vd.cantidad * p.precio)) as ganancia
            FROM 
                ventasdetalle vd
            JOIN 
                ventas v ON vd.id_venta = v.id_venta
            JOIN 
                productos p ON vd.id_productos = p.id_productos
            WHERE 
                v.fecha BETWEEN ? AND ?
                AND v.fechacancelada IS NULL
            GROUP BY 
                DATE(v.fecha), vd.id_productos
            ORDER BY 
                DATE(v.fecha), vd.id_productos";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para resumir ganancias por día
function getResumenGananciasDiarias($datosDetallados)
{
    $resumen = [];
    foreach ($datosDetallados as $fila) {
        $dia = $fila['dia'];
        if (!isset($resumen[$dia])) {
            $resumen[$dia] = [
                'dia' => $dia,
                'total_ventas' => 0,
                'total_costo' => 0,
                'ganancia' => 0,
                'productos' => []
            ];
        }
        $resumen[$dia]['total_ventas'] += $fila['total_ventas'];
        $resumen[$dia]['total_costo'] += $fila['total_costo'];
        $resumen[$dia]['ganancia'] += $fila['ganancia'];
        $resumen[$dia]['productos'][] = $fila;
    }
    return array_values($resumen);
}

// Obtener fechas por defecto (últimos 30 días)
$fechaFin = date('Y-m-d');
$fechaInicio = date('Y-m-d', strtotime('-30 days'));

// Procesar filtros si se enviaron
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fechaInicio = $_POST['fecha_inicio'] ?? $fechaInicio;
    $fechaFin = $_POST['fecha_fin'] ?? $fechaFin;
}

// Obtener datos
$gananciasDetalladas = getGananciasReales($consulta, $fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59');
$resumenDiario = getResumenGananciasDiarias($gananciasDetalladas);

// Calcular totales
$totalVentas = array_sum(array_column($resumenDiario, 'total_ventas'));
$totalCosto = array_sum(array_column($resumenDiario, 'total_costo'));
$totalGanancias = $totalVentas - $totalCosto;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ganancias por Producto</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #2973B2;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .form-container {
            background-color: #F2EFE7;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            background-color: #f8f9fa;
            border-left: 5px solid;
            padding: 15px;
            margin-bottom: 15px;
        }

        .summary-income {
            border-left-color: #28a745;
        }

        .summary-expense {
            border-left-color: #dc3545;
        }

        .summary-profit {
            border-left-color: #ffc107;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .product-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .product-row {
            border-bottom: 1px solid #dee2e6;
            padding: 8px 0;
        }

        .badge-sold {
            background-color: #17a2b8;
        }

        .badge-profit {
            background-color: #28a745;
        }
    </style>
</head>

<body>
    <?php 
    include("menu.php"); 
    ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Filtros -->
            <div class="col-md-12">
                <div class="form-container">
                    <form method="POST" class="row">
                        <div class="col-md-3">
                            <label class="form-label"><b>Fecha Inicio</b></label>
                            <input type="date" class="form-control" name="fecha_inicio" value="<?= $fechaInicio ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Fecha Fin</b></label>
                            <input type="date" class="form-control" name="fecha_fin" value="<?= $fechaFin ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumen -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="summary-card summary-income">
                            <h5>Ventas Totales</h5>
                            <h3>$<?= number_format($totalVentas, 2) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card summary-expense">
                            <h5>Costo Total</h5>
                            <h3>$<?= number_format($totalCosto, 2) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card summary-profit">
                            <h5>Ganancias Reales</h5>
                            <h3>$<?= number_format($totalGanancias, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de ganancias -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Ganancias Diarias</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="gananciasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle por día -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Detalle de Ganancias por Día</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($resumenDiario as $dia): ?>
                            <div class="product-details mb-4">
                                <h5 class="d-flex justify-content-between align-items-center">
                                    <span><?= $dia['dia'] ?></span>
                                    <div>
                                        <span class="badge bg-primary">Ventas:
                                            $<?= number_format($dia['total_ventas'], 2) ?></span>
                                        <span class="badge bg-secondary">Costo:
                                            $<?= number_format($dia['total_costo'], 2) ?></span>
                                        <span class="badge <?= $dia['ganancia'] >= 0 ? 'bg-success' : 'bg-danger' ?>">
                                            Ganancia: $<?= number_format($dia['ganancia'], 2) ?>
                                        </span>
                                    </div>
                                </h5>

                                <?php foreach ($dia['productos'] as $producto): ?>
                                    <div class="product-row row">
                                        <div class="col-md-4">
                                            <strong><?= $producto['producto'] ?></strong>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge badge-sold"><?= $producto['unidades_vendidas'] ?> unid.</span>
                                        </div>
                                        <div class="col-md-2">
                                            <span>Venta: $<?= number_format($producto['total_ventas'], 2) ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <span>Costo: $<?= number_format($producto['total_costo'], 2) ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <span
                                                class="badge <?= $producto['ganancia'] >= 0 ? 'badge-profit' : 'bg-danger' ?>">
                                                Ganancia: $<?= number_format($producto['ganancia'], 2) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Preparar datos para la gráfica
            const resumenDiario = <?= json_encode($resumenDiario) ?>;

            const labels = resumenDiario.map(item => item.dia);
            const ganancias = resumenDiario.map(item => item.ganancia);

            // Configuración de la gráfica
            new Chart(document.getElementById('gananciasChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ganancias por día',
                        data: ganancias,
                        backgroundColor: ganancias.map(value =>
                            value >= 0 ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)'
                        ),
                        borderColor: ganancias.map(value =>
                            value >= 0 ? 'rgba(40, 167, 69, 1)' : 'rgba(220, 53, 69, 1)'
                        ),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function (value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const dia = resumenDiario[context.dataIndex];
                                    return [
                                        `Ganancia: $${dia.ganancia.toLocaleString(undefined, { minimumFractionDigits: 2 })}`,
                                        `Ventas: $${dia.total_ventas.toLocaleString(undefined, { minimumFractionDigits: 2 })}`,
                                        `Costo: $${dia.total_costo.toLocaleString(undefined, { minimumFractionDigits: 2 })}`
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>