<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "spvabarrotera";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$user = $_POST['username'];
$pass = $_POST['password'];

// Consulta para verificar el usuario
$sql = "SELECT * FROM empleados WHERE usuario = '$user' AND contrasena = '$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Inicio de sesión exitoso
    $_SESSION['username'] = $user;
    header("Location: productos.php");
} else {
    // Credenciales incorrectas
    echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='login.html';</script>";
}

if($_POST['funcion'] == 'iniciar'){
    include("inc/conectar.php");
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $Auto = $consulta->query("SELECT * FROM empleados WHERE usuario LIKE  '$usuario' AND contrasena LIKE '".MD5($contrasena)."'");
    foreach ($Auto as $row);
    if($row['id_empleado']>0){
        echo $row['tipo'];
        $_SESSION['SISTEMA']['id_empleado'] = $row['id_empleado'];
        $_SESSION['SISTEMA']['rol'] = $row['id_rol'];
        $_SESSION['SISTEMA']['usuario'] = $row['usuario'];
    }else{  
        echo "error";
    }
    exit();
}
$conn->close();
?>