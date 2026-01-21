<?
include('../inc/conectar.php');
if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT * FROM unidades_medida WHERE fechabaja IS NULL");
    foreach ($Auto as $unidades_medida) {
        $tabla .= "<tr>
            <td>" . $unidades_medida['nombre'] . "</td>
            <td>
            <button class='btn editar btn-sm' idregistros='" . $unidades_medida['id_unidad_medida'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button>
            <button class='btn btn-danger btn-sm eliminar' idregistros='" . $unidades_medida['id_unidad_medida'] . "' style='background-color:rgb(203, 38, 38); color: white; border: none;'>Eliminar</button>
            </td>


            <button class='btn editar btn-sm' idregistros='" . $producto['id_productos'] . "' >Editar</button> 
            <button class='btn btn-danger   eliminar' idregistros='" . $producto['id_productos'] . "'>Eliminar</button>

        </tr>";
    }
    echo $tabla;
    exit();
}
if ($_POST['funcion'] == 'Guardar') {
    $Auto = $consulta->query("SELECT * FROM unidades_medida WHERE nombre = '" . $_POST['nombre'] . "'");
    $unidades_medida = $Auto->fetch();

    if ($unidades_medida) {
        echo "El nombre ya existe";
        exit();
    }
    $consulta->query("INSERT INTO unidades_medida (nombre) VALUES ('" . strtoupper($_POST['nombre']) . "')");
    echo "Medida guardada correctamente";
}
if ($_POST['funcion'] == 'Editar') {
    if (isset($_POST['idregistros']) && isset($_POST['nombre'])) {
        $consulta->query("UPDATE unidades_medida SET nombre = '" . strtoupper($_POST['nombre']) . "' WHERE id_unidad_medida = " . $_POST['idregistros']);
        echo "Medida actualizada correctamente";
    } else {
        echo "Faltan datos para actualizar la medida";
    }
}
if ($_POST['funcion'] == 'Eliminar') {
    if (isset($_POST['idregistros'])) {
        $consulta->query("UPDATE unidades_medida SET fechabaja = '" . date("Y-m-d H:i:s") . "' WHERE id_unidad_medida = " . $_POST['idregistros']);
        echo "Medida eliminada correctamente";
    } else {
        echo "Faltan datos para eliminar la medida";
    }
}

if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        $Auto = $consulta->query("SELECT * FROM unidades_medida WHERE id_unidad_medida=" . $_POST['id']);
        foreach ($Auto as $row)
            ;
    }
    $modal = "
        <div class='row'>
            <div class='col-9'>
                <div class='form-group'>
                    <b for='nombre'>Nombre</b>
                    <input type='text' class='form-control' id='nombre' value='" . $row['nombre'] . "'  name='nombre'>
                </div>
            </div>
        </div>";
    echo $modal;
    exit();
}
?>