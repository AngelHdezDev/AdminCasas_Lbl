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
                        <a href="{{ route('autos.index') }}">
                            <i class="bi bi-arrow-left"></i>
                            Volver a vehículos
                        </a>
                    </div>
                    <h1 class="page-title">{{ $auto->marca->nombre ?? '' }} {{ $auto->modelo }}</h1>
                    <p class="page-subtitle">
                        {{ $auto->year }} · {{ $auto->tipo }} · {{ $auto->color }}
                    </p>
                </div>
                <div class="header-actions">
                    <!-- <a class="btn-header btn-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                    Editar Vehículo
                                                </a>
                                                <form action="{{ route('autos.destroy', $auto->id_auto) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn-header btn-danger btn-delete">
                                                        <i class="bi bi-trash"></i>
                                                        Eliminar
                                                    </button>
                                                </form> -->
                </div>
            </div>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-wrapper">
        <div class="container-fluid px-4">
            <div class="row g-4">

                <!-- COLUMNA PRINCIPAL: Galería e Información -->
                <div class="col-12 col-lg-8">

                    <!-- Galería de Imágenes -->
                    <div class="content-card">
                        <div class="card-body-custom p-0">
                            @if($auto->imagenes->count() > 0)
                                <div class="gallery-main">
                                    <div class="gallery-featured" id="galleryFeatured">
                                        <img src="{{ asset('storage/' . $auto->imagenes->first()->imagen) }}"
                                            alt="{{ $auto->marca->nombre ?? '' }} {{ $auto->modelo }}" id="featuredImage">
                                        <button class="btn-fullscreen" onclick="viewFullscreen()">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </button>
                                    </div>
                                    @if($auto->imagenes->count() > 0)
                                        <div class="gallery-thumbnails">
                                            @foreach($auto->imagenes as $index => $imagen)
                                                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                                    data-imagen-id="{{ $imagen->id_imagen }}">
                                                    <img src="{{ asset('storage/' . $imagen->imagen) }}" alt="Imagen {{ $index + 1 }}"
                                                        onclick="changeImage('{{ asset('storage/' . $imagen->imagen) }}', this.parentElement)">

                                                    <!-- Badge de portada -->
                                                    @if($imagen->thumbnail)
                                                        <span class="badge-portada">
                                                            <i class="bi bi-star-fill"></i>
                                                            Portada
                                                        </span>
                                                    @endif

                                                    <!-- Botones de acción -->
                                                    <div class="thumbnail-actions">
                                                        <!-- Botón marcar como portada -->
                                                        @if(!$imagen->thumbnail)
                                                            <form action="{{ route('autos.imagen.portada', $imagen->id_imagen) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn-portada-thumbnail"
                                                                    title="Marcar como portada">
                                                                    <i class="bi bi-star"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <!-- Botón eliminar -->
                                                        <form action="{{ route('autos.imagen.delete', $imagen->id_imagen) }}"
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
                                    @endif
                                </div>
                            @else
                                <div class="gallery-empty-large">
                                    <i class="bi bi-image"></i>
                                    <p>Este vehículo no tiene imágenes</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Especificaciones Técnicas -->
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2 class="card-title-custom">
                                <i class="bi bi-gear-wide-connected"></i>
                                Especificaciones Técnicas
                            </h2>
                        </div>
                        <div class="card-body-custom">
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Marca</div>
                                        <div class="spec-value">{{ $auto->marca->nombre ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Año</div>
                                        <div class="spec-value">{{ $auto->year }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Tipo</div>
                                        <div class="spec-value">{{ $auto->tipo }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-palette"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Color</div>
                                        <div class="spec-value">{{ $auto->color }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-gear"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Transmisión</div>
                                        <div class="spec-value">{{ $auto->transmision }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-fuel-pump"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Combustible</div>
                                        <div class="spec-value">{{ $auto->combustible }}</div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-speedometer"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Kilometraje</div>
                                        <div class="spec-value">
                                            @if($auto->ocultar_kilometraje)
                                                <span class="badge-oculto">
                                                    <i class="bi bi-eye-slash"></i> Oculto
                                                </span>
                                            @else
                                                {{ number_format($auto->kilometraje) }} km
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <i class="bi bi-bookmark-star"></i>
                                    </div>
                                    <div class="spec-content">
                                        <div class="spec-label">Estado</div>
                                        <div class="spec-value">
                                            @if($auto->consignacion)
                                                <span class="badge-consignacion">
                                                    <i class="bi bi-bookmark-star-fill"></i> Consignación
                                                </span>
                                            @else
                                                <span class="badge-propio">
                                                    <i class="bi bi-check-circle-fill"></i> Propio
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    @if($auto->descripcion)
                        <div class="content-card">
                            <div class="card-header-custom">
                                <h2 class="card-title-custom">
                                    <i class="bi bi-card-text"></i>
                                    Descripción
                                </h2>
                            </div>
                            <div class="card-body-custom">
                                <p class="description-text">{{ $auto->descripcion }}</p>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- COLUMNA LATERAL: Información de Venta -->
                <div class="col-12 col-lg-4">

                    <!-- Precio -->
                    <div class="content-card price-card">
                        <div class="card-body-custom text-center">
                            <div class="price-label">Precio de Venta</div>
                            <div class="price-value">${{ number_format($auto->precio, 2) }}</div>
                            @if($auto->consignacion)
                                <div class="price-note">
                                    <i class="bi bi-info-circle"></i>
                                    Vehículo en consignación
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Detalles Adicionales -->
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
                                    <div class="info-icon">
                                        <i class="bi bi-calendar-plus"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Fecha de registro</div>
                                        <div class="info-value">{{ $auto->created_at}}</div>
                                        <div class="info-sub">{{ $auto->created_at}}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Registrado por</div>
                                        <div class="info-value">{{ $auto->creator->nombre ?? 'Sistema' }}</div>
                                    </div>
                                </div>

                                @if($auto->updated_at != $auto->created_at)
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="bi bi-pencil-square"></i>
                                        </div>
                                        <div class="info-content">
                                            <div class="info-label">Última actualización</div>
                                            <div class="info-value">{{ $auto->updated_at }}</div>
                                            <div class="info-sub">{{ $auto->updated_at }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-images"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Imágenes</div>
                                        <div class="info-value">{{ $auto->imagenes->count() }} fotos</div>
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