<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .dropdown-item.text-danger {

      color: red !important;
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

    .navbar-nav .nav-link,
    .navbar-brand,
    .dropdown-item {
      color: rgb(229, 222, 203) !important;
    }

    .navbar-nav .nav-link:hover,
    .navbar-brand:hover {
      color: rgb(0, 0, 0) !important;
    }

    .navbar-nav .nav-link:hover,
    .navbar-brand,
    .dropdown-item {
      color: rgb(0, 0, 0) !important;
    }

    .navbar {
      background-color: #2973B2 !important;
    }
  </style>
</head>

<body>
  <?php
  include("inc/conectar.php");
  if ($_SESSION['SISTEMA']['id_empleado'] == '') {
    header("Location: login.php");
  }

  if ($_SESSION['SISTEMA']['rol'] == 1) {
    ?>
    <nav class="navbar navbar-expand-lg w-100">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
          aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link fw-bold text-white" href="ventas.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                  class="bi bi-house-door-fill" viewBox="0 0 16 16">
                  <path
                    d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5" />
                </svg>
                Inicio
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold text-white" href="clientes.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                  class="bi bi-people-fill" viewBox="0 0 16 16">
                  <path
                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                </svg>
                Clientes
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="productosm"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" fill="currentColor"
                  class="bi bi-box-seam-fill" viewBox="0 0 16 16">
                  <path
                    d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184a.75.75 0 0 1 1.114 0zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z" />
                </svg>
                Productos
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="productos.php"> <svg xmlns="http://www.w3.org/2000/svg" width="15"
                      height="15" fill="currentColor" class="bi bi-box-seam-fill" viewBox="0 0 16 16">
                      <path
                        d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184a.75.75 0 0 1 1.114 0zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z" />
                    </svg> Productos</a></li>
                <li><a class="dropdown-item" href="categorias.php"><svg xmlns="http://www.w3.org/2000/svg" width="15"
                      height="15" fill="currentColor" class="bi bi-tag-fill" viewBox="0 0 16 16">
                      <path
                        d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                    </svg> Categorías</a></li>
                <li><a class="dropdown-item" href="unidadmedida.php"><svg xmlns="http://www.w3.org/2000/svg" width="15"
                      height="15" fill="currentColor" class="bi bi-rulers" viewBox="0 0 16 16">
                      <path
                        d="M1 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h5v-1H2v-1h4v-1H4v-1h2v-1H2v-1h4V9H4V8h2V7H2V6h4V2h1v4h1V4h1v2h1V2h1v4h1V4h1v2h1V2h1v4h1V1a1 1 0 0 0-1-1z" />
                    </svg> Unidades de medida</a></li>
                <li><a class="dropdown-item" href="proveedores.php"><svg xmlns="http://www.w3.org/2000/svg" width="15"
                      height="15" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                      <path
                        d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                    </svg> Proveedores</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="ventas"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-cash-coin"
                  viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                  <path
                    d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.673h.375v-.78c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.162l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.75c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                  <path
                    d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                  <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                </svg>
                Ventas
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="cuentas_cobrar.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                      <path
                        d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                      <path
                        d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                      <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                    </svg> Cuentas por cobrar</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCorteCaja"><svg
                      xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-clipboard-data-fill" viewBox="0 0 16 16">
                      <path
                        d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z" />
                      <path
                        d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zM10 8a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm4-3a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1" />
                    </svg> Corte de caja</a></li>
                <li><a class="dropdown-item" href="historial_ventas.php"><svg xmlns="http://www.w3.org/2000/svg"
                      width="16" height="16" fill="currentColor" class="bi bi-receipt-cutoff" viewBox="0 0 16 16">
                      <path
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5M11.5 4a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z" />
                      <path
                        d="M2.354.646a.5.5 0 0 0-.801.13l-.5 1A.5.5 0 0 0 1 2v13H.5a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1H15V2a.5.5 0 0 0-.053-.224l-.5-1a.5.5 0 0 0-.8-.13L13 1.293l-.646-.647a.5.5 0 0 0-.708 0L11 1.293l-.646-.647a.5.5 0 0 0-.708 0L9 1.293 8.354.646a.5.5 0 0 0-.708 0L7 1.293 6.354.646a.5.5 0 0 0-.708 0L5 1.293 4.354.646a.5.5 0 0 0-.708 0L3 1.293zm-.217 1.198.51.51a.5.5 0 0 0 .707 0L4 1.707l.646.647a.5.5 0 0 0 .708 0L6 1.707l.646.647a.5.5 0 0 0 .708 0L8 1.707l.646.647a.5.5 0 0 0 .708 0L10 1.707l.646.647a.5.5 0 0 0 .708 0L12 1.707l.646.647a.5.5 0 0 0 .708 0l.509-.51.137.274V15H2V2.118z" />
                    </svg> Historial de ventas</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="compras"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-cart-fill"
                  viewBox="0 0 16 16">
                  <path
                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg>
                Compras
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="compras.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-cart-plus-fill" viewBox="0 0 16 16">
                      <path
                        d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M9 5.5V7h1.5a.5.5 0 0 1 0 1H9v1.5a.5.5 0 0 1-1 0V8H6.5a.5.5 0 0 1 0-1H8V5.5a.5.5 0 0 1 1 0" />
                    </svg> Registrar Compras</a></li>
                <li><a class="dropdown-item" href="cuentas_pagar.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-credit-card-2-front" viewBox="0 0 16 16">
                      <path
                        d="M14 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" />
                      <path
                        d="M2 5.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                    </svg> Cuentas por Pagar</a></li>
                <li><a class="dropdown-item" href="historial_compras.php"><svg xmlns="http://www.w3.org/2000/svg"
                      width="16" height="16" fill="currentColor" class="bi bi-receipt" viewBox="0 0 16 16">
                      <path
                        d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                      <path
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                    </svg> Historial de Compras</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="reportes"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                  class="bi bi-bar-chart-line-fill" viewBox="0 0 16 16">
                  <path
                    d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1z" />
                </svg>
                Reportes
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="ver_ventas.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-calendar-date-fill" viewBox="0 0 16 16">
                      <path
                        d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zm5.402 9.746c.625 0 1.184-.484 1.184-1.18 0-.832-.527-1.23-1.16-1.23-.586 0-1.168.387-1.168 1.21 0 .817.543 1.2 1.144 1.2" />
                      <path
                        d="M16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2m-6.664-1.21c-1.11 0-1.656-.767-1.703-1.407h.683c.043.37.387.82 1.051.82.844 0 1.301-.848 1.305-2.164h-.027c-.153.414-.637.79-1.383.79-.852 0-1.676-.61-1.676-1.77 0-1.137.871-1.809 1.797-1.809 1.172 0 1.953.734 1.953 2.668 0 1.805-.742 2.871-2 2.871zm-2.89-5.435v5.332H5.77V8.079h-.012c-.29.156-.883.52-1.258.777V8.16a13 13 0 0 1 1.313-.805h.632z" />
                    </svg> Ventas por fecha</a></li>
                <li><a class="dropdown-item" href="ingresos_gastos.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                      <path
                        d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015" />
                    </svg> Ingresos y gastos</a></li>
              </ul>
            </li>
          </ul>
        </div>

        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a class="nav-link fw-bold text-dark fs-5 dropdown-toggle" href="#" id="usuario" data-bs-toggle="dropdown">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                <path fill-rule="evenodd"
                  d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
              </svg>
              Mi Cuenta
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="usuarios.php"><svg xmlns="http://www.w3.org/2000/svg" width="15"
                    height="15" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                    <path
                      d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z" />
                  </svg> Usuarios</a></li>
              <li><a class="dropdown-item" href="roles.php"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                    fill="currentColor" class="bi bi-person-lock" viewBox="0 0 16 16">
                    <path
                      d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m0 5.996V14H3s-1 0-1-1 1-4 6-4q.845.002 1.544.107a4.5 4.5 0 0 0-.803.918A11 11 0 0 0 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664zM9 13a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1" />
                  </svg> Roles</a></li>
              <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout()"><svg
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                      d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                    <path fill-rule="evenodd"
                      d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                  </svg> Cerrar sesión</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
    <?php
  } else {
    ?>
    <nav class="navbar navbar-expand-lg w-100">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
          aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link fw-bold text-white" href="ventas.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                  class="bi bi-house-door-fill" viewBox="0 0 16 16">
                  <path
                    d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5" />
                </svg>
                Inicio
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold text-white" href="clientes.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                  class="bi bi-people-fill" viewBox="0 0 16 16">
                  <path
                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                </svg>
                Clientes
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="productosm"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" fill="currentColor"
                  class="bi bi-box-seam-fill" viewBox="0 0 16 16">
                  <path
                    d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184a.75.75 0 0 1 1.114 0zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z" />
                </svg>
                Productos
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="productos.php"> <svg xmlns="http://www.w3.org/2000/svg" width="15"
                      height="15" fill="currentColor" class="bi bi-box-seam-fill" viewBox="0 0 16 16">
                      <path
                        d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184a.75.75 0 0 1 1.114 0zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z" />
                    </svg> Productos</a></li>
                <li><a class="dropdown-item" href="categorias.php"><svg xmlns="http://www.w3.org/2000/svg" width="15"
                      height="15" fill="currentColor" class="bi bi-tag-fill" viewBox="0 0 16 16">
                      <path
                        d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                    </svg> Categorías</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="ventas"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-cash-coin"
                  viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                  <path
                    d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.673h.375v-.78c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.162l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.75c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                  <path
                    d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                  <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                </svg>
                Ventas
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="cuentas_cobrar.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                      <path
                        d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                      <path
                        d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                      <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                    </svg> Cuentas por cobrar</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCorteCaja"><svg
                      xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-clipboard-data-fill" viewBox="0 0 16 16">
                      <path
                        d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z" />
                      <path
                        d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zM10 8a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm4-3a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1" />
                    </svg> Corte de caja</a></li>
                <li><a class="dropdown-item" href="historial_ventas.php"><svg xmlns="http://www.w3.org/2000/svg"
                      width="16" height="16" fill="currentColor" class="bi bi-receipt-cutoff" viewBox="0 0 16 16">
                      <path
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5M11.5 4a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z" />
                      <path
                        d="M2.354.646a.5.5 0 0 0-.801.13l-.5 1A.5.5 0 0 0 1 2v13H.5a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1H15V2a.5.5 0 0 0-.053-.224l-.5-1a.5.5 0 0 0-.8-.13L13 1.293l-.646-.647a.5.5 0 0 0-.708 0L11 1.293l-.646-.647a.5.5 0 0 0-.708 0L9 1.293 8.354.646a.5.5 0 0 0-.708 0L7 1.293 6.354.646a.5.5 0 0 0-.708 0L5 1.293 4.354.646a.5.5 0 0 0-.708 0L3 1.293zm-.217 1.198.51.51a.5.5 0 0 0 .707 0L4 1.707l.646.647a.5.5 0 0 0 .708 0L6 1.707l.646.647a.5.5 0 0 0 .708 0L8 1.707l.646.647a.5.5 0 0 0 .708 0L10 1.707l.646.647a.5.5 0 0 0 .708 0L12 1.707l.646.647a.5.5 0 0 0 .708 0l.509-.51.137.274V15H2V2.118z" />
                    </svg> Historial de ventas</a></li>
              </ul>
            </li>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold text-dark dropdown-toggle" href="#" role="button" id="reportes"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                  class="bi bi-bar-chart-line-fill" viewBox="0 0 16 16">
                  <path
                    d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1z" />
                </svg>
                Reportes
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="ver_ventas.php"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                      height="16" fill="currentColor" class="bi bi-calendar-date-fill" viewBox="0 0 16 16">
                      <path
                        d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zm5.402 9.746c.625 0 1.184-.484 1.184-1.18 0-.832-.527-1.23-1.16-1.23-.586 0-1.168.387-1.168 1.21 0 .817.543 1.2 1.144 1.2" />
                      <path
                        d="M16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2m-6.664-1.21c-1.11 0-1.656-.767-1.703-1.407h.683c.043.37.387.82 1.051.82.844 0 1.301-.848 1.305-2.164h-.027c-.153.414-.637.79-1.383.79-.852 0-1.676-.61-1.676-1.77 0-1.137.871-1.809 1.797-1.809 1.172 0 1.953.734 1.953 2.668 0 1.805-.742 2.871-2 2.871zm-2.89-5.435v5.332H5.77V8.079h-.012c-.29.156-.883.52-1.258.777V8.16a13 13 0 0 1 1.313-.805h.632z" />
                    </svg> Ventas por fecha</a></li>
                <li><a class="dropdown-item" href="productos_mas_vendidos.php"><svg xmlns="http://www.w3.org/2000/svg"
                      width="16" height="16" fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                    </svg> Productos más vendidos</a></li>
              </ul>
            </li>
          </ul>
        </div>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a class="nav-link fw-bold text-dark fs-5 dropdown-toggle" href="#" id="usuario" data-bs-toggle="dropdown">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                <path fill-rule="evenodd"
                  d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
              </svg>
              Mi Cuenta
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout()"><svg
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                      d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                    <path fill-rule="evenodd"
                      d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                  </svg> Cerrar sesión</a></li>
            </ul>
          </li>
        </ul>
        </ul>
      </div>
    </nav>
    <?php
  }
  ?>
  <!-- Modal para Corte de Caja -->
  <div class="modal fade" id="modalCorteCaja" tabindex="-1" aria-labelledby="modalCorteCajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalCorteCajaLabel">Corte de Caja</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="card summary-card mb-3">
                <div class="card-header bg-info text-white">
                  <h5 class="card-title mb-0">Resumen del Día</h5>
                </div>
                <div class="card-body">
                  <table class="table table-sm table-bordered">
                    <tbody>
                      <tr>
                        <th>Ventas en Efectivo</th>
                        <td id="ventasEfectivo" class="text-end">$0.00</td>
                      </tr>
                      <tr>
                        <th>Ventas a Crédito</th>
                        <td id="ventasCredito" class="text-end">$0.00</td>
                      </tr>
                      <tr>
                        <th>Cobros Recibidos</th>
                        <td id="cobrosHoy" class="text-end">$0.00</td>
                      </tr>
                      <tr>
                        <th>Pagos Realizados</th>
                        <td id="pagosHoy" class="text-end">$0.00</td>
                      </tr>
                      <tr>
                        <th>Compras al Contado</th>
                        <td id="comprasContado" class="text-end">$0.00</td>
                      </tr>
                      <tr>
                        <th>Compras a Crédito</th>
                        <td id="comprasCredito" class="text-end">$0.00</td>
                      </tr>
                      <tr class="table-primary total-row">
                        <th>Efectivo Esperado</th>
                        <td id="efectivoEsperado" class="text-end">$0.00</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header bg-warning">
                  <h5 class="card-title mb-0">Realizar Corte</h5>
                </div>
                <div class="card-body">
                  <form id="formCorteCaja">
                    <div class="mb-3">
                      <label for="efectivoReal" class="form-label">Efectivo Real en Caja</label>
                      <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="efectivoReal" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="observaciones" class="form-label">Observaciones</label>
                      <textarea class="form-control" id="observaciones" rows="3"></textarea>
                    </div>
                    <div id="resultadoCorte" class="mb-3"></div>
                    <button type="submit" class="btn btn-primary btn-block w-100">
                      <i class="fas fa-calculator me-2"></i>Realizar Corte
                    </button>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Incluye jQuery primero -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Función para confirmar el cierre de sesión con SweetAlert
    function confirmLogout() {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Quieres cerrar la sesión?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          // Verificar con AJAX si hay corte pendiente
          $.ajax({
            url: 'funciones/verificar_corte.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
              if (response.success && response.corte_pendiente) {
                Swal.fire({
                  title: 'Corte pendiente',
                  text: 'Debes realizar el corte de caja antes de cerrar sesión',
                  icon: 'warning',
                  confirmButtonText: 'Entendido'
                }).then(() => {
                  $('#modalCorteCaja').modal('show');
                });
              } else if (response.success) {
                // No hay corte pendiente, proceder a cerrar sesión
                window.location.href = 'salir.php';
              } else {
                // Error en la verificación
                Swal.fire('Error', response.message || 'Error al verificar corte', 'error');
              }
            },
            error: function () {
              // Si hay error en la verificación, permitir cierre de sesión con confirmación
              Swal.fire({
                title: 'Error de verificación',
                text: 'No se pudo verificar el corte de caja. ¿Deseas cerrar sesión de todos modos?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = 'salir.php';
                }
              });
            }
          });
        }
      });
    }
  </script>

  <script>
    $(document).ready(function () {
      // Cargar datos al abrir el modal
      $('#modalCorteCaja').on('show.bs.modal', function () {
        cargarDatosCorteCaja();
      });

      // Enviar formulario de corte de caja
      $('#formCorteCaja').submit(function (e) {
        e.preventDefault();
        realizarCorteCaja();
      });
    });

    function cargarDatosCorteCaja() {
      $.ajax({
        url: 'funciones/corte_caja.php?action=obtener_resumen',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          if (data && data.success && data.data) {
            var resumen = data.data;

            // Mostrar mensaje si hay corte previo
            if (resumen.hay_corte_previo) {
              var fechaCorte = new Date(resumen.ultimo_corte).toLocaleString();
              $('#mensajeCortePrev').html('<div class="alert alert-info">Mostrando transacciones desde el último corte: ' + fechaCorte + '</div>').show();
            } else {
              $('#mensajeCortePrev').hide();
            }

            $('#ventasEfectivo').text('$' + parseFloat(resumen.ventas_efectivo || 0).toFixed(2));
            $('#ventasCredito').text('$' + parseFloat(resumen.ventas_credito || 0).toFixed(2));
            $('#cobrosHoy').text('$' + parseFloat(resumen.cobros_hoy || 0).toFixed(2));
            $('#pagosHoy').text('$' + parseFloat(resumen.pagos_hoy || 0).toFixed(2));
            $('#comprasContado').text('$' + parseFloat(resumen.compras_contado || 0).toFixed(2));
            $('#comprasCredito').text('$' + parseFloat(resumen.compras_credito || 0).toFixed(2));
            $('#efectivoEsperado').text('$' + parseFloat(resumen.efectivo_esperado || 0).toFixed(2));
          } else {
            console.error("Estructura de datos incorrecta:", data);
            mostrarValoresPorDefecto();
          }
        },
        error: function (xhr, status, error) {
          console.error("Error al cargar datos:", error);
          mostrarValoresPorDefecto();
          $('#resultadoCorte').html('<div class="alert alert-danger">Error al cargar los datos del corte de caja</div>');
        }
      });
    }

    function mostrarValoresPorDefecto() {
      $('[id^="ventas"], [id^="cobros"], [id^="pagos"], [id^="compras"], #efectivoEsperado').text('$0.00');
      $('#mensajeCortePrev').hide();
    }

    function realizarCorteCaja() {
      var formData = {
        efectivo_real: $('#efectivoReal').val(),
        observaciones: $('#observaciones').val()
      };

      $.ajax({
        url: 'funciones/corte_caja.php?action=realizar_corte',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            $('#resultadoCorte').html('<div class="alert alert-success">' + response.message + '</div>');
            setTimeout(function () {
              $('#modalCorteCaja').modal('hide');
              location.reload();
            }, 500);
          } else {
            $('#resultadoCorte').html('<div class="alert alert-danger">' + response.message + '</div>');
          }
        },
        error: function (xhr, status, error) {
          console.error("Error en el corte:", error);
          $('#resultadoCorte').html('<div class="alert alert-danger">Error al procesar el corte de caja</div>');
        }
      });
    }
  </script>

</body>

</html>