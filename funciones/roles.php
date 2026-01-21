<?
include('../inc/conectar.php');
if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT * FROM roles WHERE fechabaja IS NULL");
    foreach ($Auto as $roles) {
        $tabla .= "<tr>
            <td>" . $roles['nombre_rol'] . "</td>
            <td>
            <button class='btn editar btn-sm' idregistros='" . $roles['id_rol'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button> 
            <button class='btn btn-danger eliminar btn-sm' idregistros='" . $roles['id_rol'] . "'style='background-color:rgb(203, 38, 38); color: white; border: none;' >Eliminar</button>
            </td>
        </tr>";
    }
    echo $tabla;
    exit();
}

if ($_POST['funcion'] == 'Guardar') {
    $Auto = $consulta->query("SELECT * FROM roles WHERE nombre_rol = '" . $_POST['nombre_rol'] . "'");
    $roles = $Auto->fetch();

    if ($roles) {
        echo "El nombre ya existe";
        exit();
    }
    $consulta->query("INSERT INTO roles (nombre_rol) VALUES ('" . strtoupper($_POST['nombre_rol']) . "')");
    echo "Rol guardada correctamente";
}
if ($_POST['funcion'] == 'Editar') {
    if (isset($_POST['idregistros']) && isset($_POST['nombre_rol'])) {
        $consulta->query("UPDATE roles SET nombre_rol = '" . strtoupper($_POST['nombre_rol']) . "' WHERE id_rol = " . $_POST['idregistros']);
        echo "Rol actualizada correctamente";
    } else {
        echo "Faltan datos para actualizar la medida";
    }
}
if ($_POST['funcion'] == 'Eliminar') {
    if (isset($_POST['idregistros'])) {
        $consulta->query("UPDATE roles SET fechabaja = '" . date("Y-m-d H:i:s") . "' WHERE id_rol = " . $_POST['idregistros']);
        echo "Rol eliminado correctamente";
    } else {
        echo "Faltan datos para eliminar el rol";
    }
}

if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        $Auto = $consulta->query("SELECT * FROM roles WHERE id_rol=" . $_POST['id']);
        foreach ($Auto as $row)
            ;
    }
    $modal = "
        <div class='row'>
            <div class='col-9'>
                <div class='form-group'>
                    <b for='nombre'>Nombre</b>
                    <input type='text' class='form-control' id='nombre_rol' value='" . $row['nombre_rol'] . "'  name='nombre_rol'>
                </div>
            </div>
        </div>";
    echo $modal;
    exit();
}
?>