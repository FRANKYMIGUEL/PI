<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categorias</title>

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

    .banner {
      background-color: #2973B2;
      /* Azul intenso */
      color: white;
      padding: 5px;
    }
  </style>
</head>

<body>
  <div class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulo_modal1">Alta de categoria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="cerrar"></button>
        </div>
        <div class="modal-body" id="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn-custom invisible" id="Guardar_Edita">Editar categoria</button>
          <button type="button" class="btn-custom" id="Guardar_Nuevo">Agregar categoria</button>
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
      <div class="col-12 text-center banner">
        <h1 class="">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-tag-fill"
            viewBox="0 0 16 16">
            <path
              d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
          </svg> Categoria
        </h1>
      </div>
      <div class="col-10">
      </div>
      <div class="col-2 text-center mt-5">
        <button class="btn-custom w-50" id="nuevo" data-bs-toggle="modal" data-bs-target="#modal"> Nuevo <svg
            xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle"
            viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
            <path
              d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
          </svg></button>
      </div>
      <table id="categoriaTable" class="table table-striped table-sm compact">
        <thead>
          <tr>
            <th scope="col">Nombre</th>
            <th scope="col">Opciones</th>
          </tr>
        </thead>
        <tbody id="resultados_productos"></tbody>
      </table>
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

        $.ajax({
          url: 'funciones/categorias.php',
          type: 'POST',
          data: {
            funcion: 'Guardar',
            nombre: $("#nombre").val()
          },
          success: function (response) {
            if (response == "El codigo ya existe") {
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
                text: 'Categoria agregado correctamente',
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
        $.ajax({
          url: 'funciones/categorias.php',
          type: 'POST',
          data: {
            funcion: 'Editar',
            nombre: $("#nombre").val(),
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
              text: 'Categoria editado correctamente',
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

      $(document).on("click", "#nuevo", function () {
        Modal("Nuevo", 0);
        $("#Guardar_Nuevo").show();
        $("#Guardar_Edita").hide();
        $("#titulo_modal").text("Alta de Producto");
      });

      $(document).on("click", ".editar", function () {
        var idregistros = $(this).attr('idregistros');
        Modal("Editar", idregistros);
        $("#Guardar_Nuevo").hide();
        $("#Guardar_Edita").show().removeClass('invisible').attr('idregistros', idregistros);
        $("#titulo_modal").text("Editar Producto");
        $('#modal').modal('show');
      });

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
              url: 'funciones/categorias.php',
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
                  text: 'Categoria eliminado correctamente',
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
              text: 'La categoría no fue eliminada',
            });
          }
        });
      });

      function Modal(tipo, id) {
        $.ajax({
          url: 'funciones/categorias.php',
          type: 'POST',
          data: { funcion: 'Modal', tipo: tipo, id: id },
          success: function (response) {
            console.log(response);
            $('#modal-body').html(response);
          }
        });
      }

      function Tabla() {
        $.ajax({
          url: 'funciones/categorias.php',
          type: 'POST',
          data: { funcion: 'Tabla' },
          success: function (response) {
            console.log(response);
            $('#resultados_productos').html(response);
            tabledata();
          }
        });
      }

      function tabledata() {
        new DataTable('#categoriaTable', {
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