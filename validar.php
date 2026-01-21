<?php
    include("inc/conectar.php");


    if($_POST['funcion'] == 'iniciar'){
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];
        $Auto = $consulta->query("SELECT * FROM empleados WHERE usuario LIKE  '$usuario' AND contrasena LIKE '$contrasena'");
        foreach ($Auto as $row);
        if($row['id_empleado']>0){
            $_SESSION['SISTEMA']['idempleados'] = $row['id_empleado'];
            $_SESSION['SISTEMA']['rol'] = $row['id_rol'];
            $_SESSION['SISTEMA']['usuario'] = $row['usuario'];
            $_SESSION['SISTEMA']['nombre'] = $row['nombre'];  // Asegúrate de incluir el nombre
        }else{  
            echo "error";
        }
        exit();
    }
?>
