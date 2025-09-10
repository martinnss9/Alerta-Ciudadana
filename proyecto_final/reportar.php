<?php
session_start();
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
if (!$usuario) {
    echo "Debes iniciar sesión para reportar. <a href='login.php'>Login</a>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alerta Ciudadana - Reportar</title>
    <!-- CSS propio -->
    <link rel="stylesheet" href="styles.css">
    <!-- CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Alerta Ciudadana </h1>
        <nav>
            <a href="inicio.html">Inicio</a>
            <a href="reportar.php">Reportar</a>
            <a href="misreportes.php">Mis Reportes</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main style="display: flex; gap: 20px;">
    <!-- Mapa a la izquierda -->
    <div id="map" style="flex: 2; height: 500px;"></div>

    <!-- Formulario a la derecha -->
    <div style="flex: 1;">
        <h2>Reportar un Problema</h2>
        <form id="reporteForm" enctype="multipart/form-data">
            <label for="descripcion">Descripcion del problema:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria">
                <option value="bache">Bache</option>
                <option value="alumbrado">Alumbrado</option>
                <option value="calle">Calle en mal estado</option>
            </select>

            <label for="foto">Subir foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Enviar Reporte</button>
        </form>
    </div>
</main>


    <footer>
        <p></p>
    </footer>

    <!-- JS Leaflet -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- JS propio -->
    <script>
        // Inicializar mapa
        var map = L.map('map').setView([-33.233, -54.383], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Cargar usuario logueado desde PHP
        var usuario = <?php echo json_encode($usuario); ?>;

        // Cargar reportes guardados en localStorage
        var reportes = JSON.parse(localStorage.getItem("reportes")) || [];

        // Mostrar reportes existentes en el mapa
        reportes.forEach(function(reporte) {
            let marker = L.marker([reporte.lat, reporte.lng]).addTo(map);
            let popup = `
                <strong>${reporte.categoria}</strong><br>
                ${reporte.descripcion}<br>
                Estado: ${reporte.estado}<br>
                Fecha: ${reporte.fecha}
            `;
            if (reporte.foto) {
                popup += `<br><img src="${reporte.foto}" width="150px">`;
            }
            marker.bindPopup(popup);
        });

        // Marcador interactivo
        var marcador;
        map.on('click', function(e) {
            if (marcador) {
                marcador.setLatLng(e.latlng);
            } else {
                marcador = L.marker(e.latlng, {draggable: true}).addTo(map);
            }
        });

        // Enviar reporte
        document.getElementById("reporteForm").addEventListener("submit", function(e) {
            e.preventDefault();

            let descripcion = document.getElementById("descripcion").value;
            let categoria = document.getElementById("categoria").value;
            let fotoInput = document.getElementById("foto");

            if (!marcador) {
                alert("Selecciona la ubicación del problema en el mapa.");
                return;
            }

            let coords = marcador.getLatLng();

            let reporte = {
                descripcion,
                categoria,
                lat: coords.lat,
                lng: coords.lng,
                fecha: new Date().toLocaleString(),
                estado: "Pendiente",
                usuario: usuario.email
            };

            // Guardar foto como base64 si existe
            if (fotoInput.files.length > 0) {
                let file = fotoInput.files[0];
                let reader = new FileReader();
                reader.onload = function(e) {
                    reporte.foto = e.target.result;
                    guardarReporte(reporte);
                };
                reader.readAsDataURL(file);
            } else {
                guardarReporte(reporte);
            }
        });

        function guardarReporte(reporte) {
            reportes.push(reporte);
            localStorage.setItem("reportes", JSON.stringify(reportes));

            let marker = L.marker([reporte.lat, reporte.lng]).addTo(map);
            let popup = `
                <strong>${reporte.categoria}</strong><br>
                ${reporte.descripcion}<br>
                Estado: ${reporte.estado}<br>
                Fecha: ${reporte.fecha}
            `;
            if (reporte.foto) {
                popup += `<br><img src="${reporte.foto}" width="150px">`;
            }
            marker.bindPopup(popup).openPopup();

            alert("Reporte enviado!");
            document.getElementById("reporteForm").reset();

            marcador.remove();
            marcador = null;
        }
    </script>
</body>
</html>
