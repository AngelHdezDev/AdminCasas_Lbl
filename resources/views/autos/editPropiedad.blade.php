@extends('layouts.app')
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

        </main>
        <main class="main-content">
            <form action="{{ route('propiedades.update', $property->id) }}" method="POST" id="formPropiedad">
                @csrf
                @method('PUT')
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
                                    placeholder="Ej: Casa moderna con alberca en Zapopan"
                                    value="{{ old('title', $property->title) }}" required>
                            </div>
                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">
                                    Tipo de propiedad <span class="required">*</span>
                                </label>
                                <select class="field-input" name="type" id="type" required>
                                    <option value="house" @selected(old('type', $property->type) == 'house')>Casa</option>
                                    <option value="apartment" @selected(old('type', $property->type) == 'apartment')>
                                        Departamento</option>
                                    <option value="land" @selected(old('type', $property->type) == 'land')>Terreno</option>
                                    <option value="commercial" @selected(old('type', $property->type) == 'commercial')>Local
                                        Comercial</option>
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
                            <input type="hidden" name="latitude" id="lat"
                                value="{{ old('latitude', $property->latitude) }}">
                            <input type="hidden" name="longitude" id="lng"
                                value="{{ old('longitude', $property->longitude) }}">
                        </div>

                        <!-- Dimensiones -->
                        <div class="card">
                            <div class="mb-4 border-l-4 border-blue-500 pl-3 py-1">
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Dimensiones y distribución</h4>
                            </div>

                            <div class="grid-2">
                                <div class="field">
                                    <label class="field-label">Terreno (m²) <span class="required">*</span></label>
                                    <input type="text" class="field-input m2-mask" id="m2_land_display" placeholder="0"
                                        required>
                                    <input type="hidden" name="m2_land" id="m2_land"
                                        value="{{ old('m2_land', $property->m2_land) }}">
                                </div>

                                <div class="field">
                                    <label class="field-label">Construcción (m²) <span class="required">*</span></label>
                                    <input type="text" class="field-input m2-mask" id="m2_construction_display"
                                        placeholder="0" required>
                                    <input type="hidden" name="m2_construction" id="m2_construction"
                                        value="{{ old('m2_construction', $property->m2_construction) }}">
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="field">
                                    <label class="field-label">Habitaciones</label>
                                    <input type="number" class="field-input" name="bedrooms" id="bedrooms" min="0"
                                        value="{{ old('bedrooms', $property->bedrooms) }}">
                                </div>
                                <div class="field">
                                    <label class="field-label">Baños</label>
                                    <input type="number" class="field-input" name="bathrooms" id="bathrooms" min="0"
                                        value="{{ old('bathrooms', $property->bathrooms) }}">
                                </div>
                                <div class="field" style="margin-bottom:0">
                                    <label class="field-label">Cochera</label>
                                    <input type="number" class="field-input" name="parking_spots" id="parking_spots" min="0"
                                        value="{{ old('parking_spots', $property->parking_spots) }}">
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
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                            @checked(in_array($amenity->id, old('amenities', $property->amenities->pluck('id')->toArray())))>
                                        <span class="emoji-icon">{{ $amenity->icon }}</span>
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
                                <h4 class="text-sm font-bold text-gray-800 uppercase">Detalles adicionales</h4>
                            </div>
                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">
                                    Descripción <span style="color:var(--text-muted);font-weight:400">(opcional)</span>
                                </label>
                                <textarea class="field-input" name="description" id="description"
                                    rows="4">{{ old('description', $property->description) }}</textarea>
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
                                    placeholder="Se detecta del mapa" required
                                    value="{{ old('address', $property->address) }}">
                            </div>

                            <div class="field">
                                <label class="field-label">
                                    Colonia <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="neighborhood" id="neighborhood"
                                    placeholder="Se detecta del mapa" required
                                    value="{{ old('neighborhood', $property->neighborhood) }}">
                            </div>

                            <div class="field">
                                <label class="field-label">
                                    Código postal <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="cp" id="cp" placeholder="Se detecta del mapa"
                                    maxlength="5" required value="{{ old('cp', $property->cp) }}">
                            </div>
                            <div class="grid-2">

                                <div class="field">
                                    <label class="field-label">
                                        Ciudad <span class="required">*</span>
                                    </label>
                                    <input type="text" class="field-input" name="city" id="city" placeholder="—" readonly
                                        required value="{{ old('city', $property->city) }}">
                                </div>

                                <div class="field">
                                    <label class="field-label">
                                        Estado <span class="required">*</span>
                                    </label>
                                    <input type="text" class="field-input" name="state" id="state" placeholder="—" readonly
                                        required value="{{ old('state', $property->state) }}">
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
                                        <option value="sale" @selected(old('contract_type', $property->contract_type) == 'sale')>Venta</option>
                                        <option value="rent" @selected(old('contract_type', $property->contract_type) == 'rent')>Renta</option>
                                        <option value="consignment" @selected(old('contract_type', $property->contract_type) == 'consignment')>Consignación</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label">
                                        Precio <span class="required">*</span>
                                    </label>

                                    <input type="text" class="field-input" id="price_display" placeholder="$ 0.00" required>

                                    <input type="hidden" name="price" id="price_hidden"
                                        value="{{ old('price', $property->price) }}">
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
                                        <input type="hidden" name="is_featured" value="0">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $property->is_featured) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    @error('is_featured')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
                                        <input type="hidden" name="show_public_address" value="0">
                                        <input type="checkbox" name="show_public_address" value="1"
                                            @checked(old('show_public_address', $property->show_public_address))>
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
                                        <option value="{{ $vendedor->id }}" @selected(old('seller_id', $property->seller_id) == $vendedor->id)>
                                            {{ $vendedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">Cliente</label>
                                <select class="field-input" name="client_id" id="client_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" @selected(old('client_id', $property->client_id) == $cliente->id)>
                                            {{ $cliente->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('galeria.index') }}" class="btn btn-primary">Ir a galería</a>
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
                            Actualizar propiedad
                        </button>
                    </div>
                </div>

            </form>
        </main>
    </body>
    @if($errors->has('is_featured'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Límite de destacados alcanzado',
                    text: "{{ $errors->first('is_featured') }}",
                    icon: 'warning',
                    confirmButtonColor: '#c0392b',
                    confirmButtonText: 'Entendido'
                });
            });
        </script>
    @endif
