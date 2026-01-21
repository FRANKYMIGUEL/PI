<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    }

    .btn-custom1:hover {
        background-color: rgb(204, 27, 27);
    }
</style>

<body>
    <div class="modal" tabindex="-1" id="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo_modal">Alta de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="cerrar"></button>
                </div>
                <div class="modal-body" id="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn-custom invisible" id="Guardar_Edita">Editar Usuario</button>
                    <button type="button" class="btn-custom" id="Guardar_Nuevo">Agregar Usuario</button>
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
                        class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                        <path
                            d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z" />
                    </svg> Usuarios
                </h1>
            </div>
            <div class="col-2 text-center mt-5">
                <button class="btn-custom w-50" id="nuevo" data-bs-toggle="modal" data-bs-target="#modal"> Nuevo <svg
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg></button>
            </div>
            <div class="col-12 text-center">
                <table id="usuariosTable" class="table table-striped table-sm compact">
                    <thead>
                        <tr>
                            <th scope="col">Usuario</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultados_usuarios"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <!-- jQuery -->
    <script src="js/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            Tabla();

            // Función para guardar un nuevo usuario
            $(document).on("click", "#Guardar_Nuevo", function () {
                if ($("#usuario").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Usuario es obligatorio',
                    });
                    $("#usuario").focus();
                    return false;
                }
                if ($("#contrasena").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Contraseña es obligatorio',
                    });
                    $("#contrasena").focus();
                    return false;
                }
                if ($("#nombre").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Nombre es obligatorio',
                    });
                    $("#nombre").focus();
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
                if ($("#id_rol").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Rol es obligatorio',
                    });
                    $("#id_rol").focus();
                    return false;
                }

                $.ajax({
                    url: 'funciones/usuarios.php',
                    type: 'POST',
                    data: {
                        funcion: 'Guardar',
                        usuario: $("#usuario").val(),
                        nombre: $("#nombre").val(),
                        telefono: $("#telefono").val(),
                        contrasena: $("#contrasena").val(),
                        id_rol: $("#id_rol option:selected").val()
                    },
                    success: function (response) {
                        if (response == "El usuario ya existe") {
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
                                text: 'Usuario agregado correctamente',
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

            // Función para guardar la edición de un usuario
            $(document).on("click", "#Guardar_Edita", function () {
                var idregistros = $(this).attr('idregistros');

                if ($("#usuario").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Usuario es obligatorio',
                    });
                    $("#usuario").focus();
                    return false;
                }
                if ($("#contrasena").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Contraseña es obligatorio',
                    });
                    $("#contrasena").focus();
                    return false;
                }
                if ($("#nombre").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Nombre es obligatorio',
                    });
                    $("#nombre").focus();
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
                if ($("#id_rol").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Rol es obligatorio',
                    });
                    $("#id_rol").focus();
                    return false;
                }

                $.ajax({
                    url: 'funciones/usuarios.php',
                    type: 'POST',
                    data: {
                        funcion: 'Editar',
                        usuario: $("#usuario").val(),
                        nombre: $("#nombre").val(),
                        telefono: $("#telefono").val(),
                        id_rol: $("#id_rol option:selected").val(),
                        idregistros: idregistros,
                        contrasena: $("#contrasena").val() // Solo se actualizará si el usuario ingresa una nueva contraseña
                    },
                    success: function (response) {
                        console.log(response);

                        // Cerrar el modal
                        $("#cerrar").click();

                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Cliente editado correctamente',
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

            // Función para abrir el modal de nuevo usuario
            $(document).on("click", "#nuevo", function () {
                Modal("Nuevo", 0);
                $("#Guardar_Nuevo").show();
                $("#Guardar_Edita").hide();
                $("#titulo_modal").text("Alta de Usuarios");
            });

            // Función para abrir el modal de editar usuario
            $(document).on("click", ".editar", function () {
                var idregistros = $(this).attr('idregistros');
                Modal("Editar", idregistros);
                $("#Guardar_Nuevo").hide();
                $("#Guardar_Edita").show().removeClass('invisible').attr('idregistros', idregistros);
                $("#titulo_modal").text("Editar Usuario");
                $('#modal').modal('show');
            });

            // Función para eliminar un usuario
            $(document).on("click", ".eliminar", function () {
                var idregistros = $(this).attr('idregistros');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'funciones/usuarios.php',
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
                                    text: 'Usuario eliminado correctamente',
                                    timer: 1000, // 5 segundos
                                    showConfirmButton: false, // No mostrar botón de confirmación
                                });

                                // Recargar la página después de que el mensaje se cierre
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000); // Recargar después de 5 segundos
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Operación cancelada',
                            text: 'El usuario no fue eliminado',
                        });
                    }
                });
            });

            // Función para cargar el modal
            function Modal(tipo, id) {
                $.ajax({
                    url: 'funciones/usuarios.php',
                    type: 'POST',
                    data: { funcion: 'Modal', tipo: tipo, id: id },
                    success: function (response) {
                        $('#modal-body').html(response);
                    }
                });
            }

            // Función para cargar la tabla de usuarios
            function Tabla() {
                $.ajax({
                    url: 'funciones/usuarios.php',
                    type: 'POST',
                    data: { funcion: 'Tabla' },
                    success: function (response) {
                        $('#resultados_usuarios').html(response);
                        tabledata();
                    }
                });
            }

            // Función para inicializar DataTable
            function tabledata() {
                new DataTable('#usuariosTable', {
                    language: {
                        info: 'Mostrando pagina _PAGE_ de _PAGES_',
                        infoEmpty: 'Sin Resultados',
                        infoFiltered: '(filtro de _MAX_ resultados)',
                        lengthMenu: 'Mostrando _MENU_ resultados por pagina',
                        zeroRecords: 'No se encontraron resultados',
                    }
                });
            }
        });
    </script>
</body>

</html>