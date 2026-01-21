<?php
session_start();
require_once 'check_session.php';
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si es una petición AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['funcion'])) {
    include("inc/conectar.php");
    


    switch ($_POST['funcion']) {
        case 'Carga_Ventas':
            $cliente = '';
            if (isset($_POST['cliente']) && $_POST['cliente'] != '') {
                $clien = explode("-", $_POST['cliente']);
                $cliente = ' AND ventas.idclientes=' . $clien[0];
            }

            $sql = "SELECT v.*, 
                           c.nombre AS cliente, 
                           c.apellido_p, 
                           c.apellido_m,
                           e.nombre AS empleado
                    FROM ventas v
                    LEFT JOIN clientes c ON c.id_cliente=v.idclientes 
                    LEFT JOIN empleados e ON e.id_empleado=v.idempleados 
                    WHERE v.fecha BETWEEN '" . $_POST['fechai'] . " 00:00:00' AND '" . $_POST['fechaf'] . " 23:59:59' 
                    AND v.fechacancelada IS NULL
                    $cliente
                    ORDER BY v.fecha ASC";

            $resultados = $consulta->query($sql);

            if ($resultados->rowCount() > 0) {
                foreach ($resultados as $row) {
                    $fecha_formateada = date("d/m/Y H:i:s", strtotime($row["fecha"]));
                    $nombre_cliente = $row['cliente'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m'];
                    ?>
                    <tr class="text-uppercase">
                        <td><?= str_pad($row["folio"], 6, "0", STR_PAD_LEFT) ?></td>
                        <td><?= $fecha_formateada ?></td>
                        <td><?= htmlspecialchars($nombre_cliente) ?></td>
                        <td><?= htmlspecialchars($row['empleado']) ?></td>
                        <td align="right">$ <?= number_format($row['total'], 2) ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">No se encontraron pedidos en el rango de fechas seleccionado</td></tr>';
            }
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas</title>
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

        .form-control-sm {
            border-radius: 5px;
            border: 1px solid #9ACBD0;
            padding: 8px 12px;
            background-color: #F2EFE7;
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

        #tablaPedidos {
            width: 100% !important;
        }

        .total {
            background-color: rgb(178, 41, 41);
            color: white;
            font-weight: bold;
        }

        .total th,
        .total td {
            padding: 10px 15px;
        }

        .dataTables_filter {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .dataTables_filter label {
            display: flex;
            align-items: center;
            margin-bottom: 0;
            gap: 10px;
            font-weight: bold;
        }

        .dataTables_filter input {
            width: 400px !important;
            height: 40px !important;
            font-size: 16px !important;
            margin-left: 10px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php include("menu.php"); ?>
    <div class="col-12 text-center banner">
        <h1>Historial de Ventas</h1>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="form-label"><b>Fecha Inicial</b></label>
                            <input type="date" class="form-control" id="fechainicial" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><b>Fecha Final</b></label>
                            <input type="date" class="form-control" id="fechafinal" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="btnExportar" class="btn bt_custo">
                                <i class="bi bi-file-earmark-excel"></i> Exportar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tablaPedidos" class="table table-striped table-bordered table-hover compact">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Usuario</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="resultados_productos">
                                <tr>
                                    <td colspan="5" class="text-center">Seleccione fechas y haga clic en Buscar</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="total">
                                    <th colspan="4" style="text-align:right">Total:</th>
                                    <th id="total-ventas" style="text-align:right"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {
            var dataTable = null;

            function initDataTable() {
                if (dataTable !== null) {
                    dataTable.destroy();
                }

                dataTable = $('#tablaPedidos').DataTable({
                    "order": [[1, "asc"]],
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "No se encontraron resultados",
                        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        "infoEmpty": "No hay registros disponibles",
                        "infoFiltered": "(filtrado de _MAX_ registros totales)",
                        "search": "Buscar:",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        }
                    },
                    "footerCallback": function (row, data, start, end, display) {
                        var api = this.api();

                        var total = api
                            .column(4, { search: 'applied' })
                            .data()
                            .reduce(function (a, b) {
                                return a + parseFloat(b.replace('$', '').replace(',', ''));
                            }, 0);

                        $(api.column(4).footer()).html(
                            '$ ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                        );
                    },
                    "columnDefs": [
                        { "type": "currency", "targets": [4] }
                    ]
                });
            }

            // Carga inicial automática
            Carga_Entradas();

            // También cargar al cambiar fechas o cliente
            $('#fechainicial, #fechafinal, #clientes').change(function () {
                Carga_Entradas();
            });

            $('#btnExportar').click(function () {
                if (dataTable) {
                    var data = dataTable.rows({ search: 'applied' }).data();
                    var ventas = [];

                    // Encabezados
                    ventas.push(['Folio', 'Fecha', 'Cliente', 'Usuario', 'Importe']);

                    // Obtener el total una sola vez
                    var totalVentas = parseFloat($('#total-ventas').text().replace('$', '').replace(',', ''));

                    // Datos de las ventas
                    data.each(function (row) {
                        ventas.push([
                            row[0], //folio
                            row[1], //fecha
                            row[2], //cliente
                            row[3], // Usuario
                            parseFloat(row[4].replace('$', '').replace(',', ''))
                        ]);
                    });

                    // Agregar fila con el total
                    ventas.push([
                        "", "", "",
                        'Total:',
                        totalVentas
                    ]);

                    // Crear libro de trabajo
                    var wb = XLSX.utils.book_new();
                    var ws = XLSX.utils.aoa_to_sheet(ventas);

                    // Aplicar formato de número con 2 decimales para la columna de Importe
                    if (!ws['!cols']) ws['!cols'] = [];
                    ws['!cols'][4] = { numFmt: '0.00' }; // Formato para columna Importe

                    XLSX.utils.book_append_sheet(wb, ws, "HistorialVentas");

                    // Exportar
                    XLSX.writeFile(wb, 'Historial_Ventas_' + new Date().toISOString().slice(0, 10) + '.xlsx');
                }
            });

            function Carga_Entradas() {
                var fechaInicial = $("#fechainicial").val();
                var fechaFinal = $("#fechafinal").val();

                if (!fechaInicial || !fechaFinal) {
                    $("#resultados_productos").html('<tr><td colspan="5" class="text-center">Por favor seleccione ambas fechas</td></tr>');
                    if (dataTable !== null) {
                        dataTable.destroy();
                        dataTable = null;
                    }
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: window.location.href,
                    data: {
                        funcion: "Carga_Ventas",
                        fechai: fechaInicial,
                        fechaf: fechaFinal,
                        cliente: $("#clientes").val()
                    },
                    beforeSend: function () {
                        if (dataTable !== null) {
                            dataTable.destroy();
                            dataTable = null;
                        }
                        $("#resultados_productos").html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
                    },
                    success: function (msg) {
                        var tbody = $("#resultados_productos");
                        tbody.html(msg);

                        if (tbody.find('tr').length > 0 &&
                            tbody.find('tr').first().find('td').length === 5) {
                            initDataTable();
                        } else {
                            if (dataTable !== null) {
                                dataTable.destroy();
                                dataTable = null;
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        $("#resultados_productos").html('<tr><td colspan="5" class="text-center text-danger">Error al cargar los datos: ' + error + '</td></tr>');
                        if (dataTable !== null) {
                            dataTable.destroy();
                            dataTable = null;
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>