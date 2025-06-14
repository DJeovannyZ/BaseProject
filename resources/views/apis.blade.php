<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Geolocalización con Leaflet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 20px;
        }
        #map {
            height: 400px;
            width: 100%;
            border: 2px solid #adadad;
            margin-top: 15px;
        }
        .controls {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .controls label, .controls input, .controls button {
            margin-right: 10px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="text-center d-flex justify-content-center align-items-center vh-50 flex-column">

        <div class="shadow-lg p-3 mb-4 bg-body-tertiary rounded mt-3 w-50 p-3">
            <h1>
                Geolocalización con Leaflet
            </h1>
        </div>

        <div class="alert alert-warning p-3 w-50" role="alert">
            Para que funcione correctamente, asegúrate de permitir el acceso a tu ubicación cuando se te solicite.
        </div>

    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div id="map"></div>
            </div>
            <div class="col-md-4">

                <div class="alert alert-secondary mt-3" role="alert">
                    <h4>Coordenadas geográficas</h4>
                </div>

                <table class="table table-hover table-bordered border-secondary">
                    <thead class="table-active">
                        <tr>
                            <th scope="col"><Table>Tipo</Table></th>
                            <th scope="col">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Latitud:</th>
                            <td><span id="lat"></span></td>
                        </tr>
                        <tr>
                            <th scope="row">Longitud:</th>
                            <td><span id="lon"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        try {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    try {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        document.getElementById('lat').textContent = lat;
                        document.getElementById('lon').textContent = lon;

                        const map = L.map('map').setView([lat, lon], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(map);

                        L.marker([lat, lon]).addTo(map)
                            .bindPopup("Esta es tu ubicación actual").openPopup();
                    } catch (mapError) {
                        alert("Error al mostrar el mapa: " + mapError.message);
                        console.error(mapError);
                    }
                },
                function (error) {
                    alert("Error obteniendo ubicación: " + error.message);
                    console.error(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } catch (geoError) {
            alert("Error general al acceder a la geolocalización.");
            console.error(geoError);
        }
    </script>

<div class="container-fluid mt-5">
    <div class="col-md-12">

        <div class="alert alert-info mt-3 text-center" role="alert">
            <h4>Captura desde la cámara web</h4>
        </div>

        <video id="video" width="100%" height="240" autoplay class="border border-secondary mb-2"></video>

        <div class="d-grid gap-2">
            <button class="btn btn-primary" id="snap">Tomar Foto</button>
            <a id="download" download="captura.jpg" class="btn btn-success mt-2 d-none">Descargar Imagen</a>
        </div>

        <canvas id="photoCanvas" width="640" height="480" class="mt-3 border border-secondary w-100"></canvas>
    </div>
</div>

<!-- Script Camara -->
<script>
    const video = document.getElementById('video');
    const photoCanvas = document.getElementById('photoCanvas');
    const snapBtn = document.getElementById('snap');
    const downloadLink = document.getElementById('download');
    const photoContext = photoCanvas.getContext('2d');

    // Acceder a la cámara
    try {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
            })
            .catch(function(error) {
                alert("Error al acceder a la cámara: " + error.message);
                console.error("Error al acceder a la cámara:", error);
            });
    } catch (error) {
        alert("Error al intentar acceder a la cámara: " + error.message);
        console.error("Error general:", error);
    }

    // Capturar foto y mostrar
    snapBtn.addEventListener('click', function () {
        try {
            photoContext.drawImage(video, 0, 0, photoCanvas.width, photoCanvas.height);
        } catch (error) {
            alert("Error al capturar la imagen: " + error.message);
            return;
        }

        const dataUrl = photoCanvas.toDataURL('image/jpeg');
        downloadLink.href = dataUrl;
        downloadLink.classList.remove('d-none');
    });
</script>

<!-- Sección Canvas de Dibujo -->
<div class="container-fluid mt-5">
    <div class="col-md-12">
        <div class="alert alert-info mt-3 text-center" role="alert">
            <h4>🎨 Dibuja libremente</h4>
        </div>

        <canvas id="drawingCanvas" width="800" height="500" class="border border-secondary w-100"></canvas>

        <div class="controls mt-3">
            <label for="color">Color:</label>
            <input type="color" id="color" value="#000000">

            <label for="size">Grosor:</label>
            <input type="range" id="size" min="1" max="20" value="2">

            <button class="btn btn-warning" id="borrador">🧽 Borrador</button>
            <button class="btn btn-danger" id="limpiar">🔄 Limpiar todo</button>
            <button class="btn btn-success" id="guardar">💾 Guardar dibujo</button>
        </div>
    </div>
</div>

<!-- Script Canvas de Dibujo -->
<script>
    // Canvas de dibujo
    const drawingCanvas = document.getElementById('drawingCanvas');
    const ctx = drawingCanvas.getContext('2d');
    const colorPicker = document.getElementById('color');
    const sizeSlider = document.getElementById('size');
    const borradorBtn = document.getElementById('borrador');
    const limpiarBtn = document.getElementById('limpiar');
    const guardarBtn = document.getElementById('guardar');

    let isDrawing = false;
    let currentColor = colorPicker.value;
    let currentSize = sizeSlider.value;

    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, drawingCanvas.width, drawingCanvas.height);
    ctx.strokeStyle = currentColor;
    ctx.lineWidth = currentSize;
    ctx.lineCap = 'round';

    colorPicker.addEventListener('input', (e) => {
        currentColor = e.target.value;
        ctx.strokeStyle = currentColor;
        ctx.globalCompositeOperation = 'source-over';
    });

    sizeSlider.addEventListener('input', (e) => {
        currentSize = e.target.value;
        ctx.lineWidth = currentSize;
    });

    borradorBtn.addEventListener('click', () => {
        ctx.globalCompositeOperation = 'destination-out';
    });

    limpiarBtn.addEventListener('click', () => {
        if(confirm('¿Estás seguro de que quieres limpiar todo el dibujo?')) {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, drawingCanvas.width, drawingCanvas.height);
            ctx.strokeStyle = currentColor;
            ctx.globalCompositeOperation = 'source-over';
        }
    });

    guardarBtn.addEventListener('click', () => {
        const link = document.createElement('a');
        link.download = 'dibujo.png';
        link.href = drawingCanvas.toDataURL('image/png');
        link.click();
    });

    // Eventos de dibujo
    drawingCanvas.addEventListener('mousedown', startDrawing);
    drawingCanvas.addEventListener('mousemove', draw);
    drawingCanvas.addEventListener('mouseup', stopDrawing);
    drawingCanvas.addEventListener('mouseout', stopDrawing);


    function handleTouchStart(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        drawingCanvas.dispatchEvent(mouseEvent);
    }

    function handleTouchMove(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        drawingCanvas.dispatchEvent(mouseEvent);
    }

    function startDrawing(e) {
        isDrawing = true;
        draw(e);
    }

    function draw(e) {
        if (!isDrawing) return;

        // Obtener coordenadas correctas considerando el desplazamiento del canvas
        const rect = drawingCanvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    function stopDrawing() {
        isDrawing = false;
        ctx.beginPath();
    }
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"></script>

</body>
</html>
