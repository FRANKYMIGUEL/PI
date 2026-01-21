<?php
include("inc/conectar.php");

// Filtros
$filtro_estado = $_GET['estado'] ?? 'pendiente';
$filtro_cliente = $_GET['cliente'] ?? '';

// Actualizar estados vencidos en la base de datos
$actualizar = $consulta->prepare("UPDATE cuentas_por_cobrar 
                                SET estado = 'vencido' 
                                WHERE fecha_vencimiento < CURDATE() 
                                AND estado NOT IN ('pagado', 'vencido')");
$actualizar->execute();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cuentas por Cobrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <style>
        /* Estilos personalizados */
        body {
            background-color: #f0f8ff;
            background: linear-gradient(135deg, #F2EFE7 0%, #F2EFE7 50%, #F2EFE7 100%);
        }

        .table-responsive {
            margin: 20px 0;
            background-color: #F2EFE7;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(41, 115, 178, 0.1);
        }

        .table {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.9em;
            background-color: #F2EFE7;
            backdrop-filter: blur(5px);
        }

        .table thead tr {
            background-color: #2973B2;
            color: white;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #9ACBD0;
            white-space: nowrap;
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
            transform: scale(1.005);
        }

        .badge-pendiente {
            background-color: #ffc107;
            color: #000;
        }

        .badge-parcial {
            background-color: #fd7e14;
            color: #fff;
        }

        .badge-pagado {
            background-color: #28a745;
            color: #fff;
        }

        .badge-vencido {
            background-color: #dc3545;
            color: #fff;
        }

        .vencido {
            background-color: #fff3f3;
        }

        .bt_custom {
            background-color: #48A6A7;
            color: white;
            border: none;
            padding: 3px 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .bt_custo {
            background-color: #48A6A7;
            color: white;
            border: none;
            padding: 8px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .bt_custom1 {
            background-color: rgb(11, 156, 31);
            color: white;
            border: none;
            padding: 3px 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
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

        .form-control {
            background-color: #F2EFE7;
            border: 1px solid #9ACBD0;
        }

        .dataTables_wrapper .dataTables_filter input {
            background-color: #F2EFE7;
            border: 1px solid #9ACBD0;
        }

        .dataTables_wrapper .dataTables_length select {
            background-color: #F2EFE7;
            border: 1px solid #9ACBD0;
        }

        .paginate_button {
            border-radius: 5px !important;
            margin: 0 3px !important;
        }

        .paginate_button.current {
            background: #2973B2 !important;
            color: white !important;
            border: none !important;
        }

        .banner {
            background-color: #2973B2;
            /* Azul intenso */
            color: white;
            padding: 5px;
        }
    </style>
</head>

<body>
    <?
    include "menu.php";
    ?>
    <div class="container-fluid">
        <div class="row">
            <main class="container-fluid px-0">
                <div class="row g-0">
                    <div class="col-12 text-center banner">
                        <h1 class="">
                            Cuentas por Cobrar
                        </h1>
                    </div>
                </div>
                <!-- Filtros -->
                <div class="card mb-4">
                    <div>
                        <h1 class="mb-4">

                        </h1>
                    </div>
                    <div class="card-body">
                        <form method="get" class="row g-3" id="filtroForm">
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select"
                                    onchange="document.getElementById('filtroForm').submit()">
                                    <option value="">Todos</option> <!-- MOVIMOS "Todos" AL PRINCIPIO -->
                                    <option value="pendiente" <?= $filtro_estado == 'pendiente' ? 'selected' : '' ?>>
                                        Pendientes</option>
                                    <option value="parcial" <?= $filtro_estado == 'parcial' ? 'selected' : '' ?>>Parciales
                                    </option>
                                    <option value="pagado" <?= $filtro_estado == 'pagado' ? 'selected' : '' ?>>Pagados
                                    </option>
                                    <option value="vencido" <?= $filtro_estado == 'vencido' ? 'selected' : '' ?>>Vencidos
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Cliente</label>
                                <input type="text" name="cliente" class="form-control" placeholder="Buscar cliente..."
                                    value="<?= htmlspecialchars($filtro_cliente) ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn bt_custo me-2">Filtrar</button>
                                <a href="cuentas_cobrar.php" class="btn btn-outline-secondary">Limpiar</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de cuentas -->
                <div class="card compact">
                    <div class="card-header compact">
                        <h5 class="mb-0">Listado de Cuentas</h5>
                    </div>
                    <div class="card-body compact">
                        <div class="table-responsive ">
                            <table id="tablaCuentas" class="table table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Venta</th>
                                        <th>Monto</th>
                                        <th>Saldo</th>
                                        <th>Vencimiento</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT 
                                    cc.*,
                                    CONCAT(c.nombre, ' ', c.apellido_p, ' ', c.apellido_m) AS cliente_nombre, 
                                    v.folio AS venta_folio 
                                    FROM 
                                        cuentas_por_cobrar cc
                                    JOIN 
                                         clientes c ON cc.id_cliente = c.id_cliente
                                    JOIN 
                                        ventas v ON cc.id_venta = v.id_venta
                                    WHERE 
                                        1=1";

                                    if (!empty($filtro_estado)) {
                                        if ($filtro_estado == 'vencido') {
                                            $sql .= " AND (cc.estado = 'vencido' OR (cc.fecha_vencimiento < CURDATE() AND cc.estado != 'pagado'))";
                                        } else {
                                            $sql .= " AND cc.estado = :estado";
                                        }
                                    }

                                    if (!empty($filtro_cliente)) {
                                        $sql .= " AND (c.nombre LIKE :cliente OR c.apellido_p LIKE :cliente)";
                                    }

                                    $sql .= " ORDER BY cc.fecha_vencimiento ASC";

                                    $stmt = $consulta->prepare($sql);

                                    if (!empty($filtro_estado) && $filtro_estado != 'vencido') {
                                        $stmt->bindValue(':estado', $filtro_estado);
                                    }

                                    if (!empty($filtro_cliente)) {
                                        $stmt->bindValue(':cliente', "%$filtro_cliente%");
                                    }

                                    $stmt->execute();
                                    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($cuentas as $cuenta) {
                                        $clase_fila = '';
                                        $hoy = new DateTime();
                                        $vencimiento = new DateTime($cuenta['fecha_vencimiento']);

                                        if ($cuenta['estado'] == 'vencido' || ($hoy > $vencimiento && $cuenta['estado'] != 'pagado')) {
                                            $clase_fila = 'vencido';
                                            $cuenta['estado'] = 'vencido'; // Forzar estado visual
                                        }
                                        ?>
                                        <tr class="<?= $clase_fila ?>">
                                            <td><?= $cuenta['id_cuenta_cobrar'] ?></td>
                                            <td><?= htmlspecialchars($cuenta['cliente_nombre']) ?></td>
                                            <td>
                                                <a href="detalle_venta.php?id=<?= $cuenta['id_venta'] ?>" target="_blank">
                                                    <?= $cuenta['venta_folio'] ?>
                                                </a>
                                            </td>
                                            <td>$<?= number_format($cuenta['monto_total'], 2) ?></td>
                                            <td>$<?= number_format($cuenta['saldo_pendiente'], 2) ?></td>
                                            <td><?= date('d/m/Y', strtotime($cuenta['fecha_vencimiento'])) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $cuenta['estado'] ?>">
                                                    <?= ucfirst($cuenta['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm bt_custom1 btn-registrar-pago"
                                                    data-id="<?= $cuenta['id_cuenta_cobrar'] ?>"
                                                    data-saldo="<?= $cuenta['saldo_pendiente'] ?>">
                                                    <i class="bi bi-cash"></i> Pago
                                                </button>
                                                <a href="historial_pagos.php?id=<?= $cuenta['id_cuenta_cobrar'] ?>"
                                                    class="btn btn-sm bt_custom">
                                                    <i class="bi bi-clock-history"></i> Historial
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para registrar pago -->
    <div class="modal fade" id="pagoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPago">
                        <input type="hidden" name="id_cuenta_cobrar" id="id_cuenta_cobrar">
                        <div class="mb-3">
                            <label class="form-label">Saldo pendiente</label>
                            <input type="text" class="form-control" id="saldo_pendiente" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto a pagar *</label>
                            <input type="number" class="form-control" name="monto_pago" id="monto_pago" step="0.01"
                                min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método de pago *</label>
                            <select class="form-select" name="metodo_pago" required>
                                <option value="efectivo">Efectivo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Referencia</label>
                            <input type="text" class="form-control" name="referencia">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn bt_custo">Registrar Pago</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicializar DataTable
            var table = $('#tablaCuentas').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json'
                },
                dom: '<"top"Bf>rt<"bottom"lip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-excel"></i> Excel',
                        className: 'btn bt_custo'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Imprimir',
                        className: 'btn bt_custo'
                    }
                ],
                responsive: true,
                order: [[5, 'asc']] // Ordenar por fecha de vencimiento por defecto
            });

            // Aplicar filtros si existen
            <?php if (!empty($filtro_estado)): ?>
                table.column(6).search('<?= $filtro_estado ?>').draw();
            <?php endif; ?>

            <?php if (!empty($filtro_cliente)): ?>
                table.columns([1]).search('<?= $filtro_cliente ?>').draw();
            <?php endif; ?>

            // Mostrar modal de pago
            $('.btn-registrar-pago').click(function () {
                var id = $(this).data('id');
                var saldo = $(this).data('saldo');

                $('#id_cuenta_cobrar').val(id);
                $('#saldo_pendiente').val('$' + parseFloat(saldo).toFixed(2));
                $('#monto_pago').attr('max', saldo);
                $('#monto_pago').val(saldo);

                $('#pagoModal').modal('show');
            });

            // Enviar formulario de pago
            $('#formPago').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'funciones/registrar_pago.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#pagoModal').modal('hide');

                            // Verificar si el nuevo saldo es 0 para mostrar "PAGADO"
                            var esPagadoCompleto = parseFloat(response.pago.nuevo_saldo) === 0;

                            // Generar contenido del ticket
                            var contenido = `
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Ticket de Pago</title>
                                <style>
                                    body { 
                                        font-family: 'Courier New', monospace; 
                                        margin: 0; 
                                        padding: 10px; 
                                        font-size: 12px;
                                        width: 280px;
                                        position: relative;
                                    }
                                    .header { 
                                        text-align: center; 
                                        margin-bottom: 10px; 
                                        border-bottom: 1px dashed #000;
                                        padding-bottom: 10px;
                                    }
                                    .title { 
                                        font-size: 14px; 
                                        font-weight: bold; 
                                        margin-bottom: 5px;
                                    }
                                    .empresa { 
                                        font-size: 12px; 
                                        margin-bottom: 5px;
                                    }
                                    .info-item {
                                        display: flex;
                                        justify-content: space-between;
                                        margin-bottom: 3px;
                                    }
                                    .info-label {
                                        font-weight: bold;
                                    }
                                    .detalle {
                                        margin: 10px 0;
                                        border-top: 1px dashed #000;
                                        border-bottom: 1px dashed #000;
                                        padding: 5px 0;
                                    }
                                    .detalle-item {
                                        display: flex;
                                        justify-content: space-between;
                                        margin-bottom: 3px;
                                    }
                                    .saldo {
                                        margin: 5px 0;
                                        font-size: 11px;
                                        position: relative;
                                        z-index: 2;
                                    }
                                    .total {
                                        text-align: right;
                                        font-weight: bold;
                                        margin: 10px 0;
                                        font-size: 14px;
                                    }
                                    .footer {
                                        text-align: center;
                                        margin-top: 10px;
                                        font-size: 10px;
                                        border-top: 1px dashed #000;
                                        padding-top: 10px;
                                    }
                                    .text-center {
                                        text-align: center;
                                    }
                                    .pagado-watermark {
                                        position: absolute;
                                        top: 50%;
                                        left: 50%;
                                        transform: translate(-50%, -50%) rotate(-30deg);
                                        font-size: 60px;
                                        font-weight: bold;
                                        color: #28a745;
                                        opacity: 0.2;
                                        z-index: 1;
                                        pointer-events: none;
                                    }
                                </style>
                            </head>
                            <body>
                                ${esPagadoCompleto ? '<div class="pagado-watermark">PAGADO</div>' : ''}
                                
                                <div class="header">
                                    <div class="title">${'GTC'}</div>
                                    <div class="empresa">COMPROBANTE DE PAGO</div>
                                    <div>${new Date(response.pago.fecha_pago).toLocaleDateString()} ${new Date(response.pago.fecha_pago).toLocaleTimeString()}</div>
                                </div>
                                
                                <div class="info">
                                    <div class="info-item">
                                        <span class="info-label">No. Pago:</span>
                                        <span>${response.pago.id_pago}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Cliente:</span>
                                        <span>${response.pago.cliente_nombre}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Venta:</span>
                                        <span>${response.pago.venta_folio}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Método:</span>
                                        <span>${response.pago.metodo_pago}</span>
                                    </div>
                                    ${response.pago.referencia ? `
                                    <div class="info-item">
                                        <span class="info-label">Referencia:</span>
                                        <span>${response.pago.referencia}</span>
                                    </div>` : ''}
                                </div>
                                
                                <div class="detalle">
                                    <div class="text-center" style="margin-bottom: 5px; font-weight: bold;">DETALLE DEL PAGO</div>
                                    <div class="detalle-item">
                                        <span>Pago cuenta #${response.pago.id_cuenta_cobrar}</span>
                                        <span>$${parseFloat(response.pago.monto_pago).toFixed(2)}</span>
                                    </div>
                                </div>

                                <div class="saldo">
                                    <div class="info-item">
                                        <span class="info-label">Saldo anterior:</span>
                                        <span>$${parseFloat(response.pago.saldo_anterior).toFixed(2)}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Monto pagado:</span>
                                        <span style="color: green;">-$${parseFloat(response.pago.monto_pago).toFixed(2)}</span>
                                    </div>
                                    <div class="info-item" style="border-top: 1px solid #ccc; margin-top: 3px; padding-top: 3px; font-weight: bold;">
                                        <span class="info-label">Nuevo saldo:</span>
                                        <span>$${parseFloat(response.pago.nuevo_saldo).toFixed(2)}</span>
                                    </div>
                                </div>
                                
                                <div class="total">
                                    TOTAL PAGADO: $${parseFloat(response.pago.monto_pago).toFixed(2)}
                                </div>
                                
                                ${response.pago.observaciones ? `
                                <div style="margin: 10px 0; font-size: 11px;">
                                    <div style="font-weight: bold;">Observaciones:</div>
                                    <div>${response.pago.observaciones}</div>
                                </div>` : ''}
                                
                                <div class="footer">
                                    <div>¡Gracias por su pago!</div>
                                    <div>${new Date().getFullYear()} - ${'GTC'}</div>
                                </div>
                            </body>
                            </html>
                        `;

                            // Abrir nueva pestaña
                            var nuevaPestana = window.open('about:blank', '_blank');
                            nuevaPestana.document.open();
                            nuevaPestana.document.write(contenido);
                            nuevaPestana.document.close();

                            // Recargar la página después de un breve retraso
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('Error al registrar el pago');
                    }
                });
            });
        });
    </script>
</body>

</html>