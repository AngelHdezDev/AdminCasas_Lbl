<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de Mapa y Autocomplete</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #map { height: 400px; width: 100%; border-radius: 8px; }
    </style>
</head>
<body class="bg-gray-100 p-10">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Prueba de Ubicación</h2>

        <div class="mb-4">
            <label class="block font-bold">Buscar Dirección:</label>
            <input type="text" id="autocomplete" class="w-full border p-2 rounded shadow-sm" placeholder="Empieza a escribir una dirección...">
        </div>

        <div id="map" class="mb-6 border"></div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm">Dirección Completa</label>
                <input type="text" id="direccion" class="w-full border p-2 bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm">Código Postal</label>
                <input type="text" id="cp" class="w-full border p-2 bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm">Ciudad</label>
                <input type="text" id="ciudad" class="w-full border p-2 bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm">Estado</label>
                <input type="text" id="estado" class="w-full border p-2 bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm">Latitud</label>
                <input type="text" id="lat" class="w-full border p-2 bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm">Longitud</label>
                <input type="text" id="lng" class="w-full border p-2 bg-gray-50" readonly>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFEdmq9JH19Llzt3Wy8-XkTjqb4hV35lo&libraries=places"></script>

    <script>
        let map;
        let marker;
        let autocomplete;

        function initAutocomplete() {
            // Centro inicial: Guadalajara
            const gdl = { lat: 20.659698, lng: -103.349609 };

            map = new google.maps.Map(document.getElementById("map"), {
                center: gdl,
                zoom: 13,
            });

            marker = new google.maps.Marker({
                position: gdl,
                map: map,
                draggable: true // Permite mover el pin manualmente
            });

            const input = document.getElementById("autocomplete");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            // Al seleccionar una dirección del buscador
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    return;
                }

                // Mover el mapa y el marcador
                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);

                fillInputs(place);
            });

            // Al mover el marcador manualmente
            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                const geocoder = new google.maps.Geocoder();
                
                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        fillInputs(results[0]);
                        input.value = results[0].formatted_address;
                    }
                });
            });
        }

        function fillInputs(place) {
            // Guardar Lat/Lng
            document.getElementById("lat").value = place.geometry.location.lat();
            document.getElementById("lng").value = place.geometry.location.lng();
            document.getElementById("direccion").value = place.formatted_address;

            // Limpiar campos antes de rellenar
            document.getElementById("cp").value = "";
            document.getElementById("ciudad").value = "";
            document.getElementById("estado").value = "";

            // Mapear componentes de la dirección
            for (const component of place.address_components) {
                const type = component.types[0];

                if (type === "postal_code") {
                    document.getElementById("cp").value = component.long_name;
                }
                if (type === "locality") {
                    document.getElementById("ciudad").value = component.long_name;
                }
                if (type === "administrative_area_level_1") {
                    document.getElementById("estado").value = component.long_name;
                }
            }
        }

        google.maps.event.addDomListener(window, 'load', initAutocomplete);
    </script>
</body>
</html>