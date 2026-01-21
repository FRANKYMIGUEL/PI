<?php
session_start();
// Habilitar todos los errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Configuración para manejar memoria y tiempo
ini_set("memory_limit", "1G");
set_time_limit(1000);

// Incluir archivo de conexión PDO
$conexion_file = "inc/conectar.php";
if (!file_exists($conexion_file)) {
   die("Error: No se encontró el archivo de conexión ($conexion_file)");
}
include($conexion_file);

// Validación y seguridad básica
$folio = isset($_GET["folio"]) ? str_pad(trim($_GET["folio"]), 4, '0', STR_PAD_LEFT) : '';
if (empty($folio)) {
   die("Error: No se especificó folio de venta");
}

try {
   $stmt = $consulta->prepare("SELECT * FROM ventas WHERE folio = :folio");
   $stmt->bindParam(':folio', $folio, PDO::PARAM_STR);
   $stmt->execute();
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$row) {
      die("Error: No se encontró la venta con folio $folio");
   }

   // CAMBIO IMPORTANTE: Usar idclientes en lugar de id_cliente
   $id_cliente = $row['idclientes'] ?? 0;

   // Solo buscar cliente si hay un ID válido
   // En la sección donde buscas al cliente, modifica la consulta para incluir los apellidos
   if ($id_cliente > 0) {
      $stmt_cliente = $consulta->prepare("SELECT nombre, apellido_p, apellido_m FROM clientes WHERE id_cliente = :id_cliente");
      $stmt_cliente->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
      $stmt_cliente->execute();
      $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
   } else {
      $cliente = false;
   }
   // Consulta de los detalles de venta
   $stmt_detalle = $consulta->prepare("
        SELECT vd.*, p.codigo_barras, p.nombre 
        FROM ventasdetalle vd 
        LEFT JOIN productos p ON p.id_productos = vd.id_productos 
        WHERE vd.id_venta = :id_venta
    ");
   $stmt_detalle->bindParam(':id_venta', $row['id_venta'], PDO::PARAM_INT);
   $stmt_detalle->execute();
   $result_detalle = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
   die("Error en la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Ticket Venta <?= htmlspecialchars($row["folio"] ?? '') ?></title>
   <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
   <link href="css/bootstrap.min.css" rel="stylesheet">
   <style>
      @page {
         size: auto;
         margin: 0mm;
      }

      body {
         font-family: 'Courier New', monospace;
         font-size: 12px;
         width: 80mm;
         margin: 0 auto;
         padding: 2mm;
      }

      .ticket {
         width: 100%;
         max-width: 80mm;
      }

      .ticket-header {
         text-align: center;
         margin-bottom: 5px;
         border-bottom: 1px dashed #ccc;
         padding-bottom: 5px;
      }

      .ticket-header img {
         max-height: 50px;
         max-width: 100%;
      }

      .ticket-info {
         text-align: center;
         margin-bottom: 5px;
         font-size: 10px;
      }

      .ticket-title {
         font-weight: bold;
         font-size: 12px;
         margin-top: 3px;
      }

      .ticket-table {
         width: 100%;
         border-collapse: collapse;
         margin: 5px 0;
      }

      .ticket-table th {
         text-align: left;
         padding: 1px 0;
         border-bottom: 1px solid #000;
         font-size: 10px;
      }

      .ticket-table td {
         padding: 2px 0;
         vertical-align: top;
         font-size: 10px;
      }

      .product-name {
         font-weight: bold;
         padding-top: 3px;
      }

      .text-right {
         text-align: right;
      }

      .text-center {
         text-align: center;
      }

      .text-left {
         text-align: left;
      }

      .total-row {
         font-weight: bold;
         border-top: 1px dashed #000;
         padding-top: 3px;
         font-size: 11px;
      }

      .footer {
         margin-top: 10px;
         font-size: 10px;
         border-top: 1px dashed #ccc;
         padding-top: 5px;
      }

      .amount-in-words {
         font-style: italic;
         margin: 3px 0;
      }
   </style>
</head>

<body>
   <div class="ticket">
      <div class="ticket-header">
         <?php
         $logo_path = "imagenes/logo.jpg";
         if (file_exists($logo_path)) {
            echo "<img src='$logo_path' alt='Logo'>";
         }
         ?>
      </div>

      <div class="ticket-info">
         <div>domicilio<br>Col.Las Margaritas, Las Margaritas, Jal.</div>
         <div>tel 3315692631</div>
         <div class="ticket-title">Ticket No.: <?= htmlspecialchars($row["folio"] ?? '0000') ?></div>
      </div>

      <div class="ticket-details">
         <?php
         // Formateo de fecha y hora mejorado
         $fecha = $row["fecha"] ?? date('Y-m-d H:i:s');
         $fecha_dt = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);

         if ($fecha_dt) {
            $fecha_formateada = $fecha_dt->format('d/m/Y');
            $hora_formateada = $fecha_dt->format('h:i:s A');
         } else {
            $fecha_formateada = date('d/m/Y');
            $hora_formateada = date('h:i:s A');
         }
         ?>
         <div class="text-center">
            Fecha: <?= $fecha_formateada ?> <?= $hora_formateada ?>
         </div>

         <?php
         $nombre_cliente = "VENTA DE MOSTRADOR";
         if (!empty($id_cliente) && $id_cliente > 0) {
            if ($cliente !== false) {
               // Construir nombre completo con apellidos
               $nombre_completo = trim($cliente['nombre'] ?? '');

               // Agregar apellido paterno si existe
               if (!empty($cliente['apellido_p'])) {
                  $nombre_completo .= ' ' . trim($cliente['apellido_p']);
               }

               // Agregar apellido materno si existe
               if (!empty($cliente['apellido_m'])) {
                  $nombre_completo .= ' ' . trim($cliente['apellido_m']);
               }

               // Si al menos tiene nombre o apellidos
               if (!empty(trim($nombre_completo))) {
                  $nombre_cliente = $nombre_completo;
               } else {
                  $nombre_cliente = "[CLIENTE #$id_cliente SIN NOMBRE]";
               }
            } else {
               $nombre_cliente = "[CLIENTE #$id_cliente NO ENCONTRADO]";
            }
         }
         ?>
         <div class="text-center">Cliente: <?= htmlspecialchars($nombre_cliente) ?></div>
      </div>

      <table class="ticket-table">
         <thead>
            <tr>
               <th width="15%" class="text-left">Cant</th>
               <th width="45%" class="text-left">Descripción</th>
               <th width="20%" class="text-right">P.Unit</th>
               <th width="20%" class="text-right">Importe</th>
            </tr>
         </thead>
         <tbody>
            <?php
            $total = 0;
            foreach ($result_detalle as $reg):
               $importe = $reg["cantidad"] * $reg["precio"];
               $total += $importe;
               ?>
               <tr>
                  <td class="text-left"><?= number_format($reg["cantidad"] ?? 0, 2) ?></td>
                  <td class="text-left"><?= htmlspecialchars(strtoupper($reg["nombre"] ?? 'PRODUCTO SIN NOMBRE')) ?></td>
                  <td class="text-right">$<?= number_format($reg["precio"] ?? 0, 2) ?></td>
                  <td class="text-right">$<?= number_format($importe, 2) ?></td>
               </tr>
            <?php endforeach; ?>

            <tr>
               <td colspan="4" class="total-row text-right">
                  Total Venta: $<?= number_format($total, 2) ?>
               </td>
            </tr>

            <?php if (($row["efectivo"] ?? 0) > 0):
               $cambio = ($row["efectivo"] ?? 0) - $total;
               ?>
               <tr>
                  <td colspan="4" class="text-right">
                     Efectivo: $<?= number_format($row["efectivo"] ?? 0, 2) ?>
                  </td>
               </tr>
               <tr>
                  <td colspan="4" class="text-right">
                     Cambio: $<?= number_format($cambio, 2) ?>
                  </td>
               </tr>
            <?php endif; ?>
         </tbody>
      </table>

      <div class="footer">
         <div class="amount-in-words">*<?= num2letras($total) ?>*</div>
         <div>Atendido por: <?= htmlspecialchars($_SESSION['SISTEMA']['nombre'] ) ?></div>
         <div class="text-center">¡Gracias por su compra!</div>
      </div>
   </div>

   <script src="js/jquery-3.7.1.min.js"></script>
   <script>
      $(document).ready(function () {
         window.print();
         setTimeout(function () {
            window.close();
         }, 1000);
      });
   </script>
</body>

</html>
<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////																			////////////////////////
/////////////////				CONVIERTE NUMEROS A LETRAS									////////////////////////
/////////////////																			////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function num2letras($num, $fem = false, $dec = true)
{
   $matuni[2] = "dos";
   $matuni[3] = "tres";
   $matuni[4] = "cuatro";
   $matuni[5] = "cinco";
   $matuni[6] = "seis";
   $matuni[7] = "siete";
   $matuni[8] = "ocho";
   $matuni[9] = "nueve";
   $matuni[10] = "diez";
   $matuni[11] = "once";
   $matuni[12] = "doce";
   $matuni[13] = "trece";
   $matuni[14] = "catorce";
   $matuni[15] = "quince";
   $matuni[16] = "dieciseis";
   $matuni[17] = "diecisiete";
   $matuni[18] = "dieciocho";
   $matuni[19] = "diecinueve";
   $matuni[20] = "veinte";
   $matunisub[2] = "dos";
   $matunisub[3] = "tres";
   $matunisub[4] = "cuatro";
   $matunisub[5] = "quin";
   $matunisub[6] = "seis";
   $matunisub[7] = "sete";
   $matunisub[8] = "ocho";
   $matunisub[9] = "nove";

   $matdec[2] = "veint";
   $matdec[3] = "treinta";
   $matdec[4] = "cuarenta";
   $matdec[5] = "cincuenta";
   $matdec[6] = "sesenta";
   $matdec[7] = "setenta";
   $matdec[8] = "ochenta";
   $matdec[9] = "noventa";
   $matsub[3] = 'mill';
   $matsub[5] = 'bill';
   $matsub[7] = 'mill';
   $matsub[9] = 'trill';
   $matsub[11] = 'mill';
   $matsub[13] = 'bill';
   $matsub[15] = 'mill';
   $matmil[4] = 'millones';
   $matmil[6] = 'billones';
   $matmil[7] = 'de billones';
   $matmil[8] = 'millones de billones';
   $matmil[10] = 'trillones';
   $matmil[11] = 'de trillones';
   $matmil[12] = 'millones de trillones';
   $matmil[13] = 'de trillones';
   $matmil[14] = 'billones de trillones';
   $matmil[15] = 'de billones de trillones';
   $matmil[16] = 'millones de billones de trillones';

   //Zi hack
   $float = explode('.', $num);
   $num = $float[0];

   $num = trim((string) @$num);
   if ($num[0] == '-') {
      $neg = 'menos ';
      $num = substr($num, 1);
   } else
      $neg = '';
   while ($num[0] == '0')
      $num = substr($num, 1);
   if ($num[0] < '1' or $num[0] > 9)
      $num = '0' . $num;
   $zeros = true;
   $punt = false;
   $ent = '';
   $fra = '';
   for ($c = 0; $c < strlen($num); $c++) {
      $n = $num[$c];
      if (!(strpos(".,'''", $n) === false)) {
         if ($punt)
            break;
         else {
            $punt = true;
            continue;
         }

      } elseif (!(strpos('0123456789', $n) === false)) {
         if ($punt) {
            if ($n != '0')
               $zeros = false;
            $fra .= $n;
         } else

            $ent .= $n;
      } else

         break;

   }
   $ent = '     ' . $ent;
   if ($dec and $fra and !$zeros) {
      $fin = ' coma';
      for ($n = 0; $n < strlen($fra); $n++) {
         if (($s = $fra[$n]) == '0')
            $fin .= ' cero';
         elseif ($s == '1')
            $fin .= $fem ? ' una' : ' un';
         else
            $fin .= ' ' . $matuni[$s];
      }
   } else
      $fin = '';
   if ((int) $ent === 0)
      return 'Cero ' . $fin;
   $tex = '';
   $sub = 0;
   $mils = 0;
   $neutro = false;
   while (($num = substr($ent, -3)) != '   ') {
      $ent = substr($ent, 0, -3);
      if (++$sub < 3 and $fem) {
         $matuni[1] = 'una';
         $subcent = 'as';
      } else {
         $matuni[1] = $neutro ? 'un' : 'uno';
         $subcent = 'os';
      }
      $t = '';
      $n2 = substr($num, 1);
      if ($n2 == '00') {
      } elseif ($n2 < 21)
         $t = ' ' . $matuni[(int) $n2];
      elseif ($n2 < 30) {
         $n3 = $num[2];
         if ($n3 != 0)
            $t = 'i' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      } else {
         $n3 = $num[2];
         if ($n3 != 0)
            $t = ' y ' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }
      $n = $num[0];
      if ($n == 1) {
         $t = ' ciento' . $t;
      } elseif ($n == 5) {
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
      } elseif ($n != 0) {
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
      }
      if ($sub == 1) {
      } elseif (!isset($matsub[$sub])) {
         if ($num == 1) {
            $t = ' mil';
         } elseif ($num > 1) {
            $t .= ' mil';
         }
      } elseif ($num == 1) {
         $t .= ' ' . $matsub[$sub] . '?n';
      } elseif ($num > 1) {
         $t .= ' ' . $matsub[$sub] . 'ones';
      }
      if ($num == '000')
         $mils++;
      elseif ($mils != 0) {
         if (isset($matmil[$sub]))
            $t .= ' ' . $matmil[$sub];
         $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
   }
   $tex = $neg . substr($tex, 1) . $fin;
   //Zi hack --> return ucfirst($tex);
   $end_num = ucfirst($tex) . ' pesos ' . (isset($float[1]) ? substr($float[1], 0, 2) : '00') . '/100 M.N.';
   return $end_num;
}
?>