<?php
session_start();

// 1. Verificar sesión activa - DEBE IR ANTES DE CUALQUIER HTML
if (!isset($_SESSION['SISTEMA']['id_empleado'])) {
    header("Location: login.php");
    exit();
}

// 2. Verificar corte pendiente (si aplica)
if (isset($_SESSION['corte_pendiente'])) {
    header("Location: login.php");
    exit();
}

// 3. Luego incluir conexión y funciones
include("inc/conectar.php");

function FolioCompra() {
    global $consulta;
    $Auto = $consulta->query("SELECT MAX(id_compra)+1 AS autoincrement FROM compras");
    $row = $Auto->fetch(PDO::FETCH_ASSOC);
    return $row['autoincrement'] ? str_pad($row['autoincrement'], 4, "0", STR_PAD_LEFT) : "0001";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* (Mantener todos los estilos anteriores) */

        /* Estilos adicionales para el nuevo modal */
        .modal-lg-custom {
            max-width: 800px;
        }

        .search-container {
            position: relative;
        }

        .search-results {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
            display: none;
        }

        .search-item {
            padding: 8px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .search-item:hover {
            background-color: #f5f5f5;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

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


        .bt_custom {
            background-color: #48A6A7;
            color: white;
            border: none;
            padding: 10px 5px;
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
            background-color: #2973B2;
            color: white;
            border: none;
            padding: 7px 8px;
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
    <?php include "menu.php"; ?>

    <div class="col-12 banner position-relative d-flex justify-content-center align-items-center">
        <h1 class="m-0 text-center">Compras</h1>
        <h6 class="m-0 position-absolute end-0 pe-3 text-white fw-bold">
            Usuario: <?= $_SESSION['SISTEMA']['nombre'] ?>
        </h6>
    </div>

    <div class="container-fluid mt-3">
        <div class="row mb-3">
            <div class="col-10">

            </div>
            <div class=" col-2">
                <button class="btn bt_custom1" id="btnNuevaCompra">
                    <i class="bi bi-plus-circle"></i> Nueva Compra
                </button>
            </div>
        </div>

        <!-- Tabla de compras del día -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bt_custom1 text-white">
                        <h5 class="card-title m-0">Compras del Día</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaComprasDia" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Proveedor</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Tipo Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán con AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para nueva compra -->
        <div class="modal fade" id="modalNuevaCompra" tabindex="-1" aria-labelledby="modalNuevaCompraLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg-custom">
                <div class="modal-content">
                    <div class="modal-header bt_custom1 text-white">
                        <h5 class="modal-title" id="modalNuevaCompraLabel">Nueva Compra</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Folio Compra</label>
                                <input type="text" class="form-control" id="folio"
                                    value="<?= str_pad(FolioCompra(), 4, "0", STR_PAD_LEFT); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha</label>
                                <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s') ?>" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Proveedor</label>
                                <div class="search-container">
                                    <input type="text" class="form-control" id="buscarProveedor"
                                        placeholder="Buscar proveedor...">
                                    <div class="search-results" id="resultadosProveedor"></div>
                                </div>
                                <input type="hidden" id="idProveedor">
                                <div class="mt-2" id="infoProveedor" style="display:none;">
                                    <div class="card card-border-primary p-2">
                                        <strong id="nombreProveedor"></strong>
                                        <small class="text-muted" id="contactoProveedor"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Empleado</label>
                                <input type="text" class="form-control" value="<?= $_SESSION['SISTEMA']['nombre'] ?>"
                                    readonly>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Buscar Producto</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="buscarProducto"
                                        placeholder="Código o nombre del producto">
                                    <button class="btn bt_custom" id="btnBuscarProducto">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <div class="search-results" id="resultadosProducto"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Precio Unitario (Costo)</label>
                                    <input type="number" class="form-control" id="precioProducto" min="0.01"
                                        step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio de Venta</label>
                                    <input type="number" class="form-control" id="precioVentaProducto" min="0.01"
                                        step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidadProducto" min="1" value="1">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4">

                            </div>
                            <div class="col-4">
                                <button class="btn bt_custom w-100" id="btnAgregarProducto">
                                    <i class="bi bi-plus-circle"></i> Agregar Producto
                                </button>
                            </div>
                            <div class="col-4">

                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>P. Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaDetalleCompra">
                                    <!-- Aquí se agregarán los productos -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total:</th>
                                        <th id="totalCompra">$0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bt_custo" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn bt_custom1" id="btnGuardarCompra">Guardar Compra</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para seleccionar tipo de pago (mantener el existente) -->
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
    </div>

    <script>
        $(document).ready(function () {
            // Variables globales
            var productosSeleccionados = [];
            var bandera = true;

            // Mostrar modal al hacer clic en Nueva Compra
            $('#btnNuevaCompra').click(function () {
                $('#modalNuevaCompra').modal('show');
            });
            // Función para cargar compras del día
            function cargarComprasDelDia() {
                $.ajax({
                    url: 'funciones/compras.php',
                    type: 'POST',
                    data: {
                        funcion: 'obtenerComprasDia'
                    },
                    success: function (response) {
                        var compras = JSON.parse(response);
                        var tabla = $('#tablaComprasDia tbody');
                        tabla.empty();

                        if (compras.length > 0) {
                            compras.forEach(function (compra) {
                                var fila = `
                        <tr>
                            <td>${compra.folio}</td>
                            <td>${compra.proveedor}</td>
                            <td>${compra.fecha}</td>
                            <td>$${parseFloat(compra.total).toFixed(2)}</td>
                            <td>${compra.tipo_pago === 'contado' ? 'Contado' : 'Crédito'}</td>
                            <td>
                                <button class="btn btn-danger btn-sm btn-eliminar-compra" data-id="${compra.id_compra}">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    `;
                                tabla.append(fila);
                            });
                        } else {
                            tabla.append('<tr><td colspan="6" class="text-center">No hay compras registradas hoy</td></tr>');
                        }
                    }
                });
            }
            // Eliminar compra
            $(document).on('click', '.btn-eliminar-compra', function () {
                var idCompra = $(this).data('id');
                console.log("ID de compra a eliminar:", idCompra); // Depuración

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esta acción!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2973B2',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log("Enviando solicitud de eliminación..."); // Depuración
                        $.ajax({
                            url: 'funciones/compras.php',
                            type: 'POST',
                            data: {
                                funcion: 'eliminarCompra',
                                id_compra: idCompra
                            },
                            dataType: 'json',
                            success: function (response) {
                                console.log("Respuesta del servidor:", response); // Depuración
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Eliminado!',
                                        text: response.message,
                                        confirmButtonColor: '#2973B2'
                                    }).then(() => {
                                        cargarComprasDelDia();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonColor: '#2973B2'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("Error en la solicitud:", error); // Depuración
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error al intentar eliminar la compra',
                                    confirmButtonColor: '#2973B2'
                                });
                            }
                        });
                    }
                });
            });

            // Cargar compras al iniciar la página
            cargarComprasDelDia();
            // Buscar proveedor
            $('#buscarProveedor').on('input', function () {
                var query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: 'funciones/compras.php',
                        type: 'POST',
                        data: {
                            funcion: 'buscarProveedor',
                            query: query
                        },
                        success: function (response) {
                            var resultados = JSON.parse(response);
                            var html = '';

                            if (resultados.length > 0) {
                                resultados.forEach(function (proveedor) {
                                    html += `<div class="search-item" data-id="${proveedor.id_proveedor}" 
                                    data-nombre="${proveedor.nombre}" 
                                    data-contacto="${proveedor.contacto || 'Sin contacto'}">
                                    ${proveedor.nombre} - ${proveedor.contacto || ''}
                                </div>`;
                                });
                                $('#resultadosProveedor').html(html).show();
                            } else {
                                $('#resultadosProveedor').html('<div class="search-item">No se encontraron resultados</div>').show();
                            }
                        }
                    });
                } else {
                    $('#resultadosProveedor').hide();
                }
            });

            // Seleccionar proveedor de los resultados
            $(document).on('click', '#resultadosProveedor .search-item', function () {
                var id = $(this).data('id');
                var nombre = $(this).data('nombre');
                var contacto = $(this).data('contacto');

                $('#buscarProveedor').val(nombre);
                $('#idProveedor').val(id);
                $('#nombreProveedor').text(nombre);
                $('#contactoProveedor').text(contacto);
                $('#infoProveedor').show();
                $('#resultadosProveedor').hide();
            });

            // Buscar producto
            $('#btnBuscarProducto').click(function () {
                buscarProducto();
            });

            $('#buscarProducto').on('keypress', function (e) {
                if (e.which == 13) {
                    buscarProducto();
                }
            });

            function buscarProducto() {
                var query = $('#buscarProducto').val();
                if (query.length > 1) {
                    $.ajax({
                        url: 'funciones/compras.php',
                        type: 'POST',
                        data: {
                            funcion: 'buscarProducto',
                            query: query
                        },
                        success: function (response) {
                            var resultados = JSON.parse(response);
                            var html = '';

                            if (resultados.length > 0) {
                                resultados.forEach(function (producto) {
                                    html += `<div class="search-item d-flex align-items-center" 
                            data-id="${producto.id_productos}" 
                            data-codigo="${producto.codigo_barras}" 
                            data-nombre="${producto.nombre}" 
                            data-precio="${producto.precio}"
                            data-precio-venta="${producto.precio_venta}"> <!-- Nuevo: agregar precio_venta -->
                            <div>
                                <strong>${producto.codigo_barras}</strong><br>
                                ${producto.nombre} - Compra: $${producto.precio} | Venta: $${producto.precio_venta}
                            </div>
                        </div>`;
                                });
                                $('#resultadosProducto').html(html).show();
                            } else {
                                $('#resultadosProducto').html('<div class="search-item">No se encontraron resultados</div>').show();
                            }
                        }
                    });
                }
            }
            $('#precioProducto').on('change', function () {
                var precioUnitario = parseFloat($(this).val()) || 0;
                var precioVenta = precioUnitario * 1.30; // 30% de margen
                // Puedes mostrar esto en un campo oculto o directamente en la tabla
                $('#precioVentaProducto').val(precioVenta.toFixed(2));
            });
            // Seleccionar producto de los resultados
            // Modificar la función de selección de producto
            $(document).on('click', '#resultadosProducto .search-item', function () {
                var codigo = $(this).data('codigo');
                var nombre = $(this).data('nombre');
                var precioCompra = $(this).data('precio');
                var precioVenta = $(this).data('precio-venta'); // Nuevo: obtener precio_venta
                var id = $(this).data('id');

                $('#buscarProducto').val(`${codigo} - ${nombre}`);
                $('#precioProducto').val(precioCompra);
                $('#precioVentaProducto').val(precioVenta); // Autocompletar precio de venta
                $('#resultadosProducto').hide();

                // Guardar datos temporales en el input
                $('#buscarProducto').data('producto', {
                    id: id,
                    codigo: codigo,
                    nombre: nombre,
                    precio: precioCompra,
                    precio_venta: precioVenta // Guardar también el precio de venta
                });
            });

            // Agregar producto a la tabla
            $('#btnAgregarProducto').click(function () {
                var producto = $('#buscarProducto').data('producto');
                var cantidad = parseInt($('#cantidadProducto').val()) || 1;
                var precioUnitario = parseFloat($('#precioProducto').val()) || 0;
                var precioVenta = parseFloat($('#precioVentaProducto').val()) || 0;

                if (!producto || !producto.id) {
                    Swal.fire('Error', 'Debes seleccionar un producto primero', 'error');
                    return;
                }

                if (precioVenta <= 0) {
                    Swal.fire('Error', 'El precio de venta debe ser mayor a cero', 'error');
                    return;
                }

                if (cantidad <= 0) {
                    Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'error');
                    return;
                }

                if (precioUnitario <= 0) {
                    Swal.fire('Error', 'El precio unitario debe ser mayor a cero', 'error');
                    return;
                }

                // Verificar si el producto ya está en la lista
                var existe = false;
                $('#tablaDetalleCompra tr').each(function () {
                    if ($(this).data('id') == producto.id) {
                        existe = true;
                        // Actualizar cantidad y subtotal
                        var nuevaCantidad = parseInt($(this).find('.cantidad-producto').val()) + cantidad;
                        $(this).find('.cantidad-producto').val(nuevaCantidad);

                        var subtotal = nuevaCantidad * precioUnitario;
                        $(this).find('.subtotal-producto').text('$' + subtotal.toFixed(2));

                        // Actualizar precio de venta en el data attribute
                        $(this).data('precio-venta', precioVenta);

                        return false; // Salir del each
                    }
                });

                if (!existe) {
                    // Agregar nuevo producto a la tabla
                    var subtotal = cantidad * precioUnitario;
                    var row = `
        <tr data-id="${producto.id}" data-precio="${precioUnitario}" data-precio-venta="${precioVenta}">
            <td>${producto.codigo}</td>
            <td>${producto.nombre}</td>
            <td><input type="number" class="form-control cantidad-producto" value="${cantidad}" min="1"></td>
            <td>$${precioUnitario.toFixed(2)}</td>
            <td class="subtotal-producto">$${subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm btn-eliminar-producto">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        `;
                    $('#tablaDetalleCompra').append(row);
                }

                // Actualizar total
                actualizarTotal();

                // Limpiar campos
                $('#buscarProducto').val('').removeData('producto');
                $('#cantidadProducto').val(1);
                $('#precioProducto').val('');
                $('#precioVentaProducto').val('');
                $('#resultadosProducto').hide();
            });
            // Actualizar cantidades y total
            $(document).on('change', '.cantidad-producto', function () {
                var cantidad = parseInt($(this).val()) || 1;
                var precio = parseFloat($(this).closest('tr').data('precio')) || 0;
                var subtotal = cantidad * precio;

                $(this).closest('tr').find('.subtotal-producto').text('$' + subtotal.toFixed(2));
                actualizarTotal();
            });

            // Eliminar producto
            $(document).on('click', '.btn-eliminar-producto', function () {
                $(this).closest('tr').remove();
                actualizarTotal();
            });

            // Función para actualizar el total
            function actualizarTotal() {
                var total = 0;

                $('#tablaDetalleCompra tr').each(function () {
                    var subtotal = parseFloat($(this).find('.subtotal-producto').text().replace('$', '')) || 0;
                    total += subtotal;
                });

                $('#totalCompra').text('$' + total.toFixed(2));
            }

            // Guardar compra
            $('#btnGuardarCompra').click(function () {
                var idProveedor = $('#idProveedor').val();
                var productos = [];

                // Validar proveedor
                if (!idProveedor) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debes seleccionar un proveedor',
                        confirmButtonColor: '#2973B2'
                    });
                    return;
                }

                // Validar productos
                if ($('#tablaDetalleCompra tr').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debes agregar al menos un producto',
                        confirmButtonColor: '#2973B2'
                    });
                    return;
                }

                // Recolectar datos de productos
                $('#tablaDetalleCompra tr').each(function () {
                    var producto = {
                        id: $(this).data('id'),
                        codigo: $(this).find('td:eq(0)').text(),
                        nombre: $(this).find('td:eq(1)').text(),
                        cantidad: parseInt($(this).find('.cantidad-producto').val()),
                        precio: parseFloat($(this).data('precio')),
                        subtotal: parseFloat($(this).find('.subtotal-producto').text().replace('$', ''))
                    };

                    productos.push(producto);
                });

                // Calcular total
                var total = parseFloat($('#totalCompra').text().replace('$', '')) || 0;

                // Mostrar modal de tipo de pago
                $('#modalNuevaCompra').modal('hide');
                $('#tipoPagoModal').modal('show');

                // Configurar botones del modal de pago
                $('#btnContado').off('click').on('click', function () {
                    $('#tipoPagoModal').modal('hide');
                    guardarCompra(idProveedor, productos, total, 'contado');
                });

                $('#btnCredito').off('click').on('click', async function () {
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
                    guardarCompra(idProveedor, productos, total, 'credito', diasCredito);
                });
            });

            // Función para guardar la compra
            // Función para guardar la compra
            function guardarCompra(idProveedor, productos, total, tipoPago, diasCredito = 0) {
                // Mostrar loading
                Swal.fire({
                    title: 'Guardando compra...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Preparar datos para enviar en formato FormData
                var formData = new FormData();
                formData.append('funcion', 'Guardar_Compra');
                formData.append('idproveedores', idProveedor);
                formData.append('nombre_proveedor', $('#nombreProveedor').text());
                formData.append('total', total);
                formData.append('tipo_pago', tipoPago);
                formData.append('dias_credito', diasCredito);

                // Agregar productos
                $('#tablaDetalleCompra tr').each(function (index) {
                    var producto = {
                        id: $(this).data('id'),
                        precio_unitario: parseFloat($(this).data('precio')),
                        precio_venta: parseFloat($(this).data('precio-venta')),
                        cantidad: parseInt($(this).find('.cantidad-producto').val())
                    };

                    formData.append(`Detalle[${index}][id_productos]`, producto.id);
                    formData.append(`Detalle[${index}][precio_unitario]`, producto.precio_unitario);
                    formData.append(`Detalle[${index}][precio_venta]`, producto.precio_venta);
                    formData.append(`Detalle[${index}][cantidad]`, producto.cantidad);
                });

                // Enviar datos al servidor
                $.ajax({
                    url: 'funciones/compras.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        Swal.close();
                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Compra guardada!',
                                text: `Compra registrada con folio ${response.folio}`,
                                confirmButtonColor: '#2973B2'
                            }).then(() => {
                                // Limpiar formulario
                                cargarComprasDelDia();
                                $('#modalNuevaCompra').modal('hide');
                                $('#tablaDetalleCompra').empty();
                                $('#totalCompra').text('$0.00');
                                $('#idProveedor').val('');
                                $('#buscarProveedor').val('');
                                $('#nombreProveedor').text('');
                                $('#contactoProveedor').text('');
                                $('#infoProveedor').hide();
                                $('#precioVentaProducto').val('');

                                // Actualizar folio
                                $.get('funciones/compras.php?funcion=getFolio', function (data) {
                                    $('#folio').val(data);
                                });
                            });
                        } else {
                            let errorMsg = response && response.message ? response.message : 'Respuesta inesperada del servidor';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg,
                                confirmButtonColor: '#2973B2'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        let errorMsg = 'Error en la comunicación: ';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg += xhr.responseJSON.message;
                        } else {
                            errorMsg += error + ' (HTTP ' + xhr.status + ')';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMsg + '<br><br><small>Verifica la consola para más detalles</small>',
                            confirmButtonColor: '#2973B2'
                        });
                        console.error("Error completo:", xhr.responseText);
                    }
                });
            }

            // Ocultar resultados al hacer clic fuera
            $(document).click(function (e) {
                if (!$(e.target).closest('.search-container').length) {
                    $('#resultadosProveedor').hide();
                }
                if (!$(e.target).closest('#buscarProducto').length && !$(e.target).closest('#resultadosProducto').length) {
                    $('#resultadosProducto').hide();
                }
            });

            // Funciones auxiliares
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
</html>