<?php
include('../inc/conectar.php');
if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT productos.*, categorias.nombre AS categoria, unidades_medida.nombre AS unidad_medida 
                             FROM productos 
                             LEFT JOIN categorias ON categorias.idcategorias = productos.id_categoria 
                             LEFT JOIN unidades_medida ON unidades_medida.id_unidad_medida = productos.id_unidad_medida 
                             WHERE productos.fechabaja IS NULL");
    $tabla = "";
    foreach ($Auto as $producto) {
        $tabla .= "<tr>
            <td>" . $producto['nombre'] . "</td>
            <td>" . $producto['codigo_barras'] . "</td>
            <td>$" . number_format($producto['precio'], 2) . "</td>
            <td>$" . number_format($producto['precio_venta'], 2) . "</td>
            <td>" . $producto['existencias'] . "</td>
            <td>" . $producto['unidad_medida'] . "</td>
            <td>" . $producto['categoria'] . "</td>
            <td>
            <button class='btn editar btn-sm' idregistros='" . $producto['id_productos'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button> 
            <button class='btn btn-danger btn-sm eliminar' idregistros='" . $producto['id_productos'] . "'style='background-color:rgb(203, 38, 38); color: white; border: none;'>Eliminar</button>
            </td>
        </tr>";
    }
    echo $tabla;
    exit();
}

if ($_POST['funcion'] == 'Guardar') {
    // Validar que el código de barras no exista
    $Auto = $consulta->query("SELECT * FROM productos WHERE codigo_barras='" . $_POST['codigo_barras'] . "'");
    foreach ($Auto as $producto);
    
    if ($producto['id_productos'] > 0) {
        echo "El código de barras ya existe";
        exit();
    }

    // Calcular precio de venta si no se proporciona (30% de margen)
    $precio_venta = isset($_POST['precio_venta']) ? $_POST['precio_venta'] : ($_POST['precio'] * 1.30);

    // Insertar nuevo producto
    $query = "INSERT INTO productos SET 
        codigo_barras='" . strtoupper($_POST['codigo_barras']) . "', 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        precio=" . $_POST['precio'] . ", 
        precio_venta=" . $precio_venta . ", 
        existencias=" . $_POST['existencias'] . ", 
        id_unidad_medida=" . $_POST['unidad_medida'] . ", 
        id_categoria=" . $_POST['categoria'];

    if ($consulta->query($query)) {
        echo "Producto insertado correctamente";
    }
    exit();
}

if ($_POST['funcion'] == 'Editar') {
    // Calcular precio de venta si no se proporciona (30% de margen)
    $precio_venta = isset($_POST['precio_venta']) ? $_POST['precio_venta'] : ($_POST['precio'] * 1.30);

    $Auto = $consulta->query("UPDATE productos SET 
        codigo_barras='" . strtoupper($_POST['codigo_barras']) . "', 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        precio=" . $_POST['precio'] . ", 
        precio_venta=" . $precio_venta . ", 
        existencias=" . $_POST['existencias'] . ", 
        id_unidad_medida=" . $_POST['unidad_medida'] . ", 
        id_categoria=" . $_POST['categoria'] . " 
        WHERE id_productos=" . $_POST['idregistros']);

    foreach ($Auto as $producto);
}

if ($_POST['funcion'] == 'Eliminar') {
    $Auto = $consulta->query("UPDATE productos SET fechabaja='" . date("Y-m-d H:i:s") . "' WHERE id_productos=" . $_POST['idregistros']);
    foreach ($Auto as $producto);
}

if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        $Auto = $consulta->query("SELECT * FROM productos WHERE id_productos=" . $_POST['id']);
        foreach ($Auto as $row);
    }

    $modal = "
        <div class='row'>
            <div class='col-3'>
                <div class='form-group'>
                    <b for='codigo_barras'>Código de Barras</b>
                    <input type='text' class='form-control' id='codigo_barras' value='" . $row['codigo_barras'] . "' name='codigo_barras'>
                </div>
            </div>
            <div class='col-9'>
                <div class='form-group'>
                    <b for='nombre'>Nombre</b>
                    <input type='text' class='form-control' id='nombre' value='" . $row['nombre'] . "'  name='nombre'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='precio'>Precio Unitario</b>
                    <input type='text' class='form-control' id='precio' value='" . $row['precio'] . "'  name='precio'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='precio_venta'>Precio de Venta</b>
                    <input type='text' class='form-control' id='precio_venta' value='" . $row['precio_venta'] . "'  name='precio_venta'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='existencias'>Existencias</b>
                    <input type='text' class='form-control' id='existencias' value='" . $row['existencias'] . "'  name='existencias'>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='unidad_medida'>Unidad de Medida</b>
                    <select class='form-control' id='unidad_medida' name='unidad_medida'>
                        <option value=''>Seleccione</option>";

    // Obtener unidades de medida
    $Auto = $consulta->query("SELECT * FROM unidades_medida WHERE fechabaja IS NULL");
    foreach ($Auto as $rowu) {
        $seleccionado = ($row['id_unidad_medida'] == $rowu['id_unidad_medida']) ? "selected" : "";
        $modal .= "<option value='" . $rowu['id_unidad_medida'] . "' $seleccionado>" . $rowu['nombre'] . "</option>";
    }
    $modal .= "</select>
                </div>
            </div>
            <div class='col-4'>
                <div class='form-group'>
                    <b for='categoria'>Categoría</b>
                    <select class='form-control' id='categoria' name='categoria'>
                        <option value=''>Seleccione</option>";

    // Obtener categorías
    $Auto = $consulta->query("SELECT * FROM categorias WHERE fechabaja IS NULL");
    foreach ($Auto as $rowc) {
        $seleccionado = ($row['id_categoria'] == $rowc['idcategorias']) ? "selected" : "";
        $modal .= "<option value='" . $rowc['idcategorias'] . "' $seleccionado>" . $rowc['nombre'] . "</option>";
    }
    $modal .= "</select>
                </div>
            </div>
        </div>";
    echo $modal;
    exit();
}
?>