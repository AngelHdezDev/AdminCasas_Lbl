@extends('layouts.app')


@section('title', 'Propiedades')
<title>Nueva Propiedad | CTP Realty</title>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/autos.css') }}">
    <style>
        :root {
            --bg: #f9fafb;
            --bg-card: #ffffff;
            --border: rgba(0, 0, 0, 0.08);
            --border-light: rgba(0, 0, 0, 0.06);
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --accent-light: #EEEDFE;
            --accent-hover: #4539a0;
            --success: #059669;
            --success-light: #d1fae5;
            --danger: #dc2626;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.08);
            --radius-sm: 8px;
            --radius-md: 10px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text-primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Page Header ── */
        .page-header {
            max-width: 1440px;
            margin: 0 auto;
            padding: 40px 32px 32px;
        }

        .page-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
        }

        .page-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 15px;
            color: var(--text-secondary);
            margin-top: 6px;
        }

        /* ── Main Content ── */
        .main-content {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 32px 60px;
        }

        /* ── Card Component ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            margin-bottom: 20px;
        }

        .card:last-child {
            margin-bottom: 0;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-light);
        }

        .card-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .card-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 500;
            color: var(--accent);
            background: var(--accent-light);
            padding: 4px 10px;
            border-radius: 20px;
        }

        .card-badge i {
            font-size: 10px;
        }

        /* ── Form Fields ── */
        .field {
            margin-bottom: 20px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .field-label .required {
            color: var(--danger);
            margin-left: 2px;
        }

        .field-input {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-primary);
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            outline: none;
            transition: all 0.2s;
        }

        .field-input:focus {
            border-color: var(--accent);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.08);
        }

        .field-input::placeholder {
            color: var(--text-muted);
        }

        textarea.field-input {
            resize: vertical;
            min-height: 100px;
        }

        select.field-input {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        .field-input[readonly] {
            background: #f3f4f6;
            color: var(--text-muted);
            cursor: default;
        }

        /* ── Grid Layouts ── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .grid-sidebar {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        /* ── Map ── */
        .map-search {
            position: relative;
            margin-bottom: 12px;
        }

        .map-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            z-index: 10;
        }

        .map-search .field-input {
            padding-left: 38px;
        }

        #map {
            height: 300px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            z-index: 1;
        }

        .map-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Autocomplete results */
        .autocomplete-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            max-height: 240px;
            overflow-y: auto;
            display: none;
        }

        .autocomplete-results.active {
            display: block;
        }

        .autocomplete-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
            border-bottom: 1px solid var(--border-light);
            text-align: left;
            width: 100%;
            background: none;
            border: none;
            border-bottom: 1px solid var(--border-light);
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover {
            background: var(--accent-light);
            color: var(--accent);
        }

        .autocomplete-item i {
            font-size: 12px;
            color: var(--accent);
            flex-shrink: 0;
        }

        /* ── Toggle Switches ── */
        .toggle-list {
            display: flex;
            flex-direction: column;
        }

        .toggle-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid var(--border-light);
        }

        .toggle-item:last-child {
            border-bottom: none;
        }

        .toggle-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toggle-icon {
            width: 36px;
            height: 36px;
            background: #f3f4f6;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toggle-icon i {
            font-size: 15px;
            color: var(--text-secondary);
        }

        .toggle-title {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .toggle-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #e5e7eb;
            border-radius: 24px;
            transition: 0.3s;
        }

        .slider::before {
            content: '';
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .switch input:checked+.slider {
            background: var(--accent);
        }

        .switch input:checked+.slider::before {
            transform: translateX(20px);
        }

        /* ── Amenities Grid ── */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 10px;
        }

        .amenity-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }

        .amenity-item:hover {
            background: #f3f4f6;
        }

        .amenity-item input {
            position: absolute;
            opacity: 0;
        }

        .amenity-item i {
            font-size: 16px;
            color: var(--text-secondary);
        }

        .amenity-item span {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .amenity-item:has(input:checked) {
            background: var(--accent-light);
            border-color: var(--accent);
        }

        .amenity-item:has(input:checked) i,
        .amenity-item:has(input:checked) span {
            color: var(--accent);
        }

        .amenities-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Form Footer ── */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-note {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-note i {
            color: var(--success);
        }

        .footer-note .req-dot {
            color: var(--danger);
            font-weight: 700;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #f3f4f6;
            color: var(--text-primary);
        }

        .btn-primary {
            background: #1a1d20;
            color: var(--bg-card);
        }

        .btn-primary:hover {
            background: #000;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .grid-sidebar {
                grid-template-columns: 1fr;
            }


        }

        @media (max-width: 768px) {


            .page-header,
            .main-content {
                padding-left: 20px;
                padding-right: 20px;
            }

            .page-title {
                font-size: 28px;
            }

            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 20px;
            }

            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
                justify-content: center;
            }

            .form-footer {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 24px;
            }

            .amenities-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <!DOCTYPE html>
    <html lang="es">

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

    </html>
@endsection