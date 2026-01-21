<?php
session_start();
if (!isset($_SESSION['SISTEMA']['id_empleado'])) {
    header("Location: login.php?error=no_autenticado");
    exit();
}
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si es una petición AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['funcion'])) {
    include("inc/conectar.php");

    switch ($_POST['funcion']) {
        case 'Carga_Compras':
            $proveedor = '';
            if (isset($_POST['proveedor']) && $_POST['proveedor'] != '') {
                $prov = explode("-", $_POST['proveedor']);
                $proveedor = ' AND compras.idproveedores=' . $prov[0];
            }

            $sql = "SELECT c.*, 
                           p.nombre AS proveedor,
                           e.nombre AS empleado
                    FROM compras c
                    LEFT JOIN proveedores p ON p.id_proveedor=c.idproveedores 
                    LEFT JOIN empleados e ON e.id_empleado=c.idempleados 
                    WHERE c.fecha BETWEEN '" . $_POST['fechai'] . " 00:00:00' AND '" . $_POST['fechaf'] . " 23:59:59' 
                    AND c.fechaeliminada IS NULL
                    $proveedor
                    ORDER BY c.fecha ASC";

            $resultados = $consulta->query($sql);

            if ($resultados->rowCount() > 0) {
                foreach ($resultados as $row) {
                    $fecha_formateada = date("d/m/Y H:i:s", strtotime($row["fecha"]));
                    ?>
                    <tr class="text-uppercase">
                        <td><?= str_pad($row["folio"], 6, "0", STR_PAD_LEFT) ?></td>
                        <td><?= $fecha_formateada ?></td>
                        <td><?= htmlspecialchars($row['proveedor']) ?></td>
                        <td><?= htmlspecialchars($row['empleado']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_pago']) ?></td>
                        <td align="right">$ <?= number_format($row['total'], 2) ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="6" class="text-center">No se encontraron compras en el rango de fechas seleccionado</td></tr>';
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

        #tablaCompras {
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
        <h1>Historial de Compras</h1>
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
                        <table id="tablaCompras" class="table table-striped table-bordered table-hover compact">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Usuario</th>
                                    <th>Método de Pago</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="resultados_compras">
                                <tr>
                                    <td colspan="6" class="text-center">Seleccione fechas y haga clic en Buscar</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="total">
                                    <th colspan="5" style="text-align:right">Total:</th>
                                    <th id="total-compras" style="text-align:right"></th>
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

                dataTable = $('#tablaCompras').DataTable({
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
                            .column(5, { search: 'applied' })
                            .data()
                            .reduce(function (a, b) {
                                return a + parseFloat(b.replace('$', '').replace(',', ''));
                            }, 0);

                        $(api.column(5).footer()).html(
                            '$ ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                        );
                    },
                    "columnDefs": [
                        { "type": "currency", "targets": [5] }
                    ]
                });
            }

            // Carga inicial automática
            Carga_Compras();

            // Cargar al cambiar fechas o proveedor
            $('#fechainicial, #fechafinal, #proveedores').change(function () {
                Carga_Compras();
            });

            $('#btnExportar').click(function () {
                if (dataTable) {
                    var data = dataTable.rows({ search: 'applied' }).data();
                    var compras = [];

                    // Encabezados
                    compras.push(['Folio', 'Fecha', 'Proveedor', 'Usuario', 'Método de Pago', 'Importe']);

                    // Obtener el total una sola vez
                    var totalCompras = parseFloat($('#total-compras').text().replace('$', '').replace(',', ''));

                    // Datos de las compras
                    data.each(function (row) {
                        compras.push([
                            row[0], // folio
                            row[1], // fecha
                            row[2], // proveedor
                            row[3], // usuario
                            row[4], // método de pago
                            parseFloat(row[5].replace('$', '').replace(',', ''))
                        ]);
                    });

                    // Agregar fila con el total
                    compras.push([
                        "", "", "", "",
                        'Total:',
                        totalCompras
                    ]);

                    // Crear libro de trabajo
                    var wb = XLSX.utils.book_new();
                    var ws = XLSX.utils.aoa_to_sheet(compras);

                    // Aplicar formato de número con 2 decimales para la columna de Importe
                    if (!ws['!cols']) ws['!cols'] = [];
                    ws['!cols'][5] = { numFmt: '0.00' }; // Formato para columna Importe

                    XLSX.utils.book_append_sheet(wb, ws, "HistorialCompras");

                    // Exportar
                    XLSX.writeFile(wb, 'Historial_Compras_' + new Date().toISOString().slice(0, 10) + '.xlsx');
                }
            });

            function Carga_Compras() {
                var fechaInicial = $("#fechainicial").val();
                var fechaFinal = $("#fechafinal").val();
                var proveedor = $("#proveedores").val();

                if (!fechaInicial || !fechaFinal) {
                    $("#resultados_compras").html('<tr><td colspan="6" class="text-center">Por favor seleccione ambas fechas</td></tr>');
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
                        funcion: "Carga_Compras",
                        fechai: fechaInicial,
                        fechaf: fechaFinal,
                        proveedor: proveedor
                    },
                    beforeSend: function () {
                        if (dataTable !== null) {
                            dataTable.destroy();
                            dataTable = null;
                        }
                        $("#resultados_compras").html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
                    },
                    success: function (msg) {
                        var tbody = $("#resultados_compras");
                        tbody.html(msg);

                        if (tbody.find('tr').length > 0 &&
                            tbody.find('tr').first().find('td').length === 6) {
                            initDataTable();
                        } else {
                            if (dataTable !== null) {
                                dataTable.destroy();
                                dataTable = null;
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        $("#resultados_compras").html('<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos: ' + error + '</td></tr>');
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