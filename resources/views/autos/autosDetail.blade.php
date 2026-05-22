@extends('layouts.app')

@section('title', 'Detalle de la Propiedad')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/detalle-vehiculo.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map-detail {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }

        .map-card {
            overflow: hidden;
        }

        /* ── CONTENEDOR DE LA IMAGEN GRANDE (REQUISITO PARA POSICIONAMIENTO ABSOLUTO) ── */
        .gallery-featured {
            position: relative;
        }

        /* ── PANEL DE ACCIONES FLOTANTE SOBRE LA IMAGEN PRINCIPAL ── */
        .featured-actions-bar {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            padding: 6px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-featured-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-featured-action:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            transform: translateY(-1px);
        }

        .btn-delete-thumbnail:hover {
            color: #dc3545;
            border-color: #f5c2c7;
            background-color: #f8d7da;
        }

        /* Ocultar los botones de las miniaturas para mantener limpio abajo */
        .thumbnail-actions {
            display: none !important;
        }

        /* ── BARRA DE BADGES FLOTANTE (ARRIBA A LA IZQUIERDA) ── */
        .featured-badges-bar {
            position: absolute;
            top: 15px;
            left: 15px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 10;
        }

        /* Ajustes de diseño para los badges flotantes grandes */
        .featured-badges-bar .badge-portada,
        .featured-badges-bar .badge-hero {
            position: static;
            /* Resetea el absoluto que tengan en las miniaturas */
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* ── FLECHAS DE NAVEGACIÓN FLOTANTES ── */
        .gallery-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            color: #212529;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 9;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .gallery-nav-btn:hover {
            background: rgba(255, 255, 255, 0.95);
            color: #0d6efd;
            transform: translateY(-50%) scale(1.05);
        }

        .nav-btn-left {
            left: 15px;
        }

        .nav-btn-right {
            right: 15px;
        }

        /* Ocultar las flechas si por alguna razón la propiedad tiene solo 1 imagen o ninguna */
        .gallery-thumbnails:not(:has(.thumbnail-item:nth-child(2)))~.gallery-nav-btn,
        #galleryFeatured:not(:has(+ .gallery-thumbnails)) .gallery-nav-btn {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="page-header-inner">
                <div>
                    <div class="breadcrumb-custom">
                        <a href="{{ route('propiedades.index') }}">
                            <i class="bi bi-arrow-left"></i>
                            Volver a propiedades
                        </a>
                    </div>
                    <h1 class="page-title">{{ $property->title }}</h1>
                    <p class="page-subtitle">
                        {{ ucfirst($property->type) }} · {{ $property->neighborhood }} ·
                        ${{ number_format($property->price, 2) }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('propiedades.edit', $property->id) }}" class="btn btn-outline-primary"
                        title="Editar Ubicación">
                        <i class="bi bi-geo-alt-fill"></i> Editar Propiedad
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="container-fluid px-4">
            <div class="row g-4">

                <div class="col-12 col-lg-8">
                    <div class="content-card">
                        <input type="hidden" name="latitude" id="lat" value="{{ old('latitude', $property->latitude) }}">
                        <input type="hidden" name="longitude" id="lng" value="{{ old('longitude', $property->longitude) }}">
                        <div class="card-body-custom p-0">
                            @if($property->images->count() > 0)
                                @php $firstImage = $property->images->first(); @endphp
                                <div class="gallery-main">

                                    <div class="gallery-featured" id="galleryFeatured">
                                        <img src="{{ asset('storage/' . $firstImage->path) }}" alt="{{ $property->title }}"
                                            id="featuredImage">

                                        <button type="button" class="gallery-nav-btn nav-btn-left" id="btn-gallery-prev"
                                            title="Imagen anterior">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <button type="button" class="gallery-nav-btn nav-btn-right" id="btn-gallery-next"
                                            title="Siguiente imagen">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>

                                        <div class="featured-badges-bar" id="featuredBadgesBar">
                                            <span class="badge-portada" id="featured-badge-portada"
                                                style="display: {{ $firstImage->is_main ? 'inline-flex' : 'none' }};">
                                                <i class="bi bi-star-fill"></i>
                                                <span>Portada</span>
                                            </span>
                                            <span class="badge-hero" id="featured-badge-hero"
                                                style="display: {{ $firstImage->is_hero ? 'inline-flex' : 'none' }};">
                                                <i class="bi bi-image-fill"></i>
                                                <span>Hero</span>
                                            </span>
                                        </div>

                                        <div class="featured-actions-bar">
                                            <form id="form-portada"
                                                action="{{ route('propiedades.imagen.portada', $firstImage->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-featured-action" id="btn-action-portada"
                                                    title="Marcar como portada"
                                                    style="display: {{ $firstImage->is_main ? 'none' : 'inline-flex' }};">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            </form>

                                            <form id="form-hero"
                                                action="{{ route('propiedades.imagen.hero', $firstImage->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-featured-action" id="btn-action-hero"
                                                    title="Marcar como Hero"
                                                    style="display: {{ $firstImage->is_hero ? 'none' : 'inline-flex' }};">
                                                    <i class="bi bi-image-fill"></i>
                                                </button>
                                            </form>

                                            <form id="form-delete"
                                                action="{{ route('propiedades.imagen.delete', $firstImage->id) }}" method="POST"
                                                class="d-inline delete-image-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-featured-action btn-delete-thumbnail"
                                                    title="Eliminar esta imagen">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            <button class="btn-featured-action" onclick="viewFullscreen()"
                                                title="Pantalla Completa">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="gallery-thumbnails">
                                        @foreach($property->images as $index => $image)
                                            <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                                data-imagen-id="{{ $image->id }}" data-is-main="{{ $image->is_main ? '1' : '0' }}"
                                                data-is-hero="{{ $image->is_hero ? '1' : '0' }}">

                                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagen {{ $index + 1 }}"
                                                    onclick="changeImageWithActions('{{ asset('storage/' . $image->path) }}', this.parentElement, '{{ $image->id }}')">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="gallery-empty-large"><i class="bi bi-image"></i>
                                    <p>Esta propiedad no tiene imágenes</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2 class="card-title-custom"><i class="bi bi-house-door"></i> Detalles de la Propiedad</h2>
                        </div>
                        <div class="card-body-custom">
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-geo-alt"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Ubicación</div>
                                        <div class="spec-value">{{ $property->city }}, {{ $property->state }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-map"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Colonia</div>
                                        <div class="spec-value">{{ $property->neighborhood }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-rulers"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Terreno</div>
                                        <div class="spec-value">{{ number_format($property->m2_land, 0, '.', ',') }} m²
                                        </div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-building"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Construcción</div>
                                        <div class="spec-value">{{ number_format($property->m2_construction, 0, '.', ',') }}
                                            m²</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-door-open"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Habitaciones</div>
                                        <div class="spec-value">{{ $property->bedrooms }}</div>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-droplet"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Baños</div>
                                        <div class="spec-value">{{ $property->bathrooms }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="map-card mt-4">
                                <div class="spec-label mb-2"><i class="bi bi-map"></i> Ubicación en el Mapa</div>
                                <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                            </div>

                            <div class="address-box mt-4 p-3 bg-light rounded">
                                <div class="spec-label mb-2"><i class="bi bi-geo"></i> Dirección Exacta</div>
                                @if($property->show_public_address)
                                    <div class="spec-value">{{ $property->address }}</div>
                                @else
                                    <div class="spec-value text-muted"><em><i class="bi bi-eye-slash"></i> La dirección exacta
                                            es privada</em></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($property->description)
                        <div class="content-card">
                            <div class="card-header-custom">
                                <h2 class="card-title-custom"><i class="bi bi-card-text"></i> Descripción</h2>
                            </div>
                            <div class="card-body-custom">
                                <p class="description-text">{{ $property->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-12 col-lg-4">
                    <div class="content-card price-card">
                        <div class="card-body-custom text-center">
                            <div class="price-label">
                                @if($property->contract_type == 'consignment')
                                    Propiedad en Consignación
                                @else
                                    Precio de {{ $property->contract_type == 'sale' ? 'Venta' : 'Renta' }}
                                @endif
                            </div>
                            <div class="price-value">${{ number_format($property->price, 2) }}</div>
                            @if($property->is_featured)
                                <div class="price-note"><i class="bi bi-star-fill text-warning"></i> Propiedad Destacada</div>
                            @endif
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2 class="card-title-custom"><i class="bi bi-info-square"></i> Información de Gestión</h2>
                        </div>
                        <div class="card-body-custom">
                            <div class="info-list">
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person-badge"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">Vendedor Asignado</div>
                                        <div class="info-value">{{ $property->seller->name ?? 'Sin asignar' }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person-check"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">Propietario (Cliente)</div>
                                        <div class="info-value">{{ $property->client->name ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-calendar-plus"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">Registro</div>
                                        <div class="info-value">{{ $property->created_at->format('d/m/Y') }}</div>
                                        <div class="info-sub">{{ $property->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="content-card mt-4">
                            <div class="card-header-custom">
                                <h2 class="card-title-custom"><i class="bi bi-stars"></i> Amenidades y Servicios</h2>
                            </div>
                            <div class="card-body-custom">
                                <div class="amenities-display-grid">
                                    @forelse($property->amenities as $amenity)
                                        <div class="amenity-display-item">
                                            <div class="amenity-display-icon">
                                                @if(str_contains($amenity->icon, 'bi-'))
                                                    <i class="{{ $amenity->icon }}"></i>
                                                @else
                                                    <span class="emoji-font">{{ $amenity->icon }}</span>
                                                @endif
                                            </div>
                                            <span class="amenity-display-name">{{ $amenity->name }}</span>
                                        </div>
                                    @empty
                                        <div class="text-muted small">No hay amenidades registradas para esta propiedad.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="btn-group">
                        <a href="{{ route('galeria.index') }}" class="btn btn-primary">Ir a galería</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/detalle-vehiculo.js') }}"></script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: '¡Hecho!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#c0392b'
                });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Hubo un problema',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonColor: '#c0392b'
                });
            });
        </script>
    @endif
@endsection

@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnPrev = document.getElementById('btn-gallery-prev');
            const btnNext = document.getElementById('btn-gallery-next');

            if (btnPrev && btnNext) {
                // Función genérica para mover la imagen
                function navigateGallery(direction) {
                    const thumbnails = Array.from(document.querySelectorAll('.thumbnail-item'));
                    const activeThumbnail = document.querySelector('.thumbnail-item.active');
                    if (!activeThumbnail || thumbnails.length <= 1) return;

                    let currentIndex = thumbnails.indexOf(activeThumbnail);

                    if (direction === 'next') {
                        // Si es la última, vuelve a la primera
                        currentIndex = (currentIndex === thumbnails.length - 1) ? 0 : currentIndex + 1;
                    } else if (direction === 'prev') {
                        // Si es la primera, va a la última
                        currentIndex = (currentIndex === 0) ? thumbnails.length - 1 : currentIndex - 1;
                    }

                    // Selecciona la miniatura destino y le da clic a su imagen para disparar toda la lógica
                    const targetThumbnail = thumbnails[currentIndex];
                    if (targetThumbnail) {
                        const img = targetThumbnail.querySelector('img');
                        if (img) img.click();
                    }
                }

                // Asignar los eventos de click
                btnPrev.addEventListener('click', function (e) {
                    e.preventDefault();
                    navigateGallery('prev');
                });

                btnNext.addEventListener('click', function (e) {
                    e.preventDefault();
                    navigateGallery('next');
                });
            }
        });
        function changeImageWithActions(imageSrc, element, imageId) {
            // 1. Cambiar la imagen principal
            if (typeof changeImage === 'function') {
                changeImage(imageSrc, element);
            } else {
                document.getElementById('featuredImage').src = imageSrc;
                document.querySelectorAll('.thumbnail-item').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
            }

            // 2. Actualizar los endpoints de los formularios de acción
            const baseUrl = "{{ url('propiedades/imagen') }}";
            document.getElementById('form-portada').action = `${baseUrl}/${imageId}/portada`;
            document.getElementById('form-hero').action = `${baseUrl}/${imageId}/hero`;
            document.getElementById('form-delete').action = `${baseUrl}/${imageId}`;

            // 3. Leer estados de la miniatura actual
            const isMain = element.getAttribute('data-is-main') === '1';
            const isHero = element.getAttribute('data-is-hero') === '1';

            // 4. Alternar visibilidad de los BOTONES de acción
            document.getElementById('btn-action-portada').style.display = isMain ? 'none' : 'inline-flex';
            document.getElementById('btn-action-hero').style.display = isHero ? 'none' : 'inline-flex';

            // 5. Alternar visibilidad de los TEXTOS/BADGES flotantes en la imagen grande
            document.getElementById('featured-badge-portada').style.display = isMain ? 'inline-flex' : 'none';
            document.getElementById('featured-badge-hero').style.display = isHero ? 'inline-flex' : 'none';
        }

        function initMap() {
            const latVal = Number("{{ $property->latitude }}") || 0;
            const lngVal = Number("{{ $property->longitude }}") || 0;

            const pos = { lat: latVal, lng: lngVal };

            const map = new google.maps.Map(document.getElementById("map"), {
                center: pos,
                zoom: 17,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true
            });

            new google.maps.Marker({
                position: pos,
                map: map,
                draggable: false
            });
        }

        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
@endpush