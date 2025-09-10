// --------------------
// Inicializar mapa centrado en Treinta y Tres
// --------------------
var map = L.map('map').setView([-33.233, -54.383], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// --------------------
// Verificar login
// --------------------
let usuario = JSON.parse(localStorage.getItem("usuarioLogueado"));
if (!usuario) {
    alert("Debes iniciar sesion para reportar.");
    window.location.href = "login.html";
}

// --------------------
// Cargar reportes guardados
// --------------------
let reportes = JSON.parse(localStorage.getItem("reportes")) || [];

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

// --------------------
// Marcador interactivo para nuevo reporte
// --------------------
let marcador;
map.on('click', function(e) {
    if (marcador) {
        marcador.setLatLng(e.latlng);
    } else {
        marcador = L.marker(e.latlng, {draggable: true}).addTo(map);
    }
});

// --------------------
// Evento formulario
// --------------------
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

// --------------------
// Función para guardar reporte
// --------------------
function guardarReporte(reporte) {
    reportes.push(reporte);
    localStorage.setItem("reportes", JSON.stringify(reportes));

    // Crear marcador en el mapa
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

    // Quitar marcador temporal
    marcador.remove();
    marcador = null;
}

// --------------------
// Zoom al hacer clic en el mapa
// --------------------
map.on('click', function(e) {
    map.setView(e.latlng, 15);
});
