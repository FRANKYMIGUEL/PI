<?
include('../inc/conectar.php');
if ($_POST['funcion'] == "Tabla") {
    $Auto = $consulta->query("SELECT empleados.*, roles.nombre_rol AS rol 
FROM empleados 
LEFT JOIN roles ON roles.id_rol = empleados.id_rol 
WHERE empleados.fechabaja IS NULL;
");
    $tabla = "";
    foreach ($Auto as $empleado) {
        $tabla .= "<tr>
            <td>" . $empleado['usuario'] . "</td>
            <td>" . $empleado['nombre'] . "</td>
            <td>" . $empleado['telefono'] . "</td>
            <td>" . $empleado['rol'] . "</td>
            <td>
            <button class='btn editar btn-sm' idregistros='" . $empleado['id_empleado'] . "' style='background-color: #2973B2; color: white; border: none;'>Editar</button> 
            <button class='btn btn-danger btn-sm eliminar' idregistros='" . $empleado['id_empleado'] . "' style='background-color:rgb(203, 38, 38); color: white; border: none;'>Eliminar</button>
            </td>
        </tr>";
    }
    echo $tabla;
    exit();
}
if ($_POST['funcion'] == 'Guardar') {
    // Consulta para validar que el usuario no exista
    $Auto = $consulta->query("SELECT * FROM empleados WHERE usuario='" . $_POST['usuario'] . "'");
    foreach ($Auto as $empleado)
        ;

    if (!empty($empleado['id_empleado'])) {
        echo "El usuario ya existe";
        exit();
    }

    // Insertar nuevo empleado sin encriptar la contraseña
    $query = "INSERT INTO empleados SET 
        usuario='" . $_POST['usuario'] . "', 
        contrasena='" . $_POST['contrasena'] . "', 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        telefono='" . $_POST['telefono'] . "', 
        id_rol=" . $_POST['id_rol'];

    if ($consulta->query($query)) {
        echo "Empleado insertado correctamente";
    } else {
        echo "Error al insertar empleado: " . $consulta->error;
    }
    exit();
}


if ($_POST['funcion'] == 'Editar') {
    // Verificar que el ID es válido
    if (!isset($_POST['idregistros']) || empty($_POST['idregistros']) || !is_numeric($_POST['idregistros'])) {
        echo "ID no válido";
        exit();
    }

    $id = intval($_POST['idregistros']);

    // Actualizar empleado sin cambiar la contraseña
    $query = "UPDATE empleados SET 
        usuario='" . $_POST['usuario'] . "', 
        nombre='" . strtoupper($_POST['nombre']) . "', 
        telefono='" . $_POST['telefono'] . "', 
        id_rol=" . $_POST['id_rol'] . " 
        WHERE id_empleado=" . $id;

    if ($consulta->query($query)) {
        echo "Empleado actualizado correctamente";
    } else {
        echo "Error al actualizar empleado: " . $consulta->error;
    }

    // Si se ingresó una nueva contraseña, actualizarla SIN ENCRIPTAR
    if (!empty($_POST['contrasena'])) {
        $nuevaContrasena = $_POST['contrasena']; // Se almacena en texto plano
        $consulta->query("UPDATE empleados SET contrasena='" . $nuevaContrasena . "' WHERE id_empleado=" . $id);
    }

    exit();
}

if ($_POST['funcion'] == 'Eliminar') {
    $consulta->query("UPDATE empleados SET fechabaja='" . date("Y-m-d H:i:s") . "' WHERE id_empleado=" . $_POST['idregistros']);
}



if ($_POST['funcion'] == "Modal") {
    if ($_POST['tipo'] == "Editar") {
        // Obtener datos del producto para editar
        $Auto = $consulta->query("SELECT * FROM empleados WHERE id_empleado=" . $_POST['id']);
        foreach ($Auto as $row)
            ;
    }

    // Generar el formulario modal para empleados
    $modal = "
<div class='row'>
    <div class='col-6'>
        <div class='form-group'>
            <b for='usuario'>Usuario</b>
            <input type='text' class='form-control' id='usuario' value='" . $row['usuario'] . "' name='usuario'>
        </div>
    </div>
    <div class='col-6'>
        <div class='form-group'>
            <b for='contrasena'>Contraseña</b>
            <input type='password' class='form-control' id='contrasena'value='" . $row['contrasena'] . "'name='contrasena'>
        </div>
    </div>
    <div class='col-6'>
        <div class='form-group'>
            <b for='nombre'>Nombre</b>
            <input type='text' class='form-control' id='nombre' value='" . $row['nombre'] . "' name='nombre'>
        </div>
    </div>
    <div class='col-6'>
        <div class='form-group'>
            <b for='telefono'>Teléfono</b>
            <input type='text' class='form-control' id='telefono' value='" . $row['telefono'] . "' name='telefono'>
        </div>
    </div>
    <div class='col-6'>
        <div class='form-group'>
            <b for='id_rol'>Rol</b>
            <select class='form-control' id='id_rol' name='id_rol'>
                <option value=''>Seleccione</option>";

    // Obtener los roles activos
    $Auto = $consulta->query("SELECT * FROM roles where fechabaja  is null");
    foreach ($Auto as $rowr) {
        $seleccionado = ($row['id_rol'] == $rowr['id_rol']) ? "selected" : "";
        $modal .= "<option value='" . $rowr['id_rol'] . "' $seleccionado>" . $rowr['nombre_rol'] . "</option>";
    }

    $modal .= "</select>
        </div>
    </div>
</div>";

    echo $modal;
    exit();
}
?>