<?php
session_start();
include "conexiones.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['nombre'];
    $gmail = $_POST['email'];
    $contrasela = $_POST['password'];

    // Revisar si correo ya existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Gmail = ?");
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Ese correo ya esta registrado";
    } else {
        // Insertar usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (Usuario, Contrasela, Gmail) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $contrasela, $gmail);
        if ($stmt->execute()) {
            $success = "Registro exitoso. Ahora puedes iniciar sesion.";
        } else {
            $error = "Error al registrar usuario. Intenta nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro - Alerta Ciudadana</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
<h1>Alerta Ciudadana</h1>
<nav>
<a href="index.php">Inicio</a>
<a href="reportar.php">Reportar</a>
<a href="misreportes.php">Mis Reportes</a>
<a href="login.php">Login</a>
</nav>
</header>

<main>
<h2>Registrarse</h2>

<?php 
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST" action="register.php">
<label for="nombre">Nombre:</label>
<input type="text" id="nombre" name="nombre" required>

<label for="email">Correo:</label>
<input type="email" id="email" name="email" required>

<label for="password">Contraseña:</label>
<input type="password" id="password" name="password" required>

<button type="submit">Crear Cuenta</button>
</form>

<p>Ya tienes cuenta? <a href="login.php">Inicia sesion aqui</a></p>
</main>

<footer>
<p>&copy; <?php echo date("Y"); ?> Alerta Ciudadana</p>
</footer>
</body>
</html>
