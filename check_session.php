<?php
session_start();
if (!isset($_SESSION['SISTEMA']['id_empleado'])) {
    header("Location: login.php");
    exit();
}