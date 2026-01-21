<?php
include('../inc/conectar.php');

if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT * FROM proveedores WHERE fechabaja IS NULL");
    $tabla = "";
    foreach ($Auto as $proveedor) {
        $tabla .= "<tr>
            <td>" . $proveedor['nombre'] . "</td>
            <td>" . $proveedor['direccion'] . "</td>
            <td>" . $proveedor['correo_electronico'] . "</td>
            <td>" . $proveedor['telefono'] . "</td>
            <td>
                <button class='btn editar btn-sm' idregistros='" . $proveedor['id_proveedor'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button> 
                <button class='btn btn-danger eliminar btn-sm' idregistros='" . $proveedor['id_proveedor'] . "' style='background-color:rgb(203, 38, 38); color: white; border: none;'>Eliminar</button>
            </td>
        </tr>";
    }
    echo $tabla;
    exit();
}

if ($_POST['funcion'] == 'Guardar') {
    // Insertar proveedor
    $query = "INSERT INTO proveedores SET 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        direccion='" . strtoupper($_POST['direccion']) . "', 
        correo_electronico='" . $_POST['correo_electronico'] . "', 
        telefono='" . $_POST['telefono'] . "'";

    if ($consulta->query($query)) {
        echo "Proveedor insertado correctamente";
    }
    exit();
}

if ($_POST['funcion'] == 'Editar') {
    $query = "UPDATE proveedores SET 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        direccion='" . strtoupper($_POST['direccion']) . "', 
        correo_electronico='" . $_POST['correo_electronico'] . "', 
        telefono='" . $_POST['telefono'] . "' 
        WHERE id_proveedor=" . $_POST['idregistros'];

    $consulta->query($query);
    exit();
}

if ($_POST['funcion'] == 'Eliminar') {
    $consulta->query("UPDATE proveedores SET fechabaja='" . date("Y-m-d H:i:s") . "' WHERE id_proveedor=" . $_POST['idregistros']);
    exit();
}


if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        // Obtener datos del proveedor
        $Auto = $consulta->query("SELECT * FROM proveedores WHERE id_proveedor=" . $_POST['id']);
        $row = $Auto->fetch(PDO::FETCH_ASSOC);

    }

    // Generar modal con formulario de edición
    $modal = "
        <div class='row'>
            <div class='col-6'>
                <div class='form-group'>
                    <b for='nombre'>Nombre</b>
                    <input type='text' class='form-control' id='nombre' value='" . $row['nombre'] . "' name='nombre'>
                </div>
            </div>
            <div class='col-6'>
                <div class='form-group'>
                    <b for='direccion'>Dirección</b>
                    <input type='text' class='form-control' id='direccion' value='" . $row['direccion'] . "' name='direccion'>
                </div>
            </div>
            <div class='col-6'>
                <div class='form-group'>
                    <b for='correo_electronico'>Correo Electrónico</b>
                    <input type='email' class='form-control' id='correo_electronico' value='" . $row['correo_electronico'] . "' name='correo_electronico'>
                </div>
            </div>
            <div class='col-6'>
                <div class='form-group'>
                    <b for='telefono'>Teléfono</b>
                    <input type='text' class='form-control' id='telefono' value='" . $row['telefono'] . "' name='telefono'>
                </div>
            </div>
        </div>";

    echo $modal;
    exit();
}
?>