<!DOCTYPE html>
<html lang="en">

<head> <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>
<style>
    #modal .modal-title {
        color: black;
    }

    #modal .modal-header,
    #modal .modal-footer {
        background-color: #F5F5F5;
        color: white;
    }

    body {
        background-color: #F2EFE7 !important;
    }

    .btn-custom {
        background-color: #2973B2;
        color: white;
        border-radius: 5px;
        padding: 10px;
        font-size: 18px;
        width: 200px;
        border: none;
    }

    .btn-custom1 {
        background-color: rgb(178, 41, 41);
        color: white;
        border-radius: 5px;
        padding: 10px;
        font-size: 18px;
        width: 200px;
        border: none;
    }

    .btn-custom:hover {
        background-color: #1f5a8e;
        /* Cambia de color al pasar el mouse */
    }

    .btn-custom1:hover {
        background-color: rgb(204, 27, 27);
        /* Cambia de color al pasar el mouse */
    }
</style>

<body>
    <div class="modal" tabindex="-1" id="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo_modal">Alta de Proveedores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="cerrar"></button>
                </div>
                <div class="modal-body" id="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-custom invisible" id="Guardar_Edita">Editar Proveedor</button>
                    <button type="button" class="btn-custom" id="Guardar_Nuevo">Agregar Proveedor</button>
                    <button type="button" class="btn-custom1" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <?
    include "menu.php";
    ?>
    <div class="container-fluid">

        <div class="row">
            <div class="col-10 text-center">
                <h1 class="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
                        class="bi bi-truck" viewBox="0 0 16 16">
                        <path
                            d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                    </svg> Proveedores
                </h1>
            </div>
            <div class="col-2 text-center mt-3">
                <button class="btn-custom w-50" id="nuevo" data-bs-toggle="modal" data-bs-target="#modal"> Nuevo <svg
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg></button>
            </div>
            <div class="col-12 text-center">
                <table id="proveedoresTable" class="table table-striped table-sm compact">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Correo Electrónico</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultados_proveedores">
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                Tabla();

                $(document).on("click", "#Guardar_Nuevo", function () {
                    if ($("#nombre").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Nombre es obligatorio',
                        });
                        $("#nombre").focus();
                        return false;
                    }
                    if ($("#direccion").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Dirección es obligatorio',
                        });
                        $("#direccion").focus();
                        return false;
                    }
                    if ($("#correo_electronico").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Correo Electrónico es obligatorio',
                        });
                        $("#correo_electronico").focus();
                        return false;
                    }
                    if ($("#telefono").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Teléfono es obligatorio',
                        });
                        $("#telefono").focus();
                        return false;
                    }

                    $.ajax({
                        url: 'funciones/proveedores.php',
                        type: 'POST',
                        data: {
                            funcion: 'Guardar',
                            nombre: $("#nombre").val(),
                            direccion: $("#direccion").val(),
                            correo_electronico: $("#correo_electronico").val(),
                            telefono: $("#telefono").val(),
                        },
                        success: function (response) {
                            if (response == "El proveedor ya existe") {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response,
                                });
                                return false;
                            } else {
                                // Cerrar el modal
                                $("#cerrar").click();
                                $('#modal').modal('hide');

                                // Mostrar mensaje de éxito con temporizador
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: 'Proveedor agregado correctamente',
                                    timer: 1000, // Duración de 1 segundo (1000 ms)
                                    showConfirmButton: false, // No mostrar botón de confirmación
                                });

                                // Recargar la página después de que el mensaje se cierre
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000); // Recargar después de 1 segundo
                            }
                        }
                    });
                });

                $(document).on("click", "#Guardar_Edita", function () {
                    var idregistros = $(this).attr('idregistros');
                    if ($("#nombre").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Nombre es obligatorio',
                        });
                        $("#nombre").focus();
                        return false;
                    }
                    if ($("#direccion").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Dirección es obligatorio',
                        });
                        $("#direccion").focus();
                        return false;
                    }
                    if ($("#correo_electronico").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Correo Electrónico es obligatorio',
                        });
                        $("#correo_electronico").focus();
                        return false;
                    }
                    if ($("#telefono").val() == "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El campo Teléfono es obligatorio',
                        });
                        $("#telefono").focus();
                        return false;
                    }

                    $.ajax({
                        url: 'funciones/proveedores.php',
                        type: 'POST',
                        data: {
                            funcion: 'Editar',
                            nombre: $("#nombre").val(),
                            direccion: $("#direccion").val(),
                            correo_electronico: $("#correo_electronico").val(),
                            telefono: $("#telefono").val(),
                            idregistros: idregistros
                        },
                        success: function (response) {
                            console.log(response);

                            // Cerrar el modal
                            $("#cerrar").click();

                            // Mostrar mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Proveedor editado correctamente',
                                timer: 1000, // 5 segundos
                                showConfirmButton: false, // No mostrar botón de confirmación
                            });

                            // Recargar la página después de que el mensaje se cierre
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000); // Recargar después de 5 segundos
                        }
                    });
                });

                // Función para abrir el modal de nuevo proveedor
                $(document).on("click", "#nuevo", function () {
                    Modal("Nuevo", 0);
                    $("#Guardar_Nuevo").show();
                    $("#Guardar_Edita").hide();
                    $("#titulo_modal").text("Alta de Proveedor");
                });

                // Función para abrir el modal de editar proveedor
                $(document).on("click", ".editar", function () {
                    var idregistros = $(this).attr('idregistros');
                    Modal("Editar", idregistros);
                    $("#Guardar_Nuevo").hide();
                    $("#Guardar_Edita").show().removeClass('invisible').attr('idregistros', idregistros);
                    $("#titulo_modal").text("Editar Proveedor");
                    $('#modal').modal('show');
                });

                // Función para eliminar un proveedor
                $(document).on("click", ".eliminar", function () {
                    var idregistros = $(this).attr('idregistros');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Desea eliminar este registro?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'funciones/proveedores.php',
                                type: 'POST',
                                data: { funcion: 'Eliminar', idregistros: idregistros },
                                success: function (response) {
                                    console.log(response);

                                    // Cerrar el modal
                                    $("#cerrar").click();

                                    // Mostrar mensaje de éxito
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Proveedor eliminado correctamente',
                                        timer: 1000, // 5 segundos
                                        showConfirmButton: false, // No mostrar botón de confirmación
                                    });

                                    // Recargar la página después de que el mensaje se cierre
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1000); // Recargar después de 5 segundos
                                }
                            });
                        }
                    });
                });

                // Función para cargar el modal
                function Modal(tipo, id) {
                    $.ajax({
                        url: 'funciones/proveedores.php',
                        type: 'POST',
                        data: { funcion: 'Modal', tipo: tipo, id: id },
                        success: function (response) {
                            $('#modal-body').html(response);
                        }
                    });
                }

                // Función para cargar la tabla de proveedores
                function Tabla() {
                    $.ajax({
                        url: 'funciones/proveedores.php',
                        type: 'POST',
                        data: { funcion: 'Tabla' },
                        success: function (response) {
                            $('#resultados_proveedores').html(response);
                            tabledata();
                        }
                    });
                }


                // Función para inicializar DataTables
                function tabledata() {
                    new DataTable('#proveedoresTable', {
                        language: {
                            info: 'Mostrando página _PAGE_ de _PAGES_',
                            infoEmpty: 'Sin resultados',
                            infoFiltered: '(filtrado de _MAX_ registros)',
                            lengthMenu: 'Mostrar _MENU_ registros por página',
                            zeroRecords: 'No se encontraron resultados',
                        }
                    });
                }

            });
        </script>
</body>

</html>