@endsection


@push('scripts')

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>

    <script>
        let map, marker, autocomplete, geocoder;

        function initMap() {
            const initialLat = {{ old('latitude', $property->latitude) ?? 20.6596 }};
            const initialLng = {{ old('longitude', $property->longitude) ?? -103.3496 }};

            const initialPos = { lat: initialLat, lng: initialLng }; // GDL

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 18,
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
            // Al final de tu función initMap() agrega esto para rellenar la barra de búsqueda al cargar:
            document.getElementById("address-search").value = "{{ old('address', $property->address) }}";
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
            const displayInput = document.getElementById('price_display');
            const hiddenInput = document.getElementById('price_hidden');

            // Configuración del formateador de moneda MXN
            const formatter = new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
                minimumFractionDigits: 2
            });

            // 1. Cargar valor inicial desde la DB
            if (hiddenInput.value) {
                displayInput.value = formatter.format(hiddenInput.value);
            }

            // 2. Al escribir: Limpiar y guardar valor real
            displayInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^\d.]/g, '');

                // Evitar múltiples puntos decimales
                const parts = value.split('.');
                if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');

                hiddenInput.value = value;
            });

            // 3. Al entrar (Focus): Mostrar el número limpio para editar fácil
            displayInput.addEventListener('focus', function (e) {
                if (hiddenInput.value) {
                    e.target.value = hiddenInput.value;
                }
            });

            // 4. Al salir (Blur): Poner el formato bonito $ 0,000.00
            displayInput.addEventListener('blur', function (e) {
                const numericValue = parseFloat(hiddenInput.value);
                if (!isNaN(numericValue)) {
                    e.target.value = formatter.format(numericValue);
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const m2Masks = document.querySelectorAll('.m2-mask');

            // Formateador de números (Estilo mexicano: comas para miles)
            const m2Formatter = new Intl.NumberFormat('es-MX', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            m2Masks.forEach(displayInput => {
                // Obtenemos el ID del input oculto (está justo después en el HTML o por ID)
                const realInput = displayInput.nextElementSibling;

                // 1. CARGA INICIAL: Si ya hay datos en la DB, formatearlos
                if (realInput.value) {
                    displayInput.value = m2Formatter.format(realInput.value);
                }

                // 2. EVENTO INPUT: Mientras escriben, limpiamos y guardamos el valor real
                displayInput.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/[^\d.]/g, '');

                    // Evitar doble punto decimal
                    const parts = value.split('.');
                    if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');

                    realInput.value = value;
                });

                // 3. EVENTO FOCUS: Al hacer clic para editar, quitar comas para que no estorben
                displayInput.addEventListener('focus', function (e) {
                    if (realInput.value) {
                        e.target.value = realInput.value;
                    }
                });

                // 4. EVENTO BLUR: Al salir, volver a poner el formato legible
                displayInput.addEventListener('blur', function (e) {
                    const numericValue = parseFloat(realInput.value);
                    if (!isNaN(numericValue)) {
                        e.target.value = m2Formatter.format(numericValue);
                    }
                });
            });
        });
    </script>
@endpush