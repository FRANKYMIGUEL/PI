<?php
session_start();
require_once 'check_session.php';
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

			try {
				// Iniciar transacción
				$consulta->beginTransaction();

				// 1. Verificar si la venta ya está cancelada (con manejo de errores)
				$stmtVenta = $consulta->prepare("SELECT fechacancelada FROM ventas WHERE id_venta = ?");
				$stmtVenta->execute([$_POST['idventas']]);
				$venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

				if (!$venta) {
					throw new Exception("No se encontró la venta especificada");
				}

				if ($venta['fechacancelada']) {
					throw new Exception("Esta venta ya fue cancelada anteriormente");
				}

				// 2. Obtener los detalles de la venta para devolver existencias (con consulta preparada)
				$stmtDetalles = $consulta->prepare("SELECT id_productos, cantidad FROM ventasdetalle WHERE id_venta = ?");
				$stmtDetalles->execute([$_POST['idventas']]);
				$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

				if (empty($detalles)) {
					throw new Exception("No se encontraron detalles para esta venta");
				}

				// 3. Devolver existencias a productos (con consulta preparada)
				$stmtUpdateProductos = $consulta->prepare("UPDATE productos SET existencias = existencias + ? WHERE id_productos = ?");

				foreach ($detalles as $detalle) {
					$stmtUpdateProductos->execute([$detalle['cantidad'], $detalle['id_productos']]);
				}

				// 4. Marcar la venta como cancelada (con consulta preparada)
				$stmtUpdateVenta = $consulta->prepare("UPDATE ventas SET fechacancelada = ? WHERE id_venta = ?");
				$stmtUpdateVenta->execute([date("Y-m-d H:i:s"), $_POST['idventas']]);

				// 5. Registrar la cancelación (con consulta preparada)
				$stmtInsertCancelacion = $consulta->prepare("INSERT INTO cancelaciones_ventas 
																(id_venta, id_usuario, fecha_cancelacion, motivo) 
																VALUES (?, ?, ?, ?)");
				$stmtInsertCancelacion->execute([
					$_POST['idventas'],
					$_SESSION['SISTEMA']['id_empleado'],
					date("Y-m-d H:i:s"),
					'Cancelación manual'
				]);

				// 6. Si era una venta a crédito, actualizar el saldo pendiente (con consulta preparada)
				$stmtCredito = $consulta->prepare("SELECT id, saldo_pendiente FROM cuentas_por_cobrar WHERE id_venta = ?");
				$stmtCredito->execute([$_POST['idventas']]);
				$credito = $stmtCredito->fetch(PDO::FETCH_ASSOC);

				if ($credito) {
					$stmtUpdateCredito = $consulta->prepare("UPDATE cuentas_por_cobrar 
															   SET estado = 'cancelado', saldo_pendiente = 0 
															   WHERE id = ?");
					$stmtUpdateCredito->execute([$credito['id']]);
				}

				// Confirmar transacción
				$consulta->commit();

				echo "OK";
			} catch (Exception $e) {
				// Revertir en caso de error
				if ($consulta->inTransaction()) {
					$consulta->rollBack();
				}
				http_response_code(500);
				echo "Error: " . $e->getMessage();
				error_log("Error al cancelar venta: " . $e->getMessage());
			}
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
			border-radius: 10px;
			overflow: hidden;
			background-color: #F2EFE7;
		}

		.table thead tr {
			background-color: #2973B2;
			color: white;
			text-align: left;
			font-weight: bold;
		}

		.bt_custom {
			background-color: #2973B2;
			color: white;
			border: none;
			padding: 3px 5px;
			border-radius: 5px;
			transition: background-color 0.3s;
		}

		.bt_custo {
			background-color: #2973B2;
			color: white;
			border: none;
			padding: 8px 10px;
			border-radius: 5px;
			transition: background-color 0.3s;
		}

		.bt_custom1 {
			background-color: #cb2626;
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

		.table .cantidad input {
			width: 60px;
			text-align: center;
			border: 1px solid #9ACBD0;
			border-radius: 4px;
			padding: 5px;
			background-color: #F2EFE7;
		}

		.table .opciones button {
			background: none;
			border: none;
			color: #2973B2;
			cursor: pointer;
			font-size: 1.2em;
			transition: color 0.3s;
		}

		.table .opciones button:hover {
			color: #48A6A7;
		}

		#total,
		#cambio {
			font-weight: bold;
			color: #2973B2;
			font-size: 1.1em;
		}

		.form-control-sm {
			border-radius: 5px;
			border: 1px solid #9ACBD0;
			padding: 8px 12px;
			background-color: #F2EFE7;
		}

		.table {
			background-color: #F2EFE7;
			backdrop-filter: blur(5px);
		}

		body {
			background-color: #f0f8ff;
			background: linear-gradient(135deg, #F2EFE7 0%, #F2EFE7 50%, #F2EFE7 100%);
		}

		.banner {
			background-color: #2973B2;
			color: white;
			padding: 5px;
		}

		#tablaPedidos {
			width: 100% !important;
		}

		#tablaPedidos thead th {
			white-space: nowrap;
			position: relative;
		}

		#tablaPedidos tbody td {
			white-space: nowrap;
		}

		/* Estilo para el contenedor del buscador */
		.dataTables_filter {
			margin-bottom: 20px;
			/* Separa el buscador de la tabla */
			display: flex;
			align-items: center;
		}

		.dataTables_filter label {
			display: flex;
			/* Mantener el label flexible */
			align-items: center;
			/* Alinear verticalmente el texto y el input */
			margin-bottom: 0;
			/* Eliminar margen inferior predeterminado */
			gap: 10px;
			/* Espacio entre "Buscar:" y el input */
			margin-bottom: 0;
			/* Elimina el margen inferior predeterminado */
			font-weight: bold;
		}

		.dataTables_filter input {
			width: 400px !important;
			/* Ajusta el ancho según necesidad */
			height: 40px !important;
			font-size: 16px !important;
			margin-left: 10px;
			/* Espacio entre el texto y el input */
			/* Espacio entre "Buscar:" y el input */
			padding: 8px 12px;
			border: 1px solid #ccc;
			border-radius: 4px;
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
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
	<script>
		$(document).ready(function () {
			// Variable para mantener la instancia de DataTable
			var dataTable = null;

			// Función para inicializar o recrear la DataTable
			function initDataTable() {
				if (dataTable !== null) {
					dataTable.destroy();
					$('#tablaPedidos').empty();
				}

				var table = $('#tablaPedidos');
				if (table.find('thead').length === 0) {
					table.append('<thead><tr><th>Folio</th><th>Fecha</th><th>Cliente</th><th>Usuario</th><th>Importe</th><th>Opciones</th></tr></thead>');
				}
				if (table.find('tbody').length === 0) {
					table.append('<tbody id="resultados_productos"></tbody>');
				}

				dataTable = table.DataTable({
					"order": [[1, "desc"]],
					"language": {
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
					"retrieve": true,
					"initComplete": function () {
						// Aplicar estilos después de la inicialización
						$('.dataTables_filter input').addClass('form-control');
						$('.dataTables_filter input').css({
							'width': '500px',  // Ancho personalizado
							'height': '40px',  // Altura personalizada
							'font-size': '16px' // Tamaño de fuente más grande
						});
					}
				});
			}

			// Carga inicial automática
			Carga_Entradas();


			// También cargar al cambiar fechas o cliente
			$('#fechainicial, #fechafinal, #clientes').on('change', function () {
				Carga_Entradas();
			});

			// Función para cargar los pedidos
			function Carga_Entradas() {
				var fechaInicial = $("#fechainicial").val();
				var fechaFinal = $("#fechafinal").val();

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

						if (tbody.find('tr').length > 0 &&
							tbody.find('tr').first().find('td').length === 6) {
							initDataTable();
						} else {
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
				var boton = $(this);

				alertify.confirm(
					"Confirmación",
					"¿Está seguro de cancelar este pedido? Se devolverán las existencias a inventario.",
					function () {
						boton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelando...');

						$.ajax({
							type: "POST",
							url: window.location.href,
							data: {
								funcion: "Eliminar",
								idventas: idventas
							},
							success: function (response) {
								if (response === "OK") {
									alertify.success("Venta cancelada y existencias devueltas");

									boton.closest('tr').addClass('bg-danger');
									boton.remove();

									setTimeout(function () {
										Carga_Entradas();
									}, 1000);
								} else {
									alertify.error("Error: " + response);
									boton.html('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>');
								}
							},
							error: function (xhr, status, error) {
								alertify.error("Error al cancelar: " + error);
								boton.html('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>');
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