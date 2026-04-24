@extends('layouts.app')


@section('title', 'Propiedades')
<title>Nueva Propiedad | CTP Realty</title>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/createPropiedad.css') }}">
    <style>
        /* Aseguramos que el contenedor de resultados de Google no choque con tu estilo */
        .pac-container {
            z-index: 10000 !important;
        }
    </style>
@endpush

@section('content')

    <body>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

        <!-- Main Content -->
        <main class="main-content">
            <form action="{{ route('propiedades.store') }}" method="POST" id="formPropiedad">
                @csrf

                <div class="grid-sidebar">

                    <!-- Left Column -->
                    <div class="left-column">

                        <!-- Publicación -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Datos de Publicación</h4>
                            </div>
                            <div class="field">
                                <label class="field-label">
                                    Título <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="title" id="title"
                                    placeholder="Ej: Casa moderna con alberca en Zapopan" required>
                            </div>
                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">
                                    Tipo de propiedad <span class="required">*</span>
                                </label>
                                <select class="field-input" name="type" id="type" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="house">Casa</option>
                                    <option value="apartment">Departamento</option>
                                    <option value="land">Terreno</option>
                                    <option value="commercial">Local Comercial</option>
                                </select>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Ubicación en mapa</h4>
                            </div>
                            <div class="map-search">
                                <i class="bi bi-search"></i>
                                <input type="text" id="address-search" class="field-input"
                                    placeholder="Busca una calle o colonia..." autocomplete="off">
                            </div>
                            <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                            <p class="map-hint">
                                <i class="bi bi-info-circle"></i>
                                También puedes hacer clic en el mapa o arrastrar el marcador.
                            </p>
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="lng">
                        </div>

                        <!-- Dimensiones -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Dimensiones y distribución</h4>
                            </div>
                            <div class="grid-2">
                                <div class="field">
                                    <label class="field-label">
                                        Terreno (m²) <span class="required">*</span>
                                    </label>
                                    <input type="number" class="field-input" name="m2_land" id="m2_land" placeholder="0"
                                        required>
                                </div>
                                <div class="field">
                                    <label class="field-label">
                                        Construcción (m²) <span class="required">*</span>
                                    </label>
                                    <input type="number" class="field-input" name="m2_construction" id="m2_construction"
                                        placeholder="0" required>
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="field">
                                    <label class="field-label">Habitaciones</label>
                                    <input type="number" class="field-input" name="bedrooms" id="bedrooms" min="0"
                                        value="0">
                                </div>
                                <div class="field">
                                    <label class="field-label">Baños</label>
                                    <input type="number" class="field-input" name="bathrooms" id="bathrooms" min="0"
                                        value="0">
                                </div>
                                <div class="field" style="margin-bottom:0">
                                    <label class="field-label">Cochera</label>
                                    <input type="number" class="field-input" name="parking_spots" id="parking_spots" min="0"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Amenidades -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Amenidades disponibles</h4>
                            </div>
                            <div class="amenities-grid">
                                @foreach($amenities as $amenity)
                                    <label class="amenity-item">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}">
                                        @if(str_starts_with($amenity->icon, 'bi'))
                                            <i class="bi {{ $amenity->icon }}"></i>
                                        @else
                                            <span>{{ $amenity->icon }}</span>
                                        @endif
                                        <span>{{ $amenity->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="amenities-hint">
                                <i class="bi bi-info-circle"></i>
                                Selecciona todas las características que incluye la propiedad.
                            </p>
                        </div>

                        <!-- Descripción -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Detalles Adicionales</h4>
                            </div>

                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">
                                    Descripción <span style="color:var(--text-muted);font-weight:400">(opcional)</span>
                                </label>
                                <textarea class="field-input" name="description" id="description" rows="4"
                                    placeholder="Menciona amenidades, acabados, cercanía a puntos de interes..."></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column (Sidebar) -->
                    <div class="right-column">

                        <!-- Datos de Ubicación -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Datos de ubicación</h4>
                                <span class="card-badge">
                                    <i class="bi bi-magic"></i> Auto
                                </span>
                            </div>

                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">
                                    Dirección <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="address" id="address"
                                    placeholder="Se detecta del mapa" required>
                            </div>

                            <div class="field">
                                <label class="field-label">
                                    Colonia <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="neighborhood" id="neighborhood"
                                    placeholder="Se detecta del mapa" required>
                            </div>

                            <div class="field">
                                <label class="field-label">
                                    Código postal <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="cp" id="cp" placeholder="Se detecta del mapa"
                                    maxlength="5" required>
                            </div>
                            <div class="grid-2">
                                <div class="field">
                                    <label class="field-label">
                                        Ciudad <span class="required">*</span>
                                    </label>
                                    <input type="text" class="field-input" name="city" id="city" placeholder="—" readonly
                                        required>
                                </div>
                                <div class="field">
                                    <label class="field-label">
                                        Estado <span class="required">*</span>
                                    </label>
                                    <input type="text" class="field-input" name="state" id="state" placeholder="—" readonly
                                        required>
                                </div>

                            </div>
                        </div>

                        <!-- Comercialización -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Comercialización</h4>
                            </div>
                            <div class="grid-2">
                                <div class="field">
                                    <label class="field-label">
                                        Tipo de contrato <span class="required">*</span>
                                    </label>
                                    <select class="field-input" name="contract_type" id="contract_type" required>
                                        <option value="sale">Venta</option>
                                        <option value="rent">Renta</option>
                                        <option value="consignment">Consignación</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label">
                                        Precio <span class="required">*</span>
                                    </label>
                                    {{-- Input visual con formato --}}
                                    <input type="text" class="field-input" id="price_mask" placeholder="$ 0.00" required>

                                    {{-- Input real que se envía al servidor (mantiene el name="price") --}}
                                    <input type="hidden" name="price" id="price"
                                        value="{{ old('price', $propiedad->price ?? '') }}">
                                </div>
                            </div>
                            <div class="toggle-list">
                                <div class="toggle-item">
                                    <div class="toggle-info">
                                        <div class="toggle-icon">
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <div>
                                            <div class="toggle-title">Propiedad destacada</div>
                                            <div class="toggle-desc">Aparecerá en primeros resultados</div>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="is_featured" value="1">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="toggle-item">
                                    <div class="toggle-info">
                                        <div class="toggle-icon">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div>
                                            <div class="toggle-title">Dirección pública</div>
                                            <div class="toggle-desc">Mostrar calle y número en la web</div>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="show_public_address" value="1" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Asignación -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Asignación</h4>
                            </div>
                            <div class="field">
                                <label class="field-label">Vendedor</label>
                                <select class="field-input" name="seller_id" id="seller_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">Cliente</label>
                                <select class="field-input" name="client_id" id="client_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Form Footer -->
                <div class="form-footer">
                    <span class="footer-note">
                        <i class="bi bi-shield-check"></i>
                        Los campos con <span class="req-dot">*</span> son requeridos
                    </span>
                    <div class="btn-group">
                        <a href="{{ route('propiedades.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i>
                            Registrar propiedad
                        </button>
                    </div>
                </div>

            </form>
        </main>
    </body>
@endsection

@push('scripts')

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>

    <script>
        let map, marker, autocomplete, geocoder;

        function initMap() {
            const initialPos = { lat: 20.6596, lng: -103.3496 }; // GDL

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 14,
                mapTypeControl: false
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true
            });

            geocoder = new google.maps.Geocoder();

            // Autocomplete
            const searchInput = document.getElementById("address-search");
            autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                fillAddressFields(place);
            });

            // Al arrastrar el pin
            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        fillAddressFields(results[0]);
                        searchInput.value = results[0].formatted_address;
                    }
                });
            });

            // Al hacer clic en el mapa
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                geocoder.geocode({ location: e.latLng }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        fillAddressFields(results[0]);
                        searchInput.value = results[0].formatted_address;
                    }
                });
            });
        }

        function fillAddressFields(place) {
            // Lat/Lng
            document.getElementById("lat").value = place.geometry.location.lat();
            document.getElementById("lng").value = place.geometry.location.lng();

            // Resetear campos
            document.getElementById("cp").value = "";
            document.getElementById("state").value = "";
            document.getElementById("city").value = "";
            document.getElementById("neighborhood").value = "";
            document.getElementById("address").value = "";

            let streetName = "";
            let streetNumber = "";

            place.address_components.forEach(component => {
                const types = component.types; // Usamos el array completo de tipos

                // Código Postal
                if (types.includes("postal_code")) {
                    document.getElementById("cp").value = component.long_name;
                }
                // Estado
                if (types.includes("administrative_area_level_1")) {
                    document.getElementById("state").value = component.long_name;
                }
                // Ciudad (Municipio)
                if (types.includes("locality")) {
                    document.getElementById("city").value = component.long_name;
                }

                // --- OBTENER COLONIA ---
                // Google suele enviar la colonia en una de estas 3 etiquetas:
                // 1. sublocality_level_1 (La más común en ciudades grandes como GDL)
                // 2. neighborhood (Colonias específicas o barrios)
                // 3. sublocality (Genérico)
                if (types.includes("sublocality_level_1") ||
                    types.includes("neighborhood") ||
                    types.includes("sublocality")) {

                    document.getElementById("neighborhood").value = component.long_name;
                }

                // Dirección (Calle y Número)
                if (types.includes("route")) streetName = component.long_name;
                if (types.includes("street_number")) streetNumber = component.long_name;
            });

            document.getElementById("address").value = `${streetName} ${streetNumber}`.trim();
        }

        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maskInput = document.getElementById('price_mask');
            const realInput = document.getElementById('price');

            // Función para dar formato de moneda (MXN)
            const formatter = new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
                minimumFractionDigits: 2
            });

            // Si ya hay un valor (edición), formatearlo al cargar
            if (realInput.value) {
                maskInput.value = formatter.format(realInput.value);
            }

            maskInput.addEventListener('input', function (e) {
                // 1. Limpiar el valor de todo lo que no sea número
                let value = e.target.value.replace(/[^\d.]/g, '');

                // 2. Evitar múltiples puntos decimales
                const parts = value.split('.');
                if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');

                // 3. Guardar el valor limpio en el input oculto para Laravel
                realInput.value = value;
            });

            maskInput.addEventListener('blur', function (e) {
                // Al salir del campo, aplicamos el formato visual final: $ 4,334,335.00
                const numericValue = parseFloat(realInput.value);
                if (!isNaN(numericValue)) {
                    e.target.value = formatter.format(numericValue);
                }
            });

            maskInput.addEventListener('focus', function (e) {
                // Al entrar al campo, quitamos el símbolo de pesos y comas para facilitar la edición
                if (realInput.value) {
                    e.target.value = realInput.value;
                }
            });
        });
    </script>
@endpush