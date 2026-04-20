@extends('layouts.app')


@section('title', 'Propiedades')
<title>Nueva Propiedad | CTP Realty</title>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/createPropiedad.css') }}">
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
                            <div class="card-header">
                                <span class="card-title">Publicación</span>
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
                            <div class="card-header">
                                <span class="card-title">Ubicación en mapa</span>
                            </div>
                            <div class="map-search">
                                <i class="bi bi-search"></i>
                                <input type="text" id="address-input" class="field-input"
                                    placeholder="Busca una calle o colonia..." autocomplete="off">
                                <div id="results-list" class="autocomplete-results"></div>
                            </div>
                            <div id="map"></div>
                            <p class="map-hint">
                                <i class="bi bi-info-circle"></i>
                                También puedes hacer clic en el mapa o arrastrar el marcador.
                            </p>
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="lng">
                        </div>

                        <!-- Dimensiones -->
                        <div class="card">
                            <div class="card-header">
                                <span class="card-title">Dimensiones y distribución</span>
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
                            <div class="card-header">
                                <span class="card-title">Amenidades disponibles</span>
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
                            <div class="card-header">
                                <span class="card-title">Detalles adicionales</span>
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
                            <div class="card-header">
                                <span class="card-title">Datos de ubicación</span>
                                <span class="card-badge">
                                    <i class="bi bi-magic"></i> Auto
                                </span>
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
                                        Estado <span class="required">*</span>
                                    </label>
                                    <input type="text" class="field-input" name="state" id="state" placeholder="—" readonly
                                        required>
                                </div>
                                <div class="field">
                                    <label class="field-label">
                                        Ciudad <span class="required">*</span>
                                    </label>
                                    <input type="text" class="field-input" name="city" id="city" placeholder="—" readonly
                                        required>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">
                                    Colonia <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="neighborhood" id="neighborhood"
                                    placeholder="Se detecta del mapa" required>
                            </div>
                            <div class="field" style="margin-bottom:0">
                                <label class="field-label">
                                    Dirección <span class="required">*</span>
                                </label>
                                <input type="text" class="field-input" name="address" id="address"
                                    placeholder="Se detecta del mapa" required>
                            </div>
                        </div>

                        <!-- Comercialización -->
                        <div class="card">
                            <div class="card-header">
                                <span class="card-title">Comercialización</span>
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
                                    <input type="number" class="field-input" name="price" id="price" placeholder="0.00"
                                        min="0" step="0.01" required>
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
                            <div class="card-header">
                                <span class="card-title">Asignación</span>
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

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const initialLat = 20.6596, initialLng = -103.3496;

                const map = L.map('map').setView([initialLat, initialLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

                const addressInput = document.getElementById('address-input');
                const resultsList = document.getElementById('results-list');
                const latInput = document.getElementById('lat');
                const lngInput = document.getElementById('lng');
                const stateInput = document.getElementById('state');
                const cityInput = document.getElementById('city');
                const cpInput = document.getElementById('cp');
                const neighborhoodEl = document.getElementById('neighborhood');
                const addressEl = document.getElementById('address');

                function fillForm(data) {
                    if (!data) return;
                    latInput.value = data.lat;
                    lngInput.value = data.lon;
                    if (data.display_name) addressInput.value = data.display_name;
                    if (data.address) {
                        const a = data.address;
                        stateInput.value = a.state || '';
                        cityInput.value = a.city || a.village || a.municipality || a.county || '';
                        cpInput.value = a.postcode || '';
                        const street = [a.road || a.pedestrian || '', a.house_number || ''].filter(Boolean).join(' ');
                        if (street) addressEl.value = street;
                        const colonia = a.suburb || a.neighbourhood || a.quarter || a.city_district || a.town || '';
                        if (neighborhoodEl) neighborhoodEl.value = colonia;
                    }
                }

                function reverseGeocode(lat, lng) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
                        .then(r => r.json())
                        .then(data => fillForm(data))
                        .catch(err => console.error('Reverse geocoding error:', err));
                }

                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    reverseGeocode(pos.lat, pos.lng);
                });

                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    reverseGeocode(e.latlng.lat, e.latlng.lng);
                });

                let timer;
                addressInput.addEventListener('input', function () {
                    clearTimeout(timer);
                    const query = this.value.trim();
                    if (query.length < 3) { resultsList.classList.remove('active'); return; }

                    timer = setTimeout(() => {
                        fetch(`{{ route('propiedades.autocomplete') }}?q=${encodeURIComponent(query)}`)
                            .then(r => r.json())
                            .then(data => {
                                resultsList.innerHTML = '';
                                if (data && data.length > 0) {
                                    resultsList.classList.add('active');
                                    data.forEach(item => {
                                        const btn = document.createElement('button');
                                        btn.type = 'button';
                                        btn.className = 'autocomplete-item';
                                        btn.innerHTML = `<i class="bi bi-geo-alt-fill"></i>${item.display_name}`;
                                        btn.onclick = () => {
                                            const lat = item.lat, lon = item.lon;
                                            map.setView([lat, lon], 17);
                                            marker.setLatLng([lat, lon]);
                                            reverseGeocode(lat, lon);
                                            resultsList.classList.remove('active');
                                        };
                                        resultsList.appendChild(btn);
                                    });
                                } else {
                                    resultsList.classList.remove('active');
                                }
                            })
                            .catch(err => console.error('Autocomplete error:', err));
                    }, 270);
                });

                document.addEventListener('click', e => {
                    if (!addressInput.contains(e.target) && !resultsList.contains(e.target)) {
                        resultsList.classList.remove('active');
                    }
                });
            });
        </script>

    </body>
@endsection