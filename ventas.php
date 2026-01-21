<?php
session_start();

require_once 'check_session.php';


// 3. Conexión a BD y funciones
include("inc/conectar.php");

function FolioVenta() {
    global $consulta;
    $Auto = $consulta->query("SELECT MAX(id_venta)+1 AS autoincrement FROM ventas");
    $row = $Auto->fetch(PDO::FETCH_ASSOC);
    return $row['autoincrement'] ? str_pad($row['autoincrement'], 4, "0", STR_PAD_LEFT) : "0001";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos mejorados para la tabla */
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
            font-size: 0.9em;
            box-shadow: 0 0 10px rgba(41, 115, 178, 0.1);
            /* Sutil sombra azul */
            border-radius: 10px;
            overflow: hidden;
            background-color: rgb(255, 255, 255);
            /* Fondo claro */
        }

        .table thead tr {
            background-color: #2973B2;
            /* Azul más intenso */
            color: white;
            text-align: left;
            font-weight: bold;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #9ACBD0;
            /* Borde azul claro */
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:nth-of-type(even) {
            background-color: rgba(154, 203, 208, 0.1);
            /* Azul claro muy suave */
        }

        .table tbody tr:last-of-type {
            border-bottom: 2px solid #48A6A7;
            /* Azul turquesa */
        }

        .table tbody tr:hover {
            background-color: rgba(154, 203, 208, 0.3);
            /* Azul claro semi-transparente */
            transform: scale(1.005);
        }

        .table .cantidad input {
            width: 60px;
            text-align: center;
            border: 1px solid #9ACBD0;
            /* Azul claro */
            border-radius: 4px;
            padding: 5px;
            background-color: #F2EFE7;
            /* Fondo claro */
        }

        .table .opciones button {
            background: none;
            border: none;
            color: #2973B2;
            /* Azul intenso */
            cursor: pointer;
            font-size: 1.2em;
            transition: color 0.3s;
        }

        body {
            background-color: #F2EFE7 !important;
        }

        .table .opciones button:hover {
            color: #48A6A7;
            /* Azul turquesa al pasar el mouse */
        }

        /* Estilo para los totales */
        #total,
        #cambio {
            font-weight: bold;
            color: #2973B2;
            /* Azul intenso */
            font-size: 1.1em;
        }

        /* Estilo para los inputs */
        .form-control-sm {
            border-radius: 5px;
            border: 1px solid #9ACBD0;
            /* Azul claro */
            padding: 8px 12px;
            background-color: #F2EFE7;
            /* Fondo claro */
        }

        /* Estilo para el botón de agregar producto */
        #agregar_producto {
            background-color: #48A6A7;
            /* Azul turquesa */
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            transition: background-color 0.3s;
            margin-top: 5px;
        }

        #agregar_producto:hover {
            background-color: #2973B2;
            /* Azul más intenso al pasar el mouse */
        }

        /* Estilo para el botón Guardar */
        #Guardar_Venta {
            background-color: #48A6A7;
            /* Azul turquesa */
            border: none;
            padding: 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 15px;
        }

        #Guardar_Venta:hover {
            background-color: #2973B2;
            /* Azul más intenso al pasar el mouse */
        }

        .banner {
            background-color: #2973B2;
            /* Azul intenso */
            color: white;
            padding: 5px;
        }

        /* Estilos específicos para la tarjeta de Resumen Venta */
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(41, 115, 178, 0.08);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #2973B2;
            color: white;
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header h5 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .card-body {
            padding: 15px;
        }

        .card-body .row {
            margin-bottom: 10px;
            align-items: center;
            padding: 5px 0;
        }

        .card-body .row:not(:last-child) {
            border-bottom: 1px solid #e9ecef;
        }

        .col-6:first-child {
            font-weight: 500;
            color: #495057;
        }

        .col-6:last-child {
            text-align: right;
            font-weight: 500;
        }

        #total {
            color: #2973B2;
            font-weight: 600;
            font-size: 1.05rem;
        }

        #efectivo {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: right;
            width: 100%;
            background-color: #f8f9fa;
            color: #2973B2;
            font-weight: 500;
        }

        #efectivo:focus {
            border-color: #2973B2;
            box-shadow: 0 0 0 0.2rem rgba(41, 115, 178, 0.25);
            outline: none;
        }

        #cambio {
            color: #28a745;
            font-weight: 600;
        }

        #Guardar_Venta {
            background-color: #48A6A7;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }

        #Guardar_Venta:hover {
            background-color: #3a8b8c;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            background-color: #9ACBD0;
            color: white;
            border: none;
            padding: 3px 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .bt_custom1 {
            background-color: #2973B2;
            color: white;
            border: none;
            padding: 3px 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        /* Estilos para la sección de Ventas del Día */
        .card-border-primary {
            border: 1px solid #2973B2;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(41, 115, 178, 0.1);
        }

        .display-4 {
            font-size: 2.2rem;
            font-weight: 600;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }

        .card-footer {
            padding: 0.75rem 1.25rem;
            background-color: rgba(41, 115, 178, 0.03);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Estilos adicionales para los botones de cantidad/monto */
        .btn-group-toggle .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-color: #2973B2;
            color: #2973B2;
        }

        .btn-group-toggle .btn-outline-primary.active {
            background-color: #2973B2;
            color: white;
        }

        .btn-group-toggle .btn-outline-primary:hover:not(.active) {
            background-color: rgba(41, 115, 178, 0.1);
            color: #2973B2;
        }

        .btn-group-toggle .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(41, 115, 178, 0.25);
        }

        /* Estilo para los inputs de cantidad/monto */
        #cantidad,
        #monto_fijo {
            transition: all 0.3s ease;
        }

        #cantidad:focus,
        #monto_fijo:focus {
            border-color: #2973B2;
            box-shadow: 0 0 0 0.2rem rgba(41, 115, 178, 0.25);
            outline: none;
        }
    </style>
