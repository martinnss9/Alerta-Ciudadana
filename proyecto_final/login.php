<?php
session_start();
include "conexiones.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gmail = $_POST['email'];
    $contrasela = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Gmail = ? AND Contrasela = ?");
    $stmt->bind_param("ss", $gmail, $contrasela);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['Usuario'];
        $_SESSION['gmail'] = $user['Gmail'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Correo o contrasela incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - Alerta Ciudadana</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
<h1>Alerta Ciudadana</h1>
<nav>
<a href="index.php">Inicio</a>
<a href="reportar.php">Reportar</a>
<a href="misreportes.php">Mis Reportes</a>
</nav>
</header>

<main>
<h2>Iniciar Sesion</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" action="login.php">
<label for="email">Correo:</label>
<input type="email" id="email" name="email" required>

<label for="password">Contraseña:</label>
<input type="password" id="password" name="password" required>

<button type="submit">Ingresar</button>
</form>

<p>Si no tienes cuenta, <a href="register.php">Registrate aqui</a></p>
</main>

<footer>
<p>&copy; <?php echo date("Y"); ?> Alerta Ciudadana</p>
</footer>
</body>
</html>
