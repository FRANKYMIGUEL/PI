<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


</head>
<section class="text-center text-lg-start">
    <style>
        body {
            background-color: #2973B2 !important;
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

        .btn-custom:hover {
            background-color: #1f5a8e;
        }

        .bg-body-tertiary {
            background-color: #F2EFE7 !important;
        }
    </style>

    <body>
        <div class="container py-4">
            <div class="row g-0 align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <div class="card cascading-right bg-body-tertiary" style="backdrop-filter: blur(30px);">
                        <div class="card-body p-5 shadow-5 text-center">
                            <h1 class="fw-bold mb-5">BIENVENIDO</h1>

                            <div class=mb-3>
                                <h5><label for="username" class="form-label">Usuario</label></h5>
                                <input type="text" class="form-control" id="usuario" name="username"
                                    placeholder="Ingresa tu usuario" required>
                            </div>
                            <div class="mb-3">
                                <h5><label for="contrasena" class="form-label">Contraseña</label></h5>
                                <input type="password" class="form-control" id="contrasena" name="password"
                                    placeholder="Ingresa tu contraseña" required>
                            </div>
                            <button class="btn-custom w-100" id="iniciar">INGRESAR</button>

                        </div>
                    </div>
                </div>

                <div class="col mb-2 mb-lg-0">
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0">
                    <img src="imagenes/login1.webp" class="w-100 rounded-4 shadow-4" alt="imagen" />
                </div>
            </div>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {

            $(document).on('click', '#iniciar', function (event) {
                if ($("#usuario").val() == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Ingresa un nombre de usuario.'
                    });
                    $("#usuario").focus();
                    return false;
                }
                if ($("#contrasena").val() == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Ingresa una contraseña.'
                    });
                    $("#contrasena").focus();
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "validar.php",
                    data: ({
                        funcion: "iniciar",
                        usuario: $("#usuario").val(),
                        contrasena: $("#contrasena").val()
                    }),
                    dataType: "html",
                    success: function (msg) {
                        console.log(msg);
                        if (msg === "error") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Usuario o contraseña incorrectos.'
                            });
                            $('#usuario').val("");
                            $('#contrasena').val("");
                        } else {
                            console.log(msg);
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Inicio de sesión exitoso.',
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => {
                                window.location.href = "ventas.php";
                            });
                        }

                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error al procesar la solicitud.'
                        });
                    }
                });
            });

        });
    </script>

</section>