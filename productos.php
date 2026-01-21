<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productos</title>

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

  /* Estilos para las existencias (fondo y texto) */
  .existencias-rojo {
    background-color: rgb(244, 165, 165) !important;
    /* Rojo claro */
    color: #cc0000 !important;
    /* Rojo oscuro para el texto */
  }

  .existencias-amarillo {
    background-color: rgb(251, 251, 182) !important;
    /* Amarillo claro */
    color: rgb(107, 80, 0) !important;
    /* Amarillo oscuro para el texto */
  }

  .existencias-verde {
    background-color: rgb(156, 255, 156) !important;
    /* Verde claro */
    color: #006600 !important;
    /* Verde oscuro para el texto */
  }

  .banner {
    background-color: #2973B2;
    /* Azul intenso */
    color: white;
    padding: 5px;
  }
</style>

<body>
  <div class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="titulo_modal">Alta de Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="cerrar"></button>
        </div>
        <div class="modal-body" id="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn-custom invisible" id="Guardar_Edita">Editar Producto</button>
          <button type="button" class="btn-custom" id="Guardar_Nuevo">Agregar Producto</button>
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
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-box-seam-fill"
            viewBox="0 0 16 16">
            <path
              d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184a.75.75 0 0 1 1.114 0zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z" />
          </svg> Productos
        </h1>
      </div>
      <div class="col-10">
      </div>
      <div class="col-2 text-center mt-3">
        <button class="btn-custom w-50" id="nuevo" data-bs-toggle="modal" data-bs-target="#modal"> Nuevo <svg
            xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle"
            viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
            <path
              d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
          </svg></button>
      </div>
      <div class="col-12 text-center">
        <table id="productoTable" class="table table-striped table-sm compact">
          <thead>
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Código de Barras</th>
              <th scope="col">Precio Unitario</th>
              <th scope="col">Precio de Venta</th>
              <th scope="col">Existencias</th>
              <th scope="col">Unidad de Medida</th>
              <th scope="col">Categoría</th>
              <th scope="col">Opciones</th>
            </tr>
          </thead>
          <tbody id="resultados_productos"></tbody>
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

      // Modificar la validación del modal para incluir precio_venta
      $(document).on("click", "#Guardar_Nuevo", function () {
        if ($("#codigo_barras").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Código de Barras es obligatorio' });
          $("#codigo_barras").focus();
          return false;
        }
        if ($("#nombre").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Nombre es obligatorio' });
          $("#nombre").focus();
          return false;
        }
        if ($("#precio").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Precio Unitario es obligatorio' });
          $("#precio").focus();
          return false;
        }
        if ($("#precio_venta").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Precio de Venta es obligatorio' });
          $("#precio_venta").focus();
          return false;
        }
        if ($("#existencias").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Existencias es obligatorio' });
          $("#existencias").focus();
          return false;
        }
        if ($("#unidad_medida").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Unidad de Medida es obligatorio' });
          $("#unidad_medida").focus();
          return false;
        }
        if ($("#categoria").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Categoría es obligatorio' });
          $("#categoria").focus();
          return false;
        }

        $.ajax({
          url: 'funciones/productos.php',
          type: 'POST',
          data: {
            funcion: 'Guardar',
            codigo_barras: $("#codigo_barras").val(),
            nombre: $("#nombre").val(),
            precio: $("#precio").val(),
            precio_venta: $("#precio_venta").val(),
            existencias: $("#existencias").val(),
            unidad_medida: $("#unidad_medida option:selected").val(),
            categoria: $("#categoria option:selected").val()
          },
          success: function (response) {
            if (response == "El código de barras ya existe") {
              Swal.fire({ icon: 'error', title: 'Error', text: response });
              return false;
            } else {
              $("#cerrar").click();
              $('#modal').modal('hide');
              Swal.fire({ icon: 'success', title: 'Éxito', text: 'Producto agregado correctamente', timer: 1000, showConfirmButton: false });
              setTimeout(function () { window.location.reload(); }, 1000);
            }
          }
        });
      });

      // Función para guardar la edición de un producto
      $(document).on("click", "#Guardar_Edita", function () {
        var idregistros = $(this).attr('idregistros');
        if ($("#codigo_barras").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Código de Barras es obligatorio' });
          $("#codigo_barras").focus();
          return false;
        }
        if ($("#nombre").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Nombre es obligatorio' });
          $("#nombre").focus();
          return false;
        }
        if ($("#precio").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Precio Unitario es obligatorio' });
          $("#precio").focus();
          return false;
        }
        if ($("#precio_venta").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Precio de Venta es obligatorio' });
          $("#precio_venta").focus();
          return false;
        }
        if ($("#existencias").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Existencias es obligatorio' });
          $("#existencias").focus();
          return false;
        }
        if ($("#unidad_medida").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Unidad de Medida es obligatorio' });
          $("#unidad_medida").focus();
          return false;
        }
        if ($("#categoria").val() == "") {
          Swal.fire({ icon: 'error', title: 'Error', text: 'El campo Categoría es obligatorio' });
          $("#categoria").focus();
          return false;
        }

        $.ajax({
          url: 'funciones/productos.php',
          type: 'POST',
          data: {
            funcion: 'Editar',
            codigo_barras: $("#codigo_barras").val(),
            nombre: $("#nombre").val(),
            precio: $("#precio").val(),
            precio_venta: $("#precio_venta").val(),
            existencias: $("#existencias").val(),
            unidad_medida: $("#unidad_medida option:selected").val(),
            categoria: $("#categoria option:selected").val(),
            idregistros: idregistros
          },
          success: function (response) {
            $("#cerrar").click();
            Swal.fire({ icon: 'success', title: 'Éxito', text: 'Producto editado correctamente', timer: 1000, showConfirmButton: false });
            setTimeout(function () { window.location.reload(); }, 1000);
          }
        });
      });

      // Función para abrir el modal de nuevo producto
      $(document).on("click", "#nuevo", function () {
        Modal("Nuevo", 0);
        $("#Guardar_Nuevo").show();
        $("#Guardar_Edita").hide();
        $("#titulo_modal").text("Alta de Producto");
      });

      // Función para abrir el modal de editar producto
      $(document).on("click", ".editar", function () {
        var idregistros = $(this).attr('idregistros');
        Modal("Editar", idregistros);
        $("#Guardar_Nuevo").hide();
        $("#Guardar_Edita").show().removeClass('invisible').attr('idregistros', idregistros);
        $("#titulo_modal").text("Editar Producto");
        $('#modal').modal('show');
      });

      // Función para eliminar un producto
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
              url: 'funciones/productos.php',
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
                  text: 'Producto eliminado correctamente',
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
              text: 'El producto no fue eliminado',
            });
          }
        });
      });

      // Función para cargar el modal
      function Modal(tipo, id) {
        $.ajax({
          url: 'funciones/productos.php',
          type: 'POST',
          data: { funcion: 'Modal', tipo: tipo, id: id },
          success: function (response) {
            $('#modal-body').html(response);
          }
        });
      }

      function Tabla() {
        $.ajax({
          url: 'funciones/productos.php',
          type: 'POST',
          data: { funcion: 'Tabla' },
          success: function (response) {
            $('#resultados_productos').html(response);
            tabledata();
          }
        });
      }

      // Función para inicializar DataTable y aplicar estilos
      function tabledata() {
        var table = new DataTable('#productoTable', {
          language: {
            info: 'Mostrando pagina _PAGE_ de _PAGES_',
            infoEmpty: 'Sin Resultados',
            infoFiltered: '(filtro de _MAX_ resultados)',
            lengthMenu: 'Mostrando _MENU_ resultados por pagina',
            zeroRecords: 'No se encontraron resultados',
          },
          drawCallback: function (settings) {
            // Aplicar estilos dinámicos a la columna "Existencias" (columna 4, índice 4)
            $('#productoTable tbody tr').each(function () {
              var existenciasCell = $(this).find('td:eq(4)'); // Columna 4 (Existencias)
              var existencias = parseInt(existenciasCell.text().trim()); // Convertir a número

              if (!isNaN(existencias)) {
                // Limpiar clases anteriores
                existenciasCell.removeClass('existencias-rojo existencias-amarillo existencias-verde');

                // Aplicar clases según el valor
                if (existencias <= 5) {  // 0-5 rojo
                  existenciasCell.addClass('existencias-rojo');
                } else if (existencias > 5 && existencias <= 10) {  // 5-10 amarillo
                  existenciasCell.addClass('existencias-amarillo');
                } else {  // más de 10 verde
                  existenciasCell.addClass('existencias-verde');
                }
              }
            });
          }
        });
      }
    });
  </script>
</body>

</html>