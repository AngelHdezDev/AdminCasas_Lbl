@extends('layouts.app')


@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --purple: #534AB7;
            --purple-light: #EEEDFE;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        .prop-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem;
        }

        .prop-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .prop-header-icon {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-md);
            background: var(--purple-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .prop-header-icon i {
            font-size: 18px;
            color: var(--purple);
        }

        .prop-header h1 {
            font-size: 22px;
            font-weight: 500;
            margin: 0;
        }

        .prop-header p {
            font-size: 14px;
            color: #6b7280;
            margin: 2px 0 0;
        }

        .prop-card {
            background: #fff;
            border: 0.5px solid rgba(0, 0, 0, .1);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .prop-card:last-child {
            margin-bottom: 0;
        }

        .prop-section-title {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .07em;
            padding-bottom: .75rem;
            margin-bottom: 1rem;
            border-bottom: 0.5px solid rgba(0, 0, 0, .08);
        }

        .field-group {
            margin-bottom: .75rem;
        }

        .field-group:last-child {
            margin-bottom: 0;
        }

        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .field-label .req {
            color: #b85a30;
        }

        .field-input {
            width: 100%;
            font-size: 14px;
            background: #f9fafb;
            border: 0.5px solid rgba(0, 0, 0, .12);
            border-radius: var(--radius-md);
            padding: 8px 11px;
            outline: none;
            transition: border-color .15s, background .15s;
            font-family: inherit;
            color: inherit;
            appearance: none;
        }

        .field-input:focus {
            border-color: var(--purple);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(83, 74, 183, .08);
        }

        .field-input[readonly] {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: default;
        }

        textarea.field-input {
            resize: vertical;
            min-height: 90px;
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #9ca3af;
            pointer-events: none;
        }

        .select-wrapper .field-input {
            padding-right: 32px;
        }

        .row-grid {
            display: grid;
            gap: .75rem;
        }

        .cols-2 {
            grid-template-columns: 1fr 1fr;
        }

        .cols-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        /* Layout principal: dos secciones */
        .layout-top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .layout-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {

            .layout-top,
            .layout-bottom {
                grid-template-columns: 1fr;
            }
        }

        /* Mapa */
        .map-search-wrapper {
            position: relative;
            margin-bottom: .75rem;
        }

        .map-search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
            pointer-events: none;
            z-index: 1;
        }

        #address-input {
            padding-left: 34px;
        }

        #results-list {
            position: absolute;
            width: 100%;
            z-index: 1000;
            display: none;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
        }

        #results-list .list-group-item {
            border: none;
            border-bottom: 0.5px solid rgba(0, 0, 0, .07);
            font-size: 14px;
            padding: 10px 14px;
            cursor: pointer;
            transition: background .1s;
        }

        #results-list .list-group-item:last-child {
            border-bottom: none;
        }

        #results-list .list-group-item:hover {
            background: #f5f3ff;
            color: var(--purple);
        }

        #map {
            height: 280px;
            width: 100%;
            border-radius: var(--radius-md);
            border: 0.5px solid rgba(0, 0, 0, .1);
        }

        /* Campos autorrellenos — indicador visual sutil */
        .autofill-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 500;
            color: var(--purple);
            background: var(--purple-light);
            border-radius: 4px;
            padding: 1px 6px;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* Toggles */
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 0;
            border-bottom: 0.5px solid rgba(0, 0, 0, .07);
        }

        .toggle-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .toggle-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-icon {
            width: 30px;
            height: 30px;
            border-radius: var(--radius-md);
            background: #f3f4f6;
            border: 0.5px solid rgba(0, 0, 0, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toggle-icon i {
            font-size: 13px;
            color: #6b7280;
        }

        .toggle-label {
            font-size: 14px;
            font-weight: 500;
        }

        .toggle-desc {
            font-size: 12px;
            color: #9ca3af;
        }

        .form-switch-custom .form-check-input {
            width: 38px;
            height: 22px;
            cursor: pointer;
        }

        .form-switch-custom .form-check-input:checked {
            background-color: var(--purple);
            border-color: var(--purple);
        }

        /* Footer */
        .prop-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 0.5px solid rgba(0, 0, 0, .1);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-note {
            font-size: 13px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-note i {
            color: #3B6D11;
        }

        .footer-note .req-star {
            color: #b85a30;
            font-weight: 700;
            margin: 0 2px;
        }

        .footer-actions {
            display: flex;
            gap: .75rem;
        }

        .btn-cancel {
            font-family: inherit;
            font-size: 14px;
            background: none;
            border: 0.5px solid rgba(0, 0, 0, .15);
            border-radius: var(--radius-md);
            padding: 9px 18px;
            cursor: pointer;
            color: #6b7280;
            transition: background .15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-cancel:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-submit {
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            background: var(--purple);
            border: none;
            border-radius: var(--radius-md);
            padding: 9px 20px;
            cursor: pointer;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity .15s;
        }

        .btn-submit:hover {
            opacity: .88;
        }

        /* Estilos para el Grid de Amenidades */
        .amenities-grid-form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            margin-top: 5px;
        }

        .amenity-checkbox {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f9fafb;
            border: 0.5px solid rgba(0, 0, 0, .1);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .amenity-checkbox:hover {
            background: #f3f4f6;
        }

        .amenity-checkbox i {
            font-size: 16px;
            color: #6b7280;
            transition: color 0.2s;
        }

        .amenity-checkbox span {
            font-size: 13px;
            font-weight: 500;
            color: #4b5563;
        }

        /* Escondemos el checkbox real pero mantenemos la funcionalidad */
        .amenity-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Estado cuando está seleccionado */
        .amenity-checkbox:has(input:checked) {
            background: var(--purple-light);
            border-color: var(--purple);
        }

        .amenity-checkbox:has(input:checked) i {
            color: var(--purple);
        }

        .amenity-checkbox:has(input:checked) span {
            color: var(--purple);
        }
    </style>
@endpush

@section('content')
    <div class="prop-page">

        <div class="prop-header">
            <div class="prop-header-icon">
                <i class="bi bi-house-heart-fill"></i>
            </div>
            <div>
                <h1>Nueva Propiedad</h1>
                <p>Completa los datos para registrar en el inventario</p>
            </div>
        </div>

        <form action="{{ route('propiedades.store') }}" method="POST" id="formPropiedad">
            @csrf

            {{-- BLOQUE SUPERIOR: Mapa + Campos de ubicación lado a lado --}}
            <div class="layout-top">

                {{-- Izquierda: título, tipo y mapa --}}
                <div>
                    <div class="prop-card">
                        <div class="prop-section-title">Publicación</div>
                        <div class="field-group">
                            <label class="field-label">Título <span class="req">*</span></label>
                            <input type="text" class="field-input" name="title" id="title"
                                placeholder="Ej: Casa moderna con alberca en Zapopan" required>
                        </div>
                        <div class="field-group" style="margin-bottom:0">
                            <label class="field-label">Tipo de propiedad <span class="req">*</span></label>
                            <div class="select-wrapper">
                                <select class="field-input" name="type" id="type" required>
                                    <option value="">Seleccionar</option>
                                    <option value="house">Casa</option>
                                    <option value="apartment">Departamento</option>
                                    <option value="land">Terreno</option>
                                    <option value="commercial">Local Comercial</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="prop-card">
                        <div class="prop-section-title">Ubicación en mapa</div>
                        <div class="map-search-wrapper">
                            <i class="bi bi-search map-search-icon"></i>
                            <input type="text" id="address-input" class="field-input"
                                placeholder="Busca una calle o colonia..." autocomplete="off">
                            <div id="results-list" class="list-group"></div>
                        </div>
                        <div id="map"></div>
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                        <p style="font-size:12px;color:#9ca3af;margin-top:8px;margin-bottom:0">
                            <i class="bi bi-info-circle me-1"></i>
                            También puedes hacer clic en el mapa o arrastrar el marcador.
                        </p>
                    </div>
                </div>

                {{-- Derecha: campos que se autorrellena desde el mapa --}}
                <div>
                    <div class="prop-card" style="height:100%;box-sizing:border-box;">
                        <div class="prop-section-title">
                            Datos de ubicación
                            <span class="autofill-badge"><i class="bi bi-magic"></i> se llena automáticamente</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Código postal <span class="req">*</span></label>
                            <input type="text" class="field-input" name="cp" id="cp" placeholder="Se detecta del mapa"
                                maxlength="5" required>
                        </div>

                        <div class="row-grid cols-2">
                            <div class="field-group">
                                <label class="field-label">Estado <span class="req">*</span></label>
                                <input type="text" class="field-input" name="state" id="state" placeholder="—" readonly
                                    required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Ciudad / municipio <span class="req">*</span></label>
                                <input type="text" class="field-input" name="city" id="city" placeholder="—" readonly
                                    required>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Colonia / zona <span class="req">*</span></label>
                            <input type="text" class="field-input" name="neighborhood" id="neighborhood"
                                placeholder="Se detecta del mapa" required>
                        </div>

                        <div class="field-group" style="margin-bottom:0">
                            <label class="field-label">Dirección (calle y número) <span class="req">*</span></label>
                            <input type="text" class="field-input" name="address" id="address"
                                placeholder="Se detecta del mapa" required>
                        </div>
                    </div>
                </div>

            </div>

            {{-- BLOQUE INFERIOR: resto de secciones --}}
            <div class="layout-bottom">

                {{-- Izquierda: dimensiones --}}
                <div>
                    <div class="prop-card">
                        <div class="prop-section-title">Dimensiones y distribución</div>
                        <div class="row-grid cols-2">
                            <div class="field-group">
                                <label class="field-label">Terreno (m²) <span class="req">*</span></label>
                                <input type="number" class="field-input" name="m2_land" id="m2_land" placeholder="0"
                                    required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Construcción (m²) <span class="req">*</span></label>
                                <input type="number" class="field-input" name="m2_construction" id="m2_construction"
                                    placeholder="0" required>
                            </div>
                        </div>
                        <div class="row-grid cols-3">
                            <div class="field-group">
                                <label class="field-label">Habitaciones</label>
                                <input type="number" class="field-input" name="bedrooms" id="bedrooms" min="0" value="0">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Baños</label>
                                <input type="number" class="field-input" name="bathrooms" id="bathrooms" min="0" value="0">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Cochera</label>
                                <input type="number" class="field-input" name="parking_spots" id="parking_spots" min="0"
                                    value="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Derecha: comercialización, asignación, descripción --}}
                <div>
                    <div class="prop-card">
                        <div class="prop-section-title">Comercialización</div>
                        <div class="row-grid cols-2">
                            <div class="field-group">
                                <label class="field-label">Tipo de contrato <span class="req">*</span></label>
                                <div class="select-wrapper">
                                    <select class="field-input" name="contract_type" id="contract_type" required>
                                        <option value="sale">Venta</option>
                                        <option value="rent">Renta</option>
                                        <option value="consignment">Consignación</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Precio <span class="req">*</span></label>
                                <input type="number" class="field-input" name="price" id="price" placeholder="0.00" min="0"
                                    step="0.01" required>
                            </div>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="toggle-icon"><i class="bi bi-star-fill"></i></div>
                                <div>
                                    <div class="toggle-label">Propiedad destacada</div>
                                    <div class="toggle-desc">Aparecerá en los primeros resultados</div>
                                </div>
                            </div>
                            <div class="form-switch-custom form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                    value="1">
                            </div>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="toggle-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <div class="toggle-label">Dirección pública</div>
                                    <div class="toggle-desc">Mostrar calle y número en la web</div>
                                </div>
                            </div>
                            <div class="form-switch-custom form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="show_public_address" id="show_address"
                                    value="1" checked>
                            </div>
                        </div>
                    </div>

                    <div class="prop-card">
                        <div class="prop-section-title">Asignación</div>
                        <div class="row-grid cols-2">
                            <div class="field-group">
                                <label class="field-label">Vendedor</label>
                                <div class="select-wrapper">
                                    <select class="field-input" name="seller_id" id="seller_id">
                                        <option value="">Sin asignar</option>
                                        @foreach($vendedores as $vendedor)
                                            <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Cliente</label>
                                <div class="select-wrapper">
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

                    <div class="prop-card">
                        <div class="prop-section-title">Amenidades Disponibles</div>
                        <div class="amenities-grid-form">
                            @foreach($amenities as $amenity)
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}">
                                    @if(str_starts_with($amenity->icon, 'bi'))
                                        <i class="{{ $amenity->icon }}"></i>
                                    @else
                                        <span class="emoji-icon">{{ $amenity->icon }}</span>
                                    @endif
                                    <span>{{ $amenity->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p style="font-size:12px;color:#9ca3af;margin-top:12px;margin-bottom:0">
                            <i class="bi bi-info-circle me-1"></i>
                            Selecciona todas las características que incluye la propiedad.
                        </p>
                    </div>

                    <div class="prop-card">
                        <div class="prop-section-title">Detalles adicionales</div>
                        <div class="field-group" style="margin-bottom:0">
                            <label class="field-label">
                                Descripción
                                <span style="color:#9ca3af;font-weight:400">(opcional)</span>
                            </label>
                            <textarea class="field-input" name="description" id="description" rows="4"
                                placeholder="Menciona amenidades, acabados, cercanía a puntos de interés..."></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="prop-footer">
                <span class="footer-note">
                    <i class="bi bi-shield-check"></i>
                    Los campos con <span class="req-star">*</span> son requeridos
                </span>
                <div class="footer-actions">
                    <a href="{{ route('propiedades.index') }}" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-submit" id="btnSubmit">
                        <i class="bi bi-plus-lg"></i>
                        Registrar propiedad
                    </button>
                </div>
            </div>

        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const initialLat = 20.6596, initialLng = -103.3496;

                const map = L.map('map').setView([initialLat, initialLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

                // Referencias DOM
                const addressInput = document.getElementById('address-input');
                const resultsList = document.getElementById('results-list');
                const latInput = document.getElementById('lat');
                const lngInput = document.getElementById('lng');
                const stateInput = document.getElementById('state');
                const cityInput = document.getElementById('city');
                const cpInput = document.getElementById('cp');
                const neighborhoodEl = document.getElementById('neighborhood');
                const addressEl = document.getElementById('address');

                // Rellena el formulario con datos de Nominatim
                function fillForm(data) {

                    if (!data) return;

                    latInput.value = data.lat;
                    lngInput.value = data.lon;

                    if (data.display_name) {
                        addressInput.value = data.display_name;
                    }

                    if (data.address) {
                        const a = data.address;

                        stateInput.value = a.state || '';
                        cityInput.value = a.city || a.village || a.municipality || a.county || '';
                        cpInput.value = a.postcode || '';

                        // Dirección: calle + número
                        const street = [a.road || a.pedestrian || '', a.house_number || ''].filter(Boolean).join(' ');
                        if (street) addressEl.value = street;

                        // Colonia: Extraer el nombre de la zona
                        const colonia = a.suburb || a.neighbourhood || a.quarter || a.city_district || a.town || a.amenity || '';
                        populateNeighborhood(colonia);
                    }
                }

                // AHORA FUNCIONA COMO INPUT
                function populateNeighborhood(colonia) {
                    if (neighborhoodEl) {
                        // Simplemente asignamos el valor al input
                        neighborhoodEl.value = colonia;
                    }
                }

                // Reverse geocoding al mover marcador o hacer clic en mapa
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

                // Autocomplete de dirección
                let timer;
                addressInput.addEventListener('input', function () {
                    clearTimeout(timer);
                    const query = this.value.trim();
                    if (query.length < 3) { resultsList.style.display = 'none'; return; }

                    timer = setTimeout(() => {
                        fetch(`{{ route('propiedades.autocomplete') }}?q=${encodeURIComponent(query)}`)
                            .then(r => r.json())
                            .then(data => {
                                resultsList.innerHTML = '';
                                if (data && data.length > 0) {
                                    resultsList.style.display = 'block';
                                    data.forEach(item => {
                                        const btn = document.createElement('button');
                                        btn.type = 'button';
                                        btn.className = 'list-group-item list-group-item-action text-start';
                                        btn.innerHTML = `<i class="bi bi-geo-alt-fill me-2" style="color:var(--purple);font-size:12px"></i>${item.display_name}`;

                                        btn.onclick = () => {
                                            const lat = item.lat;
                                            const lon = item.lon;

                                            map.setView([lat, lon], 17);
                                            marker.setLatLng([lat, lon]);

                                            // Pedimos el reverseGeocode para obtener el JSON completo rico en detalles
                                            reverseGeocode(lat, lon);

                                            resultsList.style.display = 'none';
                                        };
                                        resultsList.appendChild(btn);
                                    });
                                } else {
                                    resultsList.style.display = 'none';
                                }
                            })
                            .catch(err => console.error('Autocomplete error:', err));
                    }, 400);
                });

                document.addEventListener('click', e => {
                    if (!addressInput.contains(e.target) && !resultsList.contains(e.target)) {
                        resultsList.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush

@endsection