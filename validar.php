<?php
include("inc/conectar.php");

if ($_POST['funcion'] == 'iniciar') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $Auto = $consulta->query("SELECT * FROM empleados WHERE usuario LIKE '$usuario' AND contrasena LIKE '$contrasena' AND fechabaja IS NULL");
    foreach ($Auto as $row)
        ;

    if ($row['id_empleado'] > 0) {
        $_SESSION['SISTEMA']['id_empleado'] = $row['id_empleado'];
        $_SESSION['SISTEMA']['rol'] = $row['id_rol'];
        $_SESSION['SISTEMA']['usuario'] = $row['usuario'];
        $_SESSION['SISTEMA']['nombre'] = $row['nombre'];
    } else {
        echo "error";
    }
    exit();
}
?>