</head>

<body>
    <?
    include "menu.php";
    ?>
    <?php
    if (isset($_GET['error']) && $_GET['error'] == 'corte_pendiente') {
        echo '<script>
    Swal.fire({
        title: "Corte pendiente",
        text: "Debes realizar el corte de caja antes de cerrar sesión",
        icon: "warning",
        confirmButtonText: "Entendido"
    }).then(() => {
        $("#modalCorteCaja").modal("show");
        // Eliminar el parámetro de error de la URL
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    });
    </script>';
    }
    ?>
    <div class="col-12 banner position-relative d-flex justify-content-center align-items-center">
        <h1 class="m-0 text-center">Ventas</h1>
        <h6 class="m-0 position-absolute end-0 pe-3 text-white fw-bold">
            Usuario: <?= $_SESSION['SISTEMA']['nombre'] ?>
        </h6>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-10 text-center">
                <div class="row align-items-end g-2"> <!-- Añadido g-2 para espacio entre filas -->
                    <!-- Columna Clientes -->
                    <div class="col-md-3">
                        <label for="clientes" class="form-label mb-1">Clientes</label>
                        <input list="datosClientes" name="" autocomplete="off" value="<?= $CLIENTES ?>"
                            class="form-control form-control-sm" id="clientes" placeholder="Buscar clientes">
                        <datalist id="datosClientes" active>
                            <?php
                            $Auto = $consulta->query("SELECT * FROM clientes");
                            foreach ($Auto as $producto) {
                                echo "<option value ='$producto[id_cliente]-$producto[nombre]-$producto[apellido_p]-$producto[apellido_m]'>";
                            }
                            ?>
                        </datalist>
                    </div>

                    <!-- Columna Tipo de Entrada -->
                    <div class="col-md-3">
                        <label class="form-label mb-1 d-block"></label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="tipo_cantidad" id="por_cantidad" autocomplete="off" checked>
                                Por Cantidad
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="tipo_cantidad" id="por_monto" autocomplete="off">
                                Por Monto
                            </label>
                        </div>
                        <input type="number" class="form-control form-control-sm mt-1" id="cantidad" value="1"
                            step="0.01">
                        <input type="number" class="form-control form-control-sm mt-1" id="monto_fijo"
                            placeholder="Monto fijo" style="display: none;" step="0.01">
                    </div>

                    <!-- Columna Productos -->
                    <div class="col-md-5">
                        <div class="d-flex align-items-end" style="height: 100%;">
                            <div style="flex-grow: 1;">
                                <label for="productos" class="form-label mb-1">Productos</label>
                                <div class="input-group">
                                    <input list="datosProductos" name="" autocomplete="off" value="<?= $CLIENTES ?>"
                                        class="form-control form-control-sm" id="productos"
                                        placeholder="Buscar Productos">
                                    
                                </div>
                                <datalist id="datosProductos" active>
                                    <?php
                                    $Auto = $consulta->query("SELECT * FROM productos WHERE productos.fechabaja IS NULL AND existencias > 0");
                                    foreach ($Auto as $producto) {
                                        echo "<option value ='$producto[codigo_barras]-$producto[nombre]'>";
                                    }
                                    ?>
                                </datalist>
                            </div><button class="btn btn-success" id="agregar_producto"
                                        style="white-space: nowrap;">Agregar Producto</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Código</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Precio U.</th>
                                    <th scope="col">Subtotal</th>
                                    <th scope="col" class="opciones">Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_detalle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="row">
                    <div class="col-12">
                        Folio Venta
                        <input type="text" class="form-control form-control-sm" id="folio"
                            value="<?= str_pad(FolioVenta(), 4, "0", STR_PAD_LEFT); ?>" readonly>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Resumen Venta</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">Total</div>
                                    <div class="col-6" id="total">0</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Efectivo</div>
                                    <div class="col-6"><input type="text" id="efectivo" class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Cambio</div>
                                    <div class="col-6" id="cambio">0</div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button class="btn btn-success" id="Guardar_Venta">
                                            Guardar
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                                                <path
                                                    d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                                                <path
                                                    d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                                                <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div><!-- Sección de Resumen del Día -->
                        <div class="card mt-2 card-border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-graph-up"></i> Resumen del Día
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center mb-2 fs-6"> <!-- fs-6 -->
                                    <span class="text-muted">Fecha:</span>
                                    <span class="fw-bold" id="fecha-actual"><?= date('d/m/Y') ?></span>
                                </div>
                                <div class="fs-3 fw-bold text-primary mb-2" id="total-ventas-dia">$0.00
                                    <!-- Cambié a fs-3 -->
                                </div>
                                <div class="progress">
                                    <div id="progress-ventas" class="progress-bar" role="progressbar" style="width: 0%">
                                    </div>
                                </div>
                                <small class="text-muted fs-7">Total acumulado</small>
                                <!-- fs-7 si tu versión lo soporta -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para seleccionar tipo de pago -->
        <div class="modal fade" id="tipoPagoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bt_custom1 text-white">
                        <h5 class="modal-title">Seleccione el tipo de pago</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="row g-3">
                            <div class="col-6">
                                <button type="button" class="bt_custom w-100 py-3" id="btnContado">
                                    <i class="bi bi-cash-coin fs-4"></i><br>
                                    Pago al Contado
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="bt_custom w-100 py-3" id="btnCredito">
                                    <i class="bi bi-credit-card fs-4"></i><br>
                                    Pago a Crédito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
