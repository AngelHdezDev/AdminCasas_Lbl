@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/createPropiedad.css') }}">
    <style>
        /* Aseguramos que el contenedor de resultados de Google no choque con tu estilo */
        .pac-container {
            z-index: 10000 !important;
        }

        /* ============================================================
                           CTP Realty — Admin: Crear / Editar Propiedad (createPropiedad.css)
                           Paleta: Azul marino #0F2167, Dorado #F5C518
                           Fuentes: Montserrat + DM Sans
                           ============================================================ */

        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

        /* ── Variables ── */
        :root {
            --navy: #0F2167;
            --navy-dark: #091547;
            --navy-mid: #162880;
            --navy-soft: #1e3494;
            --gold: #F5C518;
            --gold-dark: #d9ab0e;
            --gold-light: #fde97a;
            --white: #FFFFFF;
            --off-white: #F7F8FC;
            --border: #E4E8F4;
            --text-primary: #0F2167;
            --text-secondary: #5A6A9A;
            --text-muted: #8896C0;

            --green: #16a34a;
            --red: #dc2626;

            --radius-sm: 8px;
            --radius: 14px;
            --radius-lg: 20px;
            --shadow-sm: 0 2px 8px rgba(15, 33, 103, 0.07);
            --shadow: 0 4px 20px rgba(15, 33, 103, 0.10);

            --font-display: 'Montserrat', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background: var(--off-white);
            color: var(--text-primary);
        }

        /* ────────────────────────────────────────
                           MAIN CONTENT WRAPPER
                        ──────────────────────────────────────── */
        .main-content {
            padding: 1.75rem 1.5rem 6rem;
            /* bottom padding para el form-footer fijo */
            max-width: 1300px;
            margin: 0 auto;
        }

        /* ────────────────────────────────────────
                           GRID SIDEBAR LAYOUT
                        ──────────────────────────────────────── */
        .grid-sidebar {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 1.25rem;
            align-items: start;
        }

        .left-column,
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        /* ────────────────────────────────────────
                           CARDS
                        ──────────────────────────────────────── */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 1.4rem;
        }

        /* Card section header */
        .card .mb-4 {
            margin-bottom: 1.1rem !important;
            border-left: 3px solid var(--gold) !important;
            padding-left: 0.75rem !important;
            padding-top: 0.2rem !important;
            padding-bottom: 0.2rem !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card h4 {
            font-family: var(--font-display) !important;
            font-size: 0.68rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em !important;
            text-transform: uppercase !important;
            color: var(--navy) !important;
        }

        /* Badge "Auto" junto al título */
        .card-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.65rem;
            font-weight: 600;
            font-family: var(--font-display);
            padding: 0.18rem 0.6rem;
            border-radius: 100px;
            background: rgba(245, 197, 24, 0.15);
            color: var(--gold-dark);
        }

        /* ────────────────────────────────────────
                           FORM FIELDS
                        ──────────────────────────────────────── */
        .field {
            margin-bottom: 1rem;
        }

        .field-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 0.4rem;
        }

        .required {
            color: var(--red);
        }

        .field-input {
            width: 100%;
            height: 40px;
            padding: 0 0.9rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.85rem;
            color: var(--text-primary);
            background: var(--off-white);
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            appearance: none;
        }

        textarea.field-input {
            height: auto;
            padding: 0.75rem 0.9rem;
            resize: vertical;
            line-height: 1.6;
        }

        .field-input::placeholder {
            color: var(--text-muted);
        }

        .field-input:focus {
            border-color: var(--navy-soft);
            box-shadow: 0 0 0 3px rgba(30, 52, 148, 0.1);
            background: var(--white);
        }

        .field-input[readonly] {
            background: #eef1fa;
            color: var(--text-secondary);
            cursor: default;
        }

        /* Select arrow */
        select.field-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%235A6A9A' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            padding-right: 2.2rem;
            cursor: pointer;
        }

        /* ────────────────────────────────────────
                           GRIDS DENTRO DEL FORM
                        ──────────────────────────────────────── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.85rem;
        }

        /* ────────────────────────────────────────
                           MAP SEARCH
                        ──────────────────────────────────────── */
        .map-search {
            position: relative;
            margin-bottom: 0.75rem;
        }

        .map-search i {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .map-search .field-input {
            padding-left: 2.4rem;
            border-radius: 100px;
        }

        .map-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.6rem;
        }

        /* Map container */
        #map {
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            overflow: hidden;
        }

        /* pac-container (autocomplete dropdown de Google) */
        .pac-container {
            font-family: var(--font-body) !important;
            border-radius: var(--radius-sm) !important;
            border: 1.5px solid var(--border) !important;
            box-shadow: var(--shadow) !important;
            z-index: 10000 !important;
        }

        /* ────────────────────────────────────────
                           AMENIDADES
                        ──────────────────────────────────────── */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.75rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--off-white);
            cursor: pointer;
            font-size: 0.78rem;
            color: var(--text-primary);
            font-weight: 500;
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
            user-select: none;
        }

        .amenity-item:hover {
            border-color: var(--navy-soft);
            background: #eef1fa;
        }

        .amenity-item input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
            flex-shrink: 0;
            margin: 0;
        }

        .amenity-item:has(input:checked) {
            border-color: var(--navy);
            background: rgba(15, 33, 103, 0.06);
            box-shadow: 0 0 0 2px rgba(15, 33, 103, 0.12);
        }

        .emoji-icon {
            font-size: 1rem;
            line-height: 1;
        }

        .amenities-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.25rem;
        }

        /* ────────────────────────────────────────
                           TOGGLE SWITCHES (Destacada / Dir. pública)
                        ──────────────────────────────────────── */
        .toggle-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .toggle-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 0;
            border-bottom: 1px solid var(--off-white);
        }

        .toggle-item:last-child {
            border-bottom: none;
        }

        .toggle-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toggle-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-soft) 100%);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .toggle-title {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 0.1rem;
        }

        .toggle-desc {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* Switch UI */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }

        /* Ocultar el hidden input de Laravel que va antes del checkbox */
        .switch input[type="hidden"] {
            display: none;
        }

        .switch input[type="checkbox"] {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: var(--border);
            border-radius: 100px;
            transition: background 0.22s;
        }

        .slider::before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            left: 3px;
            top: 3px;
            background: var(--white);
            border-radius: 50%;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.22s;
        }

        .switch input[type="checkbox"]:checked+.slider {
            background: var(--navy);
        }

        .switch input[type="checkbox"]:checked+.slider::before {
            transform: translateX(20px);
            background: var(--gold);
        }

        /* ────────────────────────────────────────
                           BUTTONS
                        ──────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            height: 40px;
            padding: 0 1.25rem;
            border-radius: 100px;
            font-family: var(--font-display);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.18s, transform 0.18s, box-shadow 0.18s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--navy);
            color: var(--white);
            box-shadow: 0 4px 14px rgba(15, 33, 103, 0.25);
        }

        .btn-primary:hover {
            background: var(--navy-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(15, 33, 103, 0.32);
            color: var(--white);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--off-white);
            border-color: var(--navy-soft);
            color: var(--navy);
        }

        /* Grupo de botones */
        .btn-group {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        /* ────────────────────────────────────────
                           FORM FOOTER (fijo abajo)
                        ──────────────────────────────────────── */
        .form-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 200;
            background: var(--white);
            border-top: 1px solid var(--border);
            box-shadow: 0 -4px 20px rgba(15, 33, 103, 0.08);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .footer-note {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .footer-note i {
            color: var(--green);
        }

        .req-dot {
            color: var(--red);
            font-weight: 700;
        }

        /* ────────────────────────────────────────
                           INVALID FEEDBACK
                        ──────────────────────────────────────── */
        .invalid-feedback {
            font-size: 0.72rem;
            color: var(--red);
            margin-top: 0.3rem;
            display: block;
        }

        /* ────────────────────────────────────────
                           RESPONSIVE
                        ──────────────────────────────────────── */
        @media (max-width: 1024px) {
            .grid-sidebar {
                grid-template-columns: 1fr;
            }

            .right-column {
                order: -1;
            }

            /* sidebar arriba en móvil */
        }

        @media (max-width: 640px) {
            .main-content {
                padding: 1rem 1rem 5rem;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .grid-3 {
                grid-template-columns: 1fr 1fr;
            }

            .form-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .form-footer .btn-group {
                justify-content: flex-end;
            }

            .amenities-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }

        /* ────────────────────────────────────────
                           SAVED IMAGES GRID (edit form)
                        ──────────────────────────────────────── */

        /* Contador badge en el header de la card */
        .images-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.68rem;
            font-weight: 700;
            font-family: var(--font-display);
            padding: 0.2rem 0.65rem;
            border-radius: 100px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-soft) 100%);
            color: var(--gold);
            letter-spacing: 0.03em;
        }

        /* Grid de miniaturas: 3 columnas ajustables */
        .saved-images-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        /* Cada celda */
        .saved-image-item {
            border-radius: var(--radius-sm);
            overflow: hidden;
        }

        /* Contenedor relativo para la imagen y el overlay */
        .saved-image-wrap {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-soft) 100%);
            border: 1.5px solid var(--border);
            transition: border-color 0.18s;
        }

        .saved-image-wrap:hover {
            border-color: var(--red);
        }

        .saved-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        /* Oscurecer imagen al hover para que se vea el botón */
        .saved-image-wrap:hover img {
            transform: scale(1.05);
            filter: brightness(0.55);
        }

        /* Overlay con botón eliminar — visible al hover */
        .saved-image-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }

        .saved-image-wrap:hover .saved-image-overlay {
            opacity: 1;
            pointer-events: auto;
        }

        /* Botón eliminar central */
        .btn-delete-saved-img {
            width: 40px;
            height: 40px;
            background: rgba(220, 38, 38, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 0.95rem;
            cursor: pointer;
            backdrop-filter: blur(4px);
            transition: background 0.15s, transform 0.15s, border-color 0.15s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-delete-saved-img:hover {
            background: var(--red);
            transform: scale(1.12);
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* Badges portada / hero sobre la miniatura */
        .img-badge-portada,
        .img-badge-hero {
            position: absolute;
            font-size: 0.58rem;
            padding: 0.15rem 0.45rem;
            border-radius: 100px;
            font-family: var(--font-display);
            font-weight: 700;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 0.2rem;
            z-index: 5;
            pointer-events: none;
        }

        .img-badge-portada {
            top: 5px;
            left: 5px;
            background: rgba(245, 197, 24, 0.92);
            color: var(--navy-dark);
        }

        .img-badge-hero {
            bottom: 5px;
            left: 5px;
            background: rgba(15, 33, 103, 0.85);
            color: var(--white);
        }

        /* Empty state sin imágenes */
        .images-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 2.5rem 1rem;
            background: var(--off-white);
            border: 1.5px dashed var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 1rem;
        }

        .images-empty-state i {
            font-size: 2rem;
            opacity: 0.35;
        }

        .images-empty-state span {
            font-size: 0.78rem;
        }

        /* Botón administrar galería */
        .btn-manage-gallery {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            height: 40px;
            background: var(--navy);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font-display);
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.18s, transform 0.18s;
            box-shadow: 0 3px 10px rgba(15, 33, 103, 0.2);
            margin-top: 0.25rem;
        }

        .btn-manage-gallery:hover {
            background: var(--navy-dark);
            color: var(--gold);
            transform: translateY(-1px);
        }

        /* Responsive: 2 columnas en pantallas pequeñas */
        @media (max-width: 480px) {
            .saved-images-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

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

                        <!-- Imágenes Guardadas -->
                        <div class="card">
                            <div
                                class="mb-4 border-l-4 border-blue-500 pl-3 py-1 d-flex justify-content-between align-items-center">
                                <h4 class="text-sm font-bold text-gray-800 uppercase mb-0">Imágenes Guardadas</h4>
                                <span class="images-count-badge">
                                    <i class="bi bi-images"></i>
                                    {{ $property->images->count() }} fotos
                                </span>
                            </div>

                            @if($property->images && $property->images->count() > 0)
                                <div class="saved-images-grid" id="savedImagesGrid">
                                    @foreach($property->images as $image)
                                        <div class="saved-image-item" id="img-item-{{ $image->id }}">
                                            <div class="saved-image-wrap">
                                                <img src="{{ asset('storage/' . $image->path) }}"
                                                    alt="Imagen de {{ $property->title }}" loading="lazy">
                                                <div class="saved-image-overlay">
                                                    <button type="button" class="btn-delete-saved-img" data-id="{{ $image->id }}"
                                                        data-url="{{ route('propiedades.imagen.delete', $image->id) }}"
                                                        title="Eliminar imagen">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                                @if($image->is_main)
                                                    <span class="img-badge-portada"><i class="bi bi-star-fill"></i></span>
                                                @endif
                                                @if($image->is_hero)
                                                    <span class="img-badge-hero"><i class="bi bi-image-fill"></i></span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="images-empty-state">
                                    <i class="bi bi-images"></i>
                                    <span>Sin imágenes en la galería</span>
                                </div>
                            @endif

                            <a href="{{ route('galeria.index', ['propiedad_id' => $property->id]) }}"
                                class="btn-manage-gallery">
                                <i class="bi bi-arrow-up-right-square"></i>
                                Administrar Galería completa
                            </a>
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
                        <button type="submit" class="btn btn-primary" id="btnActualizarPropiedad">
                            <i class="bi bi-plus-lg"></i>
                            <span>Actualizar propiedad</span>
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
            const form = document.getElementById('formPropiedad');
            const submitButton = document.getElementById('btnActualizarPropiedad');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            if (!form || !submitButton) return;

            function clearAjaxErrors() {
                form.querySelectorAll('.is-invalid').forEach(field => field.classList.remove('is-invalid'));
                form.querySelectorAll('.ajax-invalid-feedback').forEach(feedback => feedback.remove());
            }

            function findField(name) {
                const displayFieldIds = {
                    price: 'price_display',
                    m2_land: 'm2_land_display',
                    m2_construction: 'm2_construction_display',
                };

                if (displayFieldIds[name]) {
                    return document.getElementById(displayFieldIds[name]);
                }

                return form.querySelector(`[name="${name}"]`) || form.querySelector(`[name="${name}[]"]`);
            }

            function showFieldErrors(errors) {
                let firstInvalidField = null;

                Object.entries(errors).forEach(([name, messages]) => {
                    const field = findField(name);
                    if (!field) return;

                    field.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback ajax-invalid-feedback';
                    feedback.style.display = 'block';
                    feedback.textContent = messages[0];

                    const container = field.closest('.field') || field.closest('.toggle-item') || field.parentElement;
                    container.appendChild(feedback);

                    if (!firstInvalidField) firstInvalidField = field;
                });

                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalidField.focus({ preventScroll: true });
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                clearAjaxErrors();

                const originalHtml = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="bi bi-arrow-repeat"></i><span>Guardando...</span>';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                })
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || !data.success) {
                            if (response.status === 422 && data.errors) {
                                showFieldErrors(data.errors);
                            }

                            throw new Error(data.message || 'Revisa los campos marcados e intenta de nuevo.');
                        }

                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Propiedad actualizada.',
                            showConfirmButton: false,
                            timer: 2200
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'No se pudo actualizar',
                            text: error.message,
                            icon: 'error',
                            confirmButtonColor: '#c0392b'
                        });
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalHtml;
                    });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-delete-saved-img').forEach(btn => {
                btn.addEventListener('click', function () {
                    const imageId = this.dataset.id;
                    const deleteUrl = this.dataset.url;

                    Swal.fire({
                        title: '¿Eliminar imagen?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#b71b1b',
                        cancelButtonColor: '#8896C0',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) {
                            fetch(deleteUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ _method: 'DELETE' })
                            }).then(res => {
                                if (res.ok) {
                                    // 1. Lanzamos el SweetAlert de éxito discretamente (timer de 1.5s para no estorbar)
                                    Swal.fire({
                                        title: '¡Eliminada!',
                                        text: 'La imagen ha sido borrada correctamente.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    // 2. Ejecutamos la animación de la tarjeta
                                    const item = document.getElementById(`img-item-${imageId}`);
                                    if (item) {
                                        item.style.transition = 'opacity 0.3s, transform 0.3s';
                                        item.style.opacity = '0';
                                        item.style.transform = 'scale(0.85)';
                                        setTimeout(() => item.remove(), 300);
                                    }

                                    // 3. Actualizar contador
                                    const grid = document.getElementById('savedImagesGrid');
                                    const remaining = grid ? grid.querySelectorAll('.saved-image-item').length - 1 : 0;
                                    const badge = document.querySelector('.images-count-badge');
                                    if (badge) badge.innerHTML = `<i class="bi bi-images"></i> ${remaining} fotos`;
                                } else {
                                    Swal.fire('Error', 'No se pudo eliminar la imagen desde el servidor.', 'error');
                                }
                            }).catch(err => {
                                console.error(err);
                                Swal.fire('Error', 'Ocurrió un problema de red.', 'error');
                            });
                        }
                    });
                });
            });
        });
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
