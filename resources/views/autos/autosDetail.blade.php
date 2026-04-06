@extends('layouts.app')


@section('title', 'Detalle del Vehículo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/detalle-vehiculo.css') }}">
@endpush

@section('content')
    <!-- ── PAGE HEADER ── -->
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
                <div class="header-actions">
                </div>
            </div>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-wrapper">
        <div class="container-fluid px-4">
            <div class="row g-4">

                <div class="col-12 col-lg-8">

                    <div class="content-card">
                        <div class="card-body-custom p-0">
                            @if($property->images->count() > 0)
                                <div class="gallery-main">
                                    <div class="gallery-featured" id="galleryFeatured">
                                        <img src="{{ asset('storage/' . $property->images->first()->path) }}"
                                            alt="{{ $property->title }}" id="featuredImage">
                                        <button class="btn-fullscreen" onclick="viewFullscreen()">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </button>
                                    </div>
                                    <div class="gallery-thumbnails">
                                        @foreach($property->images as $index => $image)
                                            <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                                data-imagen-id="{{ $image->id }}">
                                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagen {{ $index + 1 }}"
                                                    onclick="changeImage('{{ asset('storage/' . $image->path) }}', this.parentElement)">

                                                @if($image->is_featured)
                                                    <span class="badge-portada">
                                                        <i class="bi bi-star-fill"></i>
                                                        Portada
                                                    </span>
                                                @endif

                                                <div class="thumbnail-actions">
                                                    @if(!$image->is_featured)
                                                        <form action="{{ route('propiedades.imagen.portada', $image->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn-portada-thumbnail"
                                                                title="Marcar como portada">
                                                                <i class="bi bi-star"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('propiedades.imagen.delete', $image->id) }}"
                                                        method="POST" class="delete-image-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn-delete-thumbnail" title="Eliminar imagen">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="gallery-empty-large">
                                    <i class="bi bi-image"></i>
                                    <p>Esta propiedad no tiene imágenes</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2 class="card-title-custom">
                                <i class="bi bi-house-door"></i>
                                Detalles de la Propiedad
                            </h2>
                        </div>
                        <div class="card-body-custom">
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-rulers"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Terreno</div>
                                        <div class="spec-value">{{ $property->m2_land }} m²</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-building"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Construcción</div>
                                        <div class="spec-value">{{ $property->m2_construction }} m²</div>
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

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-p-square"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Estacionamientos</div>
                                        <div class="spec-value">{{ $property->parking_spots }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon"><i class="bi bi-geo-alt"></i></div>
                                    <div class="spec-content">
                                        <div class="spec-label">Colonia</div>
                                        <div class="spec-value">{{ $property->neighborhood }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($property->description)
                        <div class="content-card">
                            <div class="card-header-custom">
                                <h2 class="card-title-custom">
                                    <i class="bi bi-card-text"></i>
                                    Descripción
                                </h2>
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
                            <div class="price-label">Precio de {{ $property->contract_type == 'sale' ? 'Venta' : 'Renta' }}
                            </div>
                            <div class="price-value">${{ number_format($property->price, 2) }}</div>
                            @if($property->is_featured)
                                <div class="price-note">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    Propiedad Destacada
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2 class="card-title-custom">
                                <i class="bi bi-info-square"></i>
                                Información del Sistema
                            </h2>
                        </div>
                        <div class="card-body-custom">
                            <div class="info-list">
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-calendar-plus"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">Fecha de registro</div>
                                        <div class="info-value">{{ $property->created_at->format('d/m/Y') }}</div>
                                        <div class="info-sub">{{ $property->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">Agente responsable</div>
                                        <div class="info-value">{{ $property->user->name ?? 'Sistema' }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-images"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">Imágenes</div>
                                        <div class="info-value">{{ $property->images->count() }} fotos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

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