<?php
include('../inc/conectar.php');

if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT * FROM clientes");
    $tabla = "";
    foreach ($Auto as $clientes) {
        $tabla .= "<tr>
            <td>" . $clientes['nombre'] . "</td>
            <td>" . $clientes['apellido_p'] . "</td>
            <td>" . $clientes['apellido_m'] . "</td>
            <td>" . $clientes['telefono'] . "</td>
            <td>
                <button class='btn editar btn-sm' idregistros='" . $clientes['id_cliente'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button> 
                <button class='btn btn-danger  btn-sm eliminar' idregistros='" . $clientes['id_cliente'] . "'style='background-color:rgb(203, 38, 38); color: white; border: none;'>Eliminar</button>
            </td>

            <button class='btn editar btn-sm' idregistros='" . $producto['id_productos'] . "' >Editar</button> 
            <button class='btn btn-danger  btn-sm eliminar' idregistros='" . $producto['id_productos'] . "'>Eliminar</button>

        </tr>";
    }
    echo $tabla;
    exit();
}

if ($_POST['funcion'] == 'Guardar') {
    // Insertar cliente
    $query = "INSERT INTO clientes SET 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        apellido_p='" . strtoupper($_POST['apellido_p']) . "', 
        apellido_m='" . strtoupper($_POST['apellido_m']) . "', 
        telefono='" . $_POST['telefono'] . "'";

    if ($consulta->query($query)) {
        echo "Cliente insertado correctamente";
    }
    exit();
}

if ($_POST['funcion'] == 'Editar') {
    $query = "UPDATE clientes SET 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        apellido_p='" . strtoupper($_POST['apellido_p']) . "', 
        apellido_m='" . strtoupper($_POST['apellido_m']) . "', 
        telefono='" . $_POST['telefono'] . "' 
        WHERE id_cliente=" . $_POST['idregistros'];

    $consulta->query($query);
    exit();
}

if ($_POST['funcion'] == 'Eliminar') {
    $consulta->query("DELETE FROM clientes WHERE id_cliente=" . $_POST['idregistros']);
    exit();
}


if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        // Obtener datos del proveedor
        $Auto = $consulta->query("SELECT * FROM clientes WHERE id_cliente=" . $_POST['id']);
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
                    <b for='apellido_p'>Apellido Paterno</b>
                    <input type='text' class='form-control' id='apellido_p' value='" . $row['apellido_p'] . "' name='apellido_p'>
                </div>
            </div>
            <div class='col-6'>
                <div class='form-group'>
                    <b for='apellido_m'>Apellido Materno</b>
                    <input type='text' class='form-control' id='apellido_m' value='" . $row['apellido_m'] . "' name='apellido_m'>
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