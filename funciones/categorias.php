<?
include('../inc/conectar.php');
if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT * FROM categorias WHERE fechabaja IS NULL");
    foreach ($Auto as $categorias) {
        $tabla .= "<tr>
            <td>" . $categorias['nombre'] . "</td>
            <td>
            <button class='btn editar btn-sm' idregistros='" . $categorias['idcategorias'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button> 
            <button class='btn btn-danger  btn-sm eliminar' idregistros='" . $categorias['idcategorias'] . "' style='background-color:rgb(203, 38, 38); color: white; border: none;'>Eliminar</button>
            </td>
            <button class='btn editar btn-sm' idregistros='" . $producto['id_productos'] . "' >Editar</button> 
            <button class='btn btn-danger  btn-sm eliminar' idregistros='" . $producto['id_productos'] . "'>Eliminar</button>

        </tr>";
    }
    echo $tabla;
    exit();
}
if ($_POST['funcion'] == 'Guardar') {
    $Auto = $consulta->query("SELECT * FROM categorias WHERE nombre = '" . $_POST['nombre'] . "'");
    $categorias = $Auto->fetch();

    if ($categorias) {
        echo "El nombre ya existe";
        exit();
    }
    $consulta->query("INSERT INTO categorias (nombre) VALUES ('" . strtoupper($_POST['nombre']) . "')");
    echo "Categoría guardada correctamente";
}
if ($_POST['funcion'] == 'Editar') {
    if (isset($_POST['idregistros']) && isset($_POST['nombre'])) {
        $consulta->query("UPDATE categorias SET nombre = '" . strtoupper($_POST['nombre']) . "' WHERE idcategorias = " . $_POST['idregistros']);
        echo "Categoría actualizada correctamente";
    } else {
        echo "Faltan datos para actualizar la categoría";
    }
}
if ($_POST['funcion'] == 'Eliminar') {
    if (isset($_POST['idregistros'])) {
        $consulta->query("UPDATE categorias SET fechabaja = '" . date("Y-m-d H:i:s") . "' WHERE idcategorias = " . $_POST['idregistros']);
        echo "Categoría eliminada correctamente";
    } else {
        echo "Faltan datos para eliminar la categoría";
    }
}

if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        $Auto = $consulta->query("SELECT * FROM categorias WHERE idcategorias=" . $_POST['id']);
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