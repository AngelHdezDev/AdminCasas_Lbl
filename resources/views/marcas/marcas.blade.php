@extends('layouts.app')

@section('title', 'Marcas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marcas.css') }}">
@endpush

@section('content')
    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="page-header-inner">
                <div>
                    <p class="page-eyebrow">Catálogo</p>
                    <h1 class="page-title">Marcas</h1>
                    <p class="page-subtitle">
                        {{ $marcas->total() }} marcas registradas en el sistema
                    </p>
                </div>
                <button class="btn-new-marca" data-bs-toggle="modal" data-bs-target="#modalNuevaMarca">
                    <i class="bi bi-plus-lg"></i>
                    Nueva Marca
                </button>
            </div>
        </div>
    </div>

    <!-- ── FILTERS BAR ── -->
    <form action="{{ route('marcas.index') }}" method="GET" class="search-box">
        <div class="filters-bar">
            <div class="container-fluid px-4">
                <div class="filters-inner">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="search-input" placeholder="Buscar marca..."
                            value="{{ request('search') }}">
                    </div>
                    <span class="filters-count">
                        Mostrando <span id="countVisible">{{ count($marcas ?? []) }}</span> marcas
                    </span>
                </div>
            </div>
        </div>
    </form>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-wrapper">
        <div class="container-fluid px-4">

            @if(isset($marcas) && count($marcas) > 0)
                <div class="marcas-grid" id="marcasGrid">
                    @foreach($marcas as $marca)
                        <div class="marca-card {{ $marca->active == 0 ? 'inactive' : '' }}"
                            data-nombre="{{ strtolower($marca->nombre) }}">
                            <div class="marca-logo-container">
                                @if($marca->imagen)
                                    <img src="{{ asset('storage/' . $marca->imagen) }}" width="50">
                                @else
                                    <i class="bi bi-tag-fill marca-logo-placeholder"></i>
                                @endif
                                @if($marca->active == 0)
                                    <div class="inactive-badge">
                                        <i class="bi bi-eye-slash"></i>
                                        Inactiva
                                    </div>
                                @endif
                            </div>
                            <div class="marca-body">
                                <h3 class="marca-name">{{ $marca->nombre }}</h3>
                                <div class="marca-meta">
                                    <div class="marca-stat">
                                        <i class="bi bi-car-front-fill"></i>
                                        <span>{{ $marca->autos_count ?? 0 }}</span> vehículos
                                    </div>
                                    <div class="marca-stat">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $marca->created_at }}
                                    </div>
                                </div>
                                <div class="marca-actions">
                                    <a class="btn-action-marca" data-bs-toggle="modal" data-bs-target="#modalNuevaMarca"
                                        data-tipo="editar" data-id="{{ $marca->id_marca }}" data-nombre="{{ $marca->nombre }}"
                                        data-imagen="{{ $marca->imagen ? asset('storage/' . $marca->imagen) : '' }}">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="{{ route('marcas.changeStatus', $marca->id_marca) }}" method="POST"
                                        class="delete-form" style="flex: 1;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn-action-marca {{ $marca->active == 0 ? 'activate' : 'delete' }} btn-toggle-status"
                                            data-marca-id="{{ $marca->id_marca }}" data-marca-nombre="{{ $marca->nombre }}"
                                            data-active="{{ $marca->active }}" style="width: 100%;">
                                            @if($marca->active == 0)
                                                <i class="bi bi-check-circle"></i>
                                                Activar
                                            @else
                                                <i class="bi bi-x-circle"></i>
                                                Desactivar
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($marcas->hasPages())
                    <div class="pagination-wrapper">
                        <div class="w-100">
                            @if($marcas->total() > 0)
                                <div class="pagination-info">
                                    Mostrando <strong>{{ $marcas->firstItem() }}</strong> a <strong>{{ $marcas->lastItem() }}</strong>
                                    de <strong>{{ $marcas->total() }}</strong> marcas
                                </div>
                            @endif
                            <div class="d-flex justify-content-center">
                                {{ $marcas->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="empty-title">Sin marcas registradas</div>
                    <p class="empty-text">Agrega la primera marca al catálogo para comenzar.</p>
                    <button class="btn-new-marca mx-auto" data-bs-toggle="modal" data-bs-target="#modalNuevaMarca">
                        <i class="bi bi-plus-lg"></i> Nueva Marca
                    </button>
                </div>
            @endif

        </div>
    </div>

    <!-- ══════════════════════════════════
                                                         MODAL — NUEVA MARCA
                                                    ══════════════════════════════════ -->
    <div class="modal fade" id="modalNuevaMarca" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <div class="modal-header-inner">
                        <div class="modal-title-group">
                            <div class="modal-icon">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                            <div>
                                <div class="modal-title-text">Nueva Marca</div>
                                <div class="modal-subtitle-text">Registra una marca de vehículos</div>
                            </div>
                        </div>
                        <button class="btn-close-custom" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <form action="{{ route('marcas.store') }}" method="POST" enctype="multipart/form-data" id="formNuevaMarca">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">

                        <!-- Nombre de la marca -->
                        <div class="field-group">
                            <label class="field-label">
                                Nombre de la Marca <span class="required">*</span>
                            </label>
                            <input type="text" class="field-input" name="nombre" id="nombre"
                                placeholder="Toyota, Honda, Nissan, BMW..." required>
                        </div>

                        <!-- Logo de la marca -->
                        <div class="field-group">
                            <label class="field-label">
                                Logo de la Marca <span class="required">*</span>
                            </label>
                            <div class="file-upload-zone" id="uploadZone">
                                <input type="file" name="imagen" id="imagen" accept="image/jpeg,image/jpg,image/png"
                                    required>
                                <div id="uploadInitialState">
                                    <div class="upload-icon">
                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                    </div>
                                    <div class="upload-text">
                                        Arrastra o haz clic para seleccionar
                                    </div>
                                    <div class="upload-hint">
                                        Formatos aceptados: <span class="highlight">JPG, PNG</span> · Máximo <span
                                            class="highlight">2MB</span>
                                    </div>
                                </div>
                                <div id="uploadPreviewState" style="display: none;">
                                    <img id="uploadPreviewImage" src="" alt="Preview"
                                        style="max-width: 100%; max-height: 200px; object-fit: contain; border-radius: 8px;">
                                </div>
                            </div>
                            <div class="preview-container" id="previewContainer">
                                <div class="preview-inner">
                                    <div class="preview-thumb">
                                        <img id="previewImage" src="" alt="Preview">
                                    </div>
                                    <div class="preview-info">
                                        <div class="preview-name" id="previewName"></div>
                                        <div class="preview-size" id="previewSize"></div>
                                    </div>
                                    <button type="button" class="btn-remove-image" id="btnRemoveImage"
                                        title="Quitar imagen">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer-custom">
                        <span class="footer-note">
                            <i class="bi bi-shield-check"></i>
                            Los campos con <span style="color:var(--gold);font-weight:700;margin:0 2px;">*</span> son
                            requeridos
                        </span>
                        <div class="footer-actions">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-check-lg"></i>
                                Guardar Marca
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DATA PARA JS -->
    <div id="laravel-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-validation-error="{{ $errors->first() }}" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/marcas.js') }}"></script>

@endsection