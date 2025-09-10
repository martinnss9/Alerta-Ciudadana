<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inicio - Alerta Ciudadana</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
<h1>Alerta Ciudadana</h1>
<nav>
<a href="index.php">Inicio</a>
<a href="reportar.php">Reportar</a>
<a href="misreportes.php">Mis Reportes</a>
<a href="logout.php">Cerrar Sesion</a>
</nav>
</header>

<main>
<h2>Bienvenido, <?php echo htmlspecialchars($usuario); ?> 👋</h2>
<p>Usa el menu para reportar problemas y ver tus reportes.</p>
</main>

<footer>
<p>&copy; <?php echo date("Y"); ?> Alerta Ciudadana</p>
</footer>
</body>
</html>
