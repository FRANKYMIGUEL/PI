<?php
session_start();
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si es una petición AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['funcion'])) {
	include("inc/conectar.php");

	switch ($_POST['funcion']) {
		case 'Carga_Ventas':
			$cliente = '';
			if (isset($_POST['cliente']) && $_POST['cliente'] != '') {
				$clien = explode("-", $_POST['cliente']);
				$cliente = ' AND ventas.idclientes=' . $clien[0];
			}

			$cancelado = '';
			$sql = "SELECT ventas.*, 
                           clientes.nombre AS cliente, 
                           clientes.apellido_p, 
                           clientes.apellido_m,
                           empleados.nombre AS empleado 
                    FROM ventas 
                    LEFT JOIN clientes ON clientes.id_cliente=ventas.idclientes 
                    LEFT JOIN empleados ON empleados.id_empleado=ventas.idempleados 
                    WHERE ventas.fecha BETWEEN '" . $_POST['fechai'] . " 00:00:00' AND '" . $_POST['fechaf'] . " 23:59:59' $cliente
                    ORDER BY ventas.fecha DESC";

			$resultados = $consulta->query($sql);

			if ($resultados->rowCount() > 0) {
				foreach ($resultados as $row) {
					$cancelado = ($row['fechacancelada'] != '') ? 'bg-danger' : '';
					$fecha_formateada = date("d/m/Y H:i:s", strtotime($row["fecha"]));
					$nombre_cliente = $row['cliente'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m'];
					?>
					<tr class="<?= $cancelado ?> text-uppercase">
						<td><?= str_pad($row["folio"], 6, "0", STR_PAD_LEFT) ?></td>
						<td><?= $fecha_formateada ?></td>
						<td><?= htmlspecialchars($nombre_cliente) ?></td>
						<td><?= htmlspecialchars($row['empleado']) ?></td>
						<td align="right">$ <?= number_format($row['total'], 2) ?></td>
						<td width="200" class="center ">
							<a href="ticket_venta.php?folio=<?= $row['folio'] ?>" target="_blank" class="btn btn-sm bt_custom"
								title="Reimprimir">
								<svg xmlns="http://www.w3.org/2000/svg" width="40" height="16" fill="currentColor" class="bi bi-printer"
									viewBox="0 0 16 16">
									<path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
									<path
										d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
								</svg></a>
							<?php if ($row['fechacancelada'] == '') { ?>
								<button class='btn btn-xs bt_custom1 cancelar' idventas="<?= $row['id_venta'] ?>" title='Cancelar Venta'>
									<svg xmlns="http://www.w3.org/2000/svg" width="40" height="16" fill="currentColor" class="bi bi-x-circle"
										viewBox="0 0 16 16">
										<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
										<path
											d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
									</svg>
								</button>
							<?php } ?>
						</td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No se encontraron pedidos en el rango de fechas seleccionado</td></tr>';
			}
			exit();

		case 'Eliminar':
			include('inc/conectar.php');
			$consulta->query("UPDATE ventas SET fechacancelada='" . date("Y-m-d H:i:s") . "' WHERE id_venta=" . $_POST['idventas']);
			exit();

		default:
			echo json_encode(['error' => 'Función no válida']);
			exit();
	}
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consulta de Pedidos</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css">

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
			background-color: #F2EFE7;
			/* Fondo claro */
		}

		.table thead tr {
			background-color: #2973B2;
			/* Azul más intenso */
			color: white;
			text-align: left;
			font-weight: bold;
		}

		.bt_custom {
			background-color: #2973B2;
			/* Azul turquesa */
			color: white;
			border: none;
			padding: 3px 5px;
			border-radius: 5px;
			transition: background-color 0.3s;
		}

		.bt_custo {
			background-color: #2973B2;
			/* Azul turquesa */
			color: white;
			border: none;
			padding: 8px 10px;
			border-radius: 5px;
			transition: background-color 0.3s;
		}

		.bt_custom1 {
			background-color: #cb2626;
			/* Azul turquesa */
			color: white;
			border: none;
			padding: 3px 5px;
			border-radius: 5px;
			transition: background-color 0.3s;
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

		.table {
			background-color: #F2EFE7;
			/* Fondo claro con transparencia */
			backdrop-filter: blur(5px);
			/* Efecto de desenfoque para el fondo */
		}

		/* Alternativa: fondo de color sólido */
		body {
			background-color: #f0f8ff;
			/* Azul claro muy suave */
			/* O un gradiente */
			background: linear-gradient(135deg, #F2EFE7 0%, #F2EFE7 50%, #F2EFE7 100%);
		}

		.banner {
			background-color: #2973B2;
			/* Azul intenso */
			color: white;
			padding: 5px;
		}

		/* Asegurar que la tabla mantenga su estructura */
		#tablaPedidos {
			width: 100% !important;
		}

		/* Estilos para encabezados y celdas */
		#tablaPedidos thead th {
			white-space: nowrap;
			/* Evitar saltos de línea */
			position: relative;
		}

		#tablaPedidos tbody td {
			white-space: nowrap;
			/* Mantener contenido en una línea */
		}
	</style>

</head>

<body>
	<?php include("menu.php"); ?>
	<div class="col-12 text-center banner">
		<h1 class="">
			Ventas por fecha
		</h1>
	</div>
	<div class="container-fluid ">

		<div class="row">
			<div class="col-md-12">
				<div class=" ">

					<div class="card-body">
						<div class="row mb-3">
							<div class="col-md-2">
								<label class="form-label"><b>Fecha Inicial</b></label>
								<input type="date" class="form-control" id="fechainicial" value="<?= date('Y-m-d') ?>">
							</div>
							<div class="col-md-2">
								<label class="form-label"><b>Fecha Final</b></label>
								<input type="date" class="form-control" id="fechafinal" value="<?= date('Y-m-d') ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label"><b>Cliente</b></label>
								<input list="datosClientes" autocomplete="off" class="form-control" id="clientes"
									placeholder="Buscar clientes">
								<datalist id="datosClientes">
									<?php
									include("inc/conectar.php");
									$clientes = $consulta->query("SELECT id_cliente, nombre, apellido_p, apellido_m FROM clientes ORDER BY nombre");
									foreach ($clientes as $cli) {
										echo "<option value='{$cli['id_cliente']}-{$cli['nombre']} {$cli['apellido_p']} {$cli['apellido_m']}'>";
									}
									?>
								</datalist>
							</div>
							<div class="col-md-2 d-flex align-items-end">
								<button id="btnBuscar" class="btn bt_custo">
									<i class="bi bi-search"></i> Buscar
								</button>
							</div>
						</div>

						<div class="table-responsive table-sm compact">
							<table id="tablaPedidos"
								class="table table-striped table-bordered table-hover table-sm compact">
								<thead class="">
									<tr>
										<th>Folio</th>
										<th>Fecha</th>
										<th>Cliente</th>
										<th>Usuario</th>
										<th>Importe</th>
										<th>Opciones</th>
									</tr>
								</thead>
								<tbody id="resultados_productos">
									<tr>
										<td colspan="6" class="text-center">Seleccione fechas y haga clic en Buscar</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Reemplazar en tus scripts -->
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
	<script>
		$(document).ready(function () {
			// Variable para mantener la instancia de DataTable
			var dataTable = null;

			// Función para inicializar o recrear la DataTable
			function initDataTable() {
				// Destruir instancia anterior si existe
				if (dataTable !== null) {
					dataTable.destroy();
					$('#tablaPedidos').empty(); // Limpiar la tabla
				}

				// Reconstruir estructura de la tabla si es necesario
				var table = $('#tablaPedidos');
				if (table.find('thead').length === 0) {
					table.append('<thead><tr><th>Folio</th><th>Fecha</th><th>Cliente</th><th>Usuario</th><th>Importe</th><th>Opciones</th></tr></thead>');
				}
				if (table.find('tbody').length === 0) {
					table.append('<tbody id="resultados_productos"></tbody>');
				}

				// Inicializar DataTable con opciones
				dataTable = table.DataTable({
					"order": [[1, "desc"]], // Ordenar por fecha descendente
					"language": { // Configuración de idioma
						"lengthMenu": "Mostrar _MENU_ registros por página",
						"zeroRecords": "No se encontraron resultados",
						"info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"infoEmpty": "No hay registros disponibles",
						"infoFiltered": "(filtrado de _MAX_ registros totales)",
						"search": "Buscar:",
						"paginate": {
							"first": "Primero",
							"last": "Último",
							"next": "Siguiente",
							"previous": "Anterior"
						}
					},
					"destroy": true,
					"retrieve": true // Permite recrear la tabla
				});
			}

			// Carga inicial automática
			Carga_Entradas();

			// Cargar datos al hacer clic en Buscar
			$('#btnBuscar').click(function () {
				Carga_Entradas();
			});

			// También cargar al cambiar fechas o cliente
			$('#fechainicial, #fechafinal, #clientes').on('change', function () {
				Carga_Entradas();
			});

			// Función para cargar los pedidos
			function Carga_Entradas() {
				var fechaInicial = $("#fechainicial").val();
				var fechaFinal = $("#fechafinal").val();

				// Validar fechas
				if (!fechaInicial || !fechaFinal) {
					$("#resultados_productos").html('<tr><td colspan="6" class="text-center">Por favor seleccione ambas fechas</td></tr>');
					if (dataTable !== null) {
						dataTable.destroy();
						dataTable = null;
					}
					return;
				}

				$.ajax({
					type: "POST",
					url: window.location.href,
					data: {
						funcion: "Carga_Ventas",
						fechai: fechaInicial,
						fechaf: fechaFinal,
						cliente: $("#clientes").val()
					},
					beforeSend: function () {
						if (dataTable !== null) {
							dataTable.destroy();
							dataTable = null;
						}
						$("#resultados_productos").html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
					},
					success: function (msg) {
						var tbody = $("#resultados_productos");
						tbody.html(msg);

						// Verificar que tenemos una estructura válida
						if (tbody.find('tr').length > 0 &&
							tbody.find('tr').first().find('td').length === 6) {
							initDataTable();
						} else {
							// Si no es válido, limpiar DataTable
							if (dataTable !== null) {
								dataTable.destroy();
								dataTable = null;
							}
						}
					},
					error: function (xhr, status, error) {
						$("#resultados_productos").html('<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos: ' + error + '</td></tr>');
						if (dataTable !== null) {
							dataTable.destroy();
							dataTable = null;
						}
					}
				});
			}

			// Manejar cancelación de pedidos
			$(document).on("click", ".cancelar", function () {
				var idventas = $(this).attr("idventas");
				alertify.confirm(
					"Confirmación",
					"¿Está seguro de cancelar este pedido? Esta acción no se puede deshacer.",
					function () {
						$.ajax({
							type: "POST",
							url: window.location.href,
							data: {
								funcion: "Eliminar",
								idventas: idventas
							},
							success: function () {
								alertify.success("Pedido cancelado correctamente");
								Carga_Entradas(); // Recargar los datos
							},
							error: function () {
								alertify.error("Error al cancelar el pedido");
							}
						});
					},
					function () {
						alertify.error("Cancelación abortada");
					}
				);
			});
		});
	</script>
</body>

</html>