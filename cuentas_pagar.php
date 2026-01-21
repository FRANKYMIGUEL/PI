<?php
// 1. VERIFICACIONES DE SEGURIDAD (PRIMERO)
session_start();

// Redirigir si no hay sesión
if (!isset($_SESSION['SISTEMA']['id_empleado'])) {
    header("Location: login.php?error=no_autenticado");
    exit();
}

// 2. CONEXIONES Y CONFIGURACIONES
include("inc/conectar.php");

// 3. LÓGICA DE FILTROS
$filtro_estado = $_GET['estado'] ?? 'pendiente';
$filtro_proveedor = $_GET['proveedor'] ?? '';

// Actualizar estados vencidos
$actualizar = $consulta->prepare("UPDATE cuentas_por_pagar 
                                SET estado = 'vencido' 
                                WHERE fecha_vencimiento < CURDATE() 
                                AND estado NOT IN ('pagado', 'vencido')");
$actualizar->execute();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cuentas por Pagar</title>
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
            background-color: rgb(220, 86, 53);
            color: #fff;
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
                            Cuentas por Pagar
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
                                    <option value="">Todos</option>
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
                                <label class="form-label">Proveedor</label>
                                <input type="text" name="proveedor" class="form-control"
                                    placeholder="Buscar proveedor..."
                                    value="<?= htmlspecialchars($filtro_proveedor) ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn bt_custo me-2">Filtrar</button>
                                <a href="cuentas_pagar.php" class="btn btn-outline-secondary">Limpiar</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de cuentas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Listado de Cuentas</h5>
                    </div>
                    <div class="card-body compact">
                        <div class="table-responsive ">
                            <table id="tablaCuentas" class="table table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Compra</th>
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
                                    cp.*,
                                    p.nombre AS proveedor_nombre,
                                    c.folio AS compra_folio 
                                    FROM 
                                        cuentas_por_pagar cp
                                    JOIN 
                                        proveedores p ON cp.id_proveedor = p.id_proveedor
                                    JOIN 
                                        compras c ON cp.id_compra = c.id_compra
                                    WHERE 
                                        1=1";

                                    if (!empty($filtro_estado)) {
                                        if ($filtro_estado == 'vencido') {
                                            $sql .= " AND (cp.estado = 'vencido' OR (cp.fecha_vencimiento < CURDATE() AND cp.estado != 'pagado'))";
                                        } else {
                                            $sql .= " AND cp.estado = :estado";
                                        }
                                    }

                                    if (!empty($filtro_proveedor)) {
                                        $sql .= " AND (p.nombre LIKE :proveedor OR p.razon_social LIKE :proveedor)";
                                    }

                                    $sql .= " ORDER BY cp.fecha_vencimiento ASC";

                                    $stmt = $consulta->prepare($sql);

                                    if (!empty($filtro_estado) && $filtro_estado != 'vencido') {
                                        $stmt->bindValue(':estado', $filtro_estado);
                                    }

                                    if (!empty($filtro_proveedor)) {
                                        $stmt->bindValue(':proveedor', "%$filtro_proveedor%");
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
                                            <td><?= $cuenta['id_cuenta_pagar'] ?></td>
                                            <td><?= htmlspecialchars($cuenta['proveedor_nombre']) ?></td>
                                            <td>
                                                <a href="detalle_compra.php?id=<?= $cuenta['id_compra'] ?>" target="_blank">
                                                    <?= $cuenta['compra_folio'] ?>
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
                                                <?php if ($cuenta['estado'] != 'pagado'): ?>
                                                    <button class="btn btn-sm bt_custom1 btn-registrar-pago"
                                                        data-id="<?= $cuenta['id_cuenta_pagar'] ?>"
                                                        data-saldo="<?= $cuenta['saldo_pendiente'] ?>">
                                                        <i class="bi bi-cash"></i> Pago
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm bt_custom1 btn-reimprimir-ticket"
                                                        data-id="<?= $cuenta['id_cuenta_pagar'] ?>">
                                                        <i class="bi bi-printer"></i> Ticket
                                                    </button>
                                                <?php endif; ?>
                                                <a href="historial_pagos2.php?id=<?= $cuenta['id_cuenta_pagar'] ?>"
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
                        <input type="hidden" name="id_cuenta_pagar" id="id_cuenta_pagar">
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
                                <option value="transferencia">Transferencia</option>
                                <option value="cheque">Cheque</option>
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

            <?php if (!empty($filtro_proveedor)): ?>
                table.columns([1]).search('<?= $filtro_proveedor ?>').draw();
            <?php endif; ?>

            // Mostrar modal de pago
            $('.btn-registrar-pago').click(function () {
                var id = $(this).data('id');
                var saldo = $(this).data('saldo');

                $('#id_cuenta_pagar').val(id);
                $('#saldo_pendiente').val('$' + parseFloat(saldo).toFixed(2));
                $('#monto_pago').attr('max', saldo);
                $('#monto_pago').val(saldo);

                $('#pagoModal').modal('show');
            });
            $('.btn-reimprimir-ticket').click(function () {
                var idCuenta = $(this).data('id');

                $.ajax({
                    url: 'funciones/obtener_pagos.php',
                    type: 'GET',
                    data: { id_cuenta_pagar: idCuenta },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Respuesta del servidor:", response);

                        if (response.success && response.pagos && response.pagos.length > 0) {
                            const cuenta = response.cuenta;
                            const pagos = response.pagos;
                            const totalPagado = parseFloat(response.total_pagado);
                            const saldoActual = parseFloat(cuenta.saldo_pendiente);
                            const montoInicial = parseFloat(cuenta.monto_total);

                            // Generar contenido del ticket
                            var contenido = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Ticket de Pagos</title>
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
                        .pago-item {
                            margin-bottom: 10px;
                            padding-bottom: 5px;
                            border-bottom: 1px dashed #ccc;
                        }
                        .saldo {
                            margin: 5px 0;
                            font-size: 11px;
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
                    ${saldoActual == 0 ? '<div class="pagado-watermark">PAGADO</div>' : ''}
                    
                    <div class="header">
                        <div class="title">GTC</div>
                        <div class="empresa">HISTORIAL DE PAGOS</div>
                        <div>${new Date().toLocaleDateString()}</div>
                    </div>
                    
                    <div class="info">
                        <div class="info-item">
                            <span class="info-label">Proveedor:</span>
                            <span>${cuenta.proveedor_nombre}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Compra:</span>
                            <span>${cuenta.compra_folio}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Monto Inicial:</span>
                            <span>$${montoInicial.toFixed(2)}</span>
                        </div>
                    </div>

                    <div class="detalle">
                        <div class="text-center" style="margin-bottom: 10px; font-weight: bold;">DETALLE DE PAGOS</div>
                        
                        ${pagos.map((pago, index) => `
                        <div class="pago-item">
                            <div class="info-item">
                                <span class="info-label">Pago #${index + 1}:</span>
                                <span>$${parseFloat(pago.monto_pago).toFixed(2)}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fecha:</span>
                                <span>${new Date(pago.fecha_pago).toLocaleDateString()}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Método:</span>
                                <span>${pago.metodo_pago}</span>
                            </div>
                            ${pago.referencia ? `
                            <div class="info-item">
                                <span class="info-label">Referencia:</span>
                                <span>${pago.referencia}</span>
                            </div>` : ''}
                            ${pago.observaciones ? `
                            <div style="font-size: 10px; margin-top: 3px;">
                                <div style="font-weight: bold;">Obs:</div>
                                <div>${pago.observaciones}</div>
                            </div>` : ''}
                        </div>
                        `).join('')}
                    </div>

                    <div class="saldo">
                        <div class="info-item" style="font-weight: bold;">
                            <span>Total Pagado:</span>
                            <span>$${totalPagado.toFixed(2)}</span>
                        </div>
                        <div class="info-item" style="font-weight: bold;">
                            <span>Saldo Actual:</span>
                            <span>$${saldoActual.toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <div>${pagos.length} pago(s) registrado(s)</div>
                        <div>${new Date().getFullYear()} - GTC</div>
                    </div>
                </body>
                </html>
                `;

                            // Abrir nueva pestaña
                            var nuevaPestana = window.open('about:blank', '_blank');
                            nuevaPestana.document.open();
                            nuevaPestana.document.write(contenido);
                            nuevaPestana.document.close();
                        } else {
                            alert('Error: ' + (response.message || 'No se encontraron pagos'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error en la solicitud:", status, error);
                        alert('Error al conectar con el servidor. Ver consola para detalles.');
                    }
                });
            });
            // Enviar formulario de pago
            // Enviar formulario de pago
            $('#formPago').submit(function (e) {
                e.preventDefault();

                // Mostrar loader o indicador de procesamiento
                $('#pagoModal').find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');

                // Preparar datos del formulario
                const formData = {
                    id_cuenta_pagar: $('#id_cuenta_pagar').val(),
                    monto_pago: $('#monto_pago').val(),
                    metodo_pago: $('select[name="metodo_pago"]').val(),
                    referencia: $('input[name="referencia"]').val(),
                    observaciones: $('textarea[name="observaciones"]').val()
                };

                $.ajax({
                    url: 'funciones/registrar_pago_proveedor.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    dataType: 'json',
                    success: function (response) {
                        $('#pagoModal').modal('hide');
                        $('#pagoModal').find('button[type="submit"]').prop('disabled', false).text('Registrar Pago');

                        if (response.success) {
                            // Generar y mostrar ticket
                            generarTicketPago(response.pago);

                            // Recargar la página después de un breve retraso
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#pagoModal').find('button[type="submit"]').prop('disabled', false).text('Registrar Pago');

                        console.error("Error en la solicitud:", status, error, xhr.responseText);

                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            alert('Error: ' + (errorResponse.message || 'Error desconocido'));
                        } catch (e) {
                            alert('Error al procesar el pago. Por favor intente nuevamente.');
                        }
                    }
                });
            });

            // Función para generar el ticket de pago
            function generarTicketPago(pago) {
                const esPagadoCompleto = parseFloat(pago.nuevo_saldo.replace(/,/g, '')) === 0;

                const contenido = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ticket de Pago</title>
            <style>
                body { font-family: 'Courier New', monospace; margin: 0; padding: 10px; font-size: 12px; width: 280px; }
                .header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
                .title { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
                .info-item { display: flex; justify-content: space-between; margin-bottom: 3px; }
                .info-label { font-weight: bold; }
                .detalle { margin: 10px 0; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0; }
                .total { text-align: right; font-weight: bold; margin: 10px 0; font-size: 14px; }
                .footer { text-align: center; margin-top: 10px; font-size: 10px; border-top: 1px dashed #000; padding-top: 10px; }
                .pagado-watermark { 
                    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg);
                    font-size: 60px; font-weight: bold; color: #28a745; opacity: 0.2; z-index: 1;
                }
            </style>
        </head>
        <body>
            ${esPagadoCompleto ? '<div class="pagado-watermark">PAGADO</div>' : ''}
            
            <div class="header">
                <div class="title">GTC</div>
                <div>COMPROBANTE DE PAGO</div>
                <div>${new Date(pago.fecha_pago).toLocaleString()}</div>
            </div>
            
            <div>
                <div class="info-item">
                    <span class="info-label">No. Pago:</span>
                    <span>${pago.id_pago}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Proveedor:</span>
                    <span>${pago.proveedor_nombre}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Compra:</span>
                    <span>${pago.compra_folio}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Método:</span>
                    <span>${pago.metodo_pago}</span>
                </div>
                ${pago.referencia ? `
                <div class="info-item">
                    <span class="info-label">Referencia:</span>
                    <span>${pago.referencia}</span>
                </div>` : ''}
                <div class="info-item">
                    <span class="info-label">Registrado por:</span>
                    <span>${pago.empleado}</span>
                </div>
            </div>
            
            <div class="detalle">
                <div style="text-align: center; font-weight: bold; margin-bottom: 5px;">DETALLE DEL PAGO</div>
                <div class="info-item">
                    <span>Pago cuenta #${pago.id_cuenta_pagar}</span>
                    <span>$${pago.monto_pago}</span>
                </div>
            </div>

            <div>
                <div class="info-item">
                    <span class="info-label">Saldo anterior:</span>
                    <span>$${pago.saldo_anterior}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Monto pagado:</span>
                    <span style="color: green;">-$${pago.monto_pago}</span>
                </div>
                <div class="info-item" style="border-top: 1px solid #ccc; margin-top: 3px; padding-top: 3px; font-weight: bold;">
                    <span class="info-label">Nuevo saldo:</span>
                    <span>$${pago.nuevo_saldo}</span>
                </div>
            </div>
            
            <div class="total">
                TOTAL PAGADO: $${pago.monto_pago}
            </div>
            
            ${pago.observaciones ? `
            <div style="margin: 10px 0; font-size: 11px;">
                <div style="font-weight: bold;">Observaciones:</div>
                <div>${pago.observaciones}</div>
            </div>` : ''}
            
            <div class="footer">
                <div>Pago registrado</div>
                <div>${new Date().getFullYear()} - GTC</div>
            </div>
        </body>
        </html>
    `;

                // Abrir nueva pestaña con el ticket
                const nuevaPestana = window.open('', '_blank');
                nuevaPestana.document.open();
                nuevaPestana.document.write(contenido);
                nuevaPestana.document.close();
            }
        });
    </script>
</body>

</html>