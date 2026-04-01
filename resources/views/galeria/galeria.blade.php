@extends('layouts.app')

@section('title', 'Galería de Imágenes - VMS')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/galeria.css') }}">
@endpush

@section('content')

    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="page-header-inner">
                <div>
                    <p class="page-eyebrow">Multimedia</p>
                    <h1 class="page-title">Galería de Imágenes</h1>
                    <p class="page-subtitle">
                        <!-- {{  $imagenes->total() }} imágenes en total · 
                        {{ $imagenesAsignadas ?? 0 }} asignadas ·  -->
                        {{  $imagenes->total()}} imagenes sin asignar
                    </p>
                </div>
                <!-- <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Subir Imágenes
                </button> -->
            </div>
        </div>
    </div>

    <!-- ── FILTERS BAR ── -->
    <div class="filters-bar">
        <div class="container-fluid px-4">
            <div class="filters-inner">
                <!-- <select class="filter-select" id="filterVehiculo">
                    <option value="">Todos los vehículos</option>
                    <option value="sin-asignar">Sin asignar</option>
                    @foreach($vehiculos ?? [] as $vehiculo)
                        <option value="{{ $vehiculo->id_auto }}">
                            {{ $vehiculo->marca->nombre ?? '' }} {{ $vehiculo->modelo }} {{ $vehiculo->year }}
                        </option>
                    @endforeach
                </select> -->
                <span class="filters-count">
                    Mostrando <span id="countVisible">{{ count($imagenes ?? []) }}</span> imágenes
                </span>
            </div>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-wrapper">
        <div class="container-fluid px-4">

            @if(isset($imagenes) && count($imagenes) > 0)
            <div class="gallery-grid" id="galleryGrid">
                @foreach($imagenes as $imagen)
                <div class="gallery-item" data-vehiculo="{{ $imagen->id_auto ?? 'sin-asignar' }}">
                    <div class="image-container">
                        
                        <img src="{{ asset('storage/' . $imagen->ruta_archivo) }}" alt="{{ $imagen->nombre_original }}">
                        <div class="image-overlay"></div>
                        
                        @if($imagen->id_auto)
                            <span class="status-badge assigned">
                                <i class="bi bi-check-circle-fill"></i>
                                Asignada
                            </span>
                        @else
                            <span class="status-badge unassigned">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                Sin asignar
                            </span>
                        @endif

                        <div class="image-actions">
                            <button class="btn-image-action" title="Ver imagen" onclick="viewImage('{{ asset('storage/' . $imagen->ruta_archivo) }}')">
                                <i class="bi bi-eye"></i>
                            </button>
                    
                            <form action="{{ route('galeria.destroy', $imagen->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-image-action delete btn-delete" title="Eliminar">
                                    <i class="bi bi-trash"></i> 
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="gallery-body">
                        <div class="image-info">
                            <div class="image-name">
                                <i class="bi bi-file-image"></i>
                                Archivo
                            </div>
                            <div class="image-filename" title="{{ $imagen->nombre }}">
                                {{ $imagen->nombre }}
                            </div>
                        </div>

                        <form action="{{ route('galeria.asignar', $imagen->id) }}" method="POST" class="assign-form"> 
                            @csrf
                            
                            <div class="vehicle-select-group">
                                <label class="vehicle-select-label">Asignar a vehículo</label>
                                <div class="select-with-button">
                                    <select class="vehicle-select" name="id_auto" data-original="{{ $imagen->id_auto ?? '' }}">
                                        <option value="">— Sin asignar —</option>
                                        @foreach($vehiculos ?? [] as $vehiculo)
                                            <option value="{{ $vehiculo->id_auto }}" 
                                                {{ $imagen->id_auto == $vehiculo->id_auto ? 'selected' : '' }}>
                                                {{ $vehiculo->marca->nombre ?? '' }} {{ $vehiculo->modelo }} ({{ $vehiculo->year }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-assign" disabled>
                                        <i class="bi bi-check-lg"></i>
                                        <span>Confirmar</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-images"></i>
                </div>
                <div class="empty-title">Sin imágenes en la galería</div>
                <p class="empty-text">Sube las primeras imágenes para comenzar.</p>
                <!-- <button class="btn-upload mx-auto" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    <i class="bi bi-cloud-arrow-up"></i> Subir Imágenes
                </button> -->
            </div>
            @endif

        </div>
        @if($imagenes->hasPages() || $imagenes->total() > 0)
            <div class="pagination-wrapper">
                <div class="w-100">
                    @if($imagenes->total() > 0)
                        <div class="pagination-info">
                            Mostrando <strong>{{ $imagenes->firstItem() }}</strong> a <strong>{{ $imagenes->lastItem() }}</strong> 
                            de <strong>{{ $imagenes->total() }}</strong> imágenes 
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-center">
                        {{-- Mantenemos la consistencia con bootstrap-4 como en tu ejemplo --}}
                        {{ $imagenes->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/galeria.js') }}"></script>
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

