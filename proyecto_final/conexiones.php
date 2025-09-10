<?php
// Datos de la base de datos
$host = "localhost";
$user = "root";           // Usuario XAMPP
$password = "";           // Contraseña XAMPP (normalmente vacía)
$database = "registro";   // Tu base de datos

// Crear conexion
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexion
if ($conn->connect_error) {
    die("Conexion fallida: " . $conn->connect_error);
}

// Charset utf8
$conn->set_charset("utf8");
?>