$(document).ready(function () {
    // Función para actualizar el progress bar
    function actualizarProgressBar() {
        const metaDiaria = 1000;
        const totalVentas = parseFloat(
            $('#total-ventas-dia').text()
                .replace('$', '')
                .replace(/,/g, '')
        ) || 0;

        const porcentaje = (totalVentas / metaDiaria) * 100;
        $('#progress-ventas').css('width', `${Math.min(porcentaje, 100)}%`);

        if (porcentaje >= 100) {
            $('#progress-ventas').removeClass('bg-success bg-warning').addClass('bg-success');
            Swal.fire({
                icon: 'success',
                title: '¡Felicidades!',
                text: '¡Meta diaria alcanzada!',
                confirmButtonColor: '#2973B2',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else if (porcentaje >= 50) {
            $('#progress-ventas').removeClass('bg-success bg-danger').addClass('bg-warning');
        } else {
            $('#progress-ventas').removeClass('bg-warning bg-danger').addClass('bg-danger');
        }
    }

    // Alternar entre cantidad y monto
    $(document).on("change", "input[name='tipo_cantidad']", function () {
        if ($("#por_monto").is(":checked")) {
            $("#cantidad").hide();
            $("#monto_fijo").show().val("").focus();
        } else {
            $("#monto_fijo").hide().val("");
            $("#cantidad").show().val("1").focus();
        }
    });

    // Calcular cantidad automáticamente cuando se ingresa monto
    $(document).on("input", "#monto_fijo", function () {
        var monto = parseFloat($(this).val()) || 0;
        var producto = $("#productos").val();

        if (producto && monto > 0) {
            var codigo_barras = producto.split("-")[0];

            $.ajax({
                url: 'funciones/ventas.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    funcion: 'VerificarExistencias',
                    codigo_barras: codigo_barras
                },
                success: function (response) {
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#2973B2'
                        });
                        return;
                    }

                    var precio = parseFloat(response.precio_venta);
                    if (precio > 0) {
                        var cantidad = monto / precio;
                        $("#cantidad").val(cantidad.toFixed(2));
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Precio inválido',
                            text: 'El producto no tiene un precio válido',
                            confirmButtonColor: '#2973B2'
                        });
                        $("#monto_fijo").val("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error AJAX:', error);
                }
            });
        }
    });

    // Ejemplo de llamada después de una venta:
    document.getElementById('Guardar_Venta').addEventListener('click', function () {
        actualizarProgressBar();
    });

    // Actualizar ventas del día al cargar
    actualizarTotalVentasDia();
    
    var bandera = true;
    $(window).on('beforeunload', function (e) {
        if (bandera) {
            e.preventDefault();
            e.returnValue = '¿Estás seguro de que quieres abandonar esta página? Los cambios no guardados se perderán.';
            return '¿Estás seguro de que quieres abandonar esta página? Los cambios no guardados se perderán.';
        }
    });

    // Validar el cambio del efectivo
    $(document).on("change", "#efectivo", function () {
        var total = Quita_Moneda($("#total").text());
        var efectivo = Quita_Moneda($(this).val());
        if (efectivo < total) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'El efectivo no puede ser menor al total',
                confirmButtonColor: '#2973B2'
            });
            $(this).val(total);
            $("#cambio").text("$" + Formato_Moneda(0, 2));
        } else {
            $("#cambio").text("$" + Formato_Moneda(efectivo - total, 2));
        }
    });

    function actualizarTotalVentasDia() {
        $.post('funciones/ventas.php', {
            funcion: 'ObtenerVentasDelDia'
        }, function (response) {
            if (response.success) {
                $('#total-ventas-dia').text('$' + response.total_dia);
                actualizarProgressBar();
            } else {
                console.error('Error:', response.message);
            }
        }, 'json').fail(function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
        });
    }

    // Eventos para agregar producto
    $(document).on("keypress", "#productos, #monto_fijo", function (e) {
        if (e.which == 13) {
            Agregar_Producto();
        }
    });

    $(document).on("click", "#agregar_producto", function () {
        Agregar_Producto();
    });

    function Agregar_Producto() {
        var tipo_venta = $("input[name='tipo_cantidad']:checked").attr("id");
        var productoVal = $("#productos").val();
        
        if (!productoVal) {
            Swal.fire({
                icon: 'error',
                title: 'Producto requerido',
                text: 'Debe seleccionar un producto',
                confirmButtonColor: '#2973B2'
            });
            return false;
        }

        var codigo_barras = productoVal.split("-")[0];
        var cantidad = parseFloat($("#cantidad").val());

        // Manejo diferente para monto vs cantidad
        if (tipo_venta === "por_monto") {
            var monto = parseFloat($("#monto_fijo").val()) || 0;
            if (monto <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Monto inválido',
                    text: 'El monto debe ser mayor a cero',
                    confirmButtonColor: '#2973B2'
                });
                return false;
            }
            
            // Forzar el cálculo de cantidad si es por monto
            if (isNaN(cantidad) || cantidad <= 0) {
                $("#monto_fijo").trigger("input");
                return false;
            }
        } else {
            if (isNaN(cantidad) || cantidad <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cantidad inválida',
                    text: 'La cantidad debe ser mayor a cero',
                    confirmButtonColor: '#2973B2'
                });
                $("#cantidad").val("1").focus();
                return false;
            }
        }

        // Verificar existencias
        $.ajax({
            url: 'funciones/ventas.php',
            type: 'POST',
            dataType: 'json',
            data: {
                funcion: 'VerificarExistencias',
                codigo_barras: codigo_barras
            },
            success: function (response) {
                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#2973B2'
                    });
                    return;
                }

                var max_cantidad = response.permite_decimal ? response.existencias : Math.floor(response.existencias);

                if (cantidad > max_cantidad) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Existencias insuficientes',
                        html: `No hay suficientes existencias.<br>
                       Disponibles: <strong>${max_cantidad}</strong><br>
                       Solicitadas: <strong>${cantidad}</strong>`,
                        confirmButtonColor: '#2973B2'
                    });
                    return;
                }

                // Si hay existencias, proceder a agregar
                agregarProductoATabla(codigo_barras, cantidad, response.permite_decimal);
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo verificar las existencias',
                    confirmButtonColor: '#2973B2'
                });
            }
        });
    }

    function agregarProductoATabla(codigo_barras, cantidad, permite_decimal) {
        // Verificar si el producto ya está en la tabla
        var productoExistente = false;
        $("#tabla_detalle tr").each(function () {
            if ($(this).find("td:eq(1)").text() == codigo_barras) {
                var nuevaCantidad = parseFloat($(this).find(".cantidad").val()) + cantidad;
                $(this).find(".cantidad").val(nuevaCantidad.toFixed(2));
                var precio = Quita_Moneda($(this).find("td:eq(3)").text());
                $(this).find("td:eq(4)").text("$" + Formato_Moneda(nuevaCantidad * precio, 2));
                SumarTotal();
                productoExistente = true;
                return false;
            }
        });

        if (productoExistente) {
            $("#cantidad").val("1");
            $("#monto_fijo").val("");
            $("#productos").val("");
            return;
        }

        // Si el producto no existe en la tabla, hacer la petición para agregarlo
        $.ajax({
            url: 'funciones/ventas.php',
            type: 'POST',
            dataType: 'json',
            data: {
                funcion: 'Agregar',
                codigo_barras: codigo_barras,
                cantidad: cantidad
            },
            success: function (response) {
                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#2973B2'
                    });
                    return;
                }

                var $newRow = $(response.html);
                $newRow.find(".cantidad")
                    .data('old-value', cantidad)
                    .attr('min', '0.01')
                    .attr('max', response.permite_decimal ? response.existencias : Math.floor(response.existencias));

                $("#tabla_detalle").append($newRow);
                SumarTotal();
                $("#cantidad").val("1");
                $("#monto_fijo").val("");
                $("#productos").val("").focus();
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al agregar el producto: ' + error,
                    confirmButtonColor: '#2973B2'
                });
            }
        });
    }

    // Guardar venta
    $(document).on("click", "#Guardar_Venta", async function () {
        if ($("#tabla_detalle tr").length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Venta vacía',
                text: 'No hay productos en la venta',
                confirmButtonColor: '#2973B2'
            });
            return false;
        }

        $('#tipoPagoModal').modal('show');
    });

    // Botón Pago al Contado
    $(document).on("click", "#btnContado", function () {
        $('#tipoPagoModal').modal('hide');
        guardarVenta("contado");
    });

    // Botón Pago a Crédito
    $(document).on("click", "#btnCredito", async function () {
        var clienteInput = $("#clientes").val();

        if (!clienteInput || clienteInput.trim() === "") {
            Swal.fire({
                icon: 'error',
                title: 'Cliente requerido',
                text: 'Para ventas a crédito debe seleccionar un cliente específico',
                confirmButtonColor: '#2973B2'
            });
            $("#clientes").focus();
            return false;
        }

        var clienteData = clienteInput.split("-");
        var idCliente = clienteData[0].trim();

        if (idCliente === "1") {
            Swal.fire({
                icon: 'error',
                title: 'Cliente inválido',
                text: 'No puede registrar créditos para ventas de mostrador. Seleccione un cliente válido.',
                confirmButtonColor: '#2973B2'
            });
            $("#clientes").focus();
            return false;
        }

        if (!/^\d+$/.test(idCliente)) {
            Swal.fire({
                icon: 'error',
                title: 'ID inválido',
                text: 'El ID del cliente no es válido',
                confirmButtonColor: '#2973B2'
            });
            $("#clientes").focus();
            return false;
        }

        const { value: diasCredito } = await Swal.fire({
            title: 'Días de crédito',
            input: 'number',
            inputLabel: 'Ingrese los días de crédito',
            inputValue: 30,
            inputAttributes: {
                min: 1,
                step: 1
            },
            showCancelButton: true,
            confirmButtonColor: '#2973B2',
            cancelButtonColor: '#d33',
            inputValidator: (value) => {
                if (!value || value <= 0) {
                    return 'Debe ingresar un número válido de días';
                }
            }
        });

        if (diasCredito === undefined) return;

        $('#tipoPagoModal').modal('hide');
        guardarVenta("credito", parseInt(diasCredito));
    });

    function guardarVenta(tipoPago, diasCredito = 0) {
        var clienteData = $("#clientes").val().split("-");
        var idCliente = "1";
        var nombreCliente = "Mostrador";

        if (clienteData.length >= 2 && clienteData[0]) {
            idCliente = clienteData[0].trim();
            nombreCliente = clienteData[1].trim() + " " + (clienteData[2] || "") + " " + (clienteData[3] || "");
        }

        // Preparar array de productos
        var productos = [];
        $("#tabla_detalle tr").each(function () {
            var producto = {
                cantidad: parseFloat($(this).find("td:eq(0)").find("input").val()) || 1,
                codigo_barras: $(this).find("td:eq(1)").text().trim(),
                id_productos: $(this).find("td:eq(1)").attr("id_productos"),
                precio: parseFloat(Quita_Moneda($(this).find("td:eq(3)").text())) || 0
            };

            if (!producto.id_productos || producto.precio <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en producto',
                    text: `Datos incorrectos para el producto: ${producto.codigo_barras}`,
                    confirmButtonColor: '#2973B2'
                });
                return false;
            }

            productos.push(producto);
        });

        // Preparar datos para enviar
        var ventaData = {
            funcion: 'Guardar_Venta',
            idclientes: idCliente,
            nombre_cliente: nombreCliente,
            total: parseFloat(Quita_Moneda($("#total").text())) || 0,
            efectivo: parseFloat(Quita_Moneda($("#efectivo").val())) || 0,
            cambio: parseFloat(Quita_Moneda($("#cambio").text())) || 0,
            Detalle: productos,
            tipo_pago: tipoPago,
            dias_credito: diasCredito
        };

        // Validaciones
        if (ventaData.total <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Total inválido',
                text: 'El total de la venta debe ser mayor a cero',
                confirmButtonColor: '#2973B2'
            });
            return false;
        }

        if (tipoPago === "contado" && ventaData.efectivo < ventaData.total) {
            Swal.fire({
                icon: 'warning',
                title: 'Efectivo insuficiente',
                text: 'El efectivo no puede ser menor al total',
                confirmButtonColor: '#2973B2'
            });
            $("#efectivo").focus();
            return false;
        }

        // Enviar datos al servidor
        $.ajax({
            url: 'funciones/ventas.php',
            type: 'POST',
            data: ventaData,
            dataType: 'text',
            success: function (response) {
                console.log(response);
                var folio = response;
                if (folio > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Venta guardada!',
                        html: `Venta guardada correctamente.<br>Folio: <strong>${response}</strong>`,
                        confirmButtonColor: '#2973B2'
                    });
                    bandera = false;

                    // Abrir ticket en nueva pestaña
                    var ticketWindow = window.open("ticket_venta.php?folio=" + folio, '_blank');

                    // Recargar página para limpiar el formulario
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al guardar la venta',
                        footer: `Detalles: ${response}`,
                        confirmButtonColor: '#2973B2'
                    });
                    console.error("Respuesta del servidor:", response);
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Error en la comunicación con el servidor',
                    footer: `Detalles: ${error}`,
                    confirmButtonColor: '#2973B2'
                });
                console.error("AJAX Error:", status, error);
            }
        });
    }

    // Cambiar cantidad de producto existente
    $(document).on("change", ".cantidad", function () {
        var input = $(this);
        var nuevaCantidad = parseFloat(input.val()) || 0;
        var fila = input.closest("tr");
        var codigoBarras = fila.find("td:eq(1)").text().trim();
        var precio = Quita_Moneda(fila.find("td:eq(3)").text());

        if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Valor inválido',
                text: 'La cantidad debe ser mayor a cero',
                confirmButtonColor: '#2973B2'
            });
            input.val(input.data('old-value') || (input.attr('step') === '0.01' ? '0.01' : '1'));
            return;
        }

        $.ajax({
            url: 'funciones/ventas.php',
            type: 'POST',
            dataType: 'json',
            data: {
                funcion: 'VerificarExistencias',
                codigo_barras: codigoBarras
            },
            success: function (response) {
                var max_cantidad = response.permite_decimal ? response.existencias : Math.floor(response.existencias);

                if (nuevaCantidad > max_cantidad) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Existencias insuficientes',
                        html: `No hay suficientes existencias.<br>
                       Disponibles: <strong>${max_cantidad}</strong><br>
                       Solicitadas: <strong>${nuevaCantidad}</strong>`,
                        confirmButtonColor: '#2973B2'
                    });
                    input.val(input.data('old-value') || (input.attr('step') === '0.01' ? '0.01' : '1'));
                    return;
                }

                input.data('old-value', nuevaCantidad);
                fila.find("td:eq(4)").text("$" + Formato_Moneda(nuevaCantidad * precio, 2));
                SumarTotal();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo verificar las existencias',
                    confirmButtonColor: '#2973B2'
                });
                input.val(input.data('old-value') || (input.attr('step') === '0.01' ? '0.01' : '1'));
            }
        });
    });

    // Eliminar producto
    $(document).on("click", ".eliminar", async function () {
        const { isConfirmed } = await Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Estás seguro de eliminar el producto?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2973B2',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (isConfirmed) {
            $(this).parent().parent().remove();
            SumarTotal();
        }
    });

    function SumarTotal() {
        var total = 0;
        $("#tabla_detalle tr").each(function () {
            total += Quita_Moneda($(this).find("td:eq(4)").text());
        });
        $("#total").text("$" + Formato_Moneda(total, 2));
        // Actualizar cambio si hay efectivo
        if (parseFloat(Quita_Moneda($("#efectivo").val()))) {
            $("#efectivo").trigger("change");
        }
    }

    function Formato_Moneda(n, c, d, t) {
        var c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }

    function Quita_Moneda(n) {
        n = String(n);
        var s = parseFloat(n.replace(",", "").replace("$", ""));
        if (isNaN(s)) s = 0;
        return s;
    }
});
</script>
</body>