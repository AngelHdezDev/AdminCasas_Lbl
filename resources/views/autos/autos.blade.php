@extends('layouts.app')


@section('title', 'Vehículos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/autos.css') }}">
@endpush

@section('content')
    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="page-header-inner">
                <div>
                    <p class="page-eyebrow">Inventario</p>
                    <h1 class="page-title">Propiedades</h1>
                    <p class="page-subtitle">
                        {{ $properties->total() }} propiedades registradas
                        @if(isset($totalConsignacion) && $totalConsignacion > 0)
                            &mdash; {{ $totalConsignacion }} en consignación
                        @endif
                    </p>
                </div>
                <button class="btn-new-vehicle" data-bs-toggle="modal" data-bs-target="#modalPropiedad">
                    <i class="bi bi-plus-lg"></i>
                    Agregar Propiedad
                </button>
            </div>
        </div>
    </div>

    <!-- ── FILTERS BAR ── -->
    <div class="filters-bar">
        <div class="container-fluid px-4">
            <form action="{{ route('propiedades.index') }}" method="GET" class="filters-inner" id="filterForm">

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="search-input" placeholder="Buscar modelo, color, año..."
                        value="{{ request('search') }}" id="searchInput">
                </div>

                <select class="filter-select" name="marca" onchange="this.form.submit()">
                    <option value="">Todas las marcas</option>
                    @foreach($marcas ?? [] as $marca)
                        <option value="{{ $marca->id_marca }}" {{ request('marca') == $marca->id_marca ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                </select>

                <select class="filter-select" name="tipo" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos ?? [] as $tipo)
                        <option value="{{ $tipo->tipo }}" {{ request('tipo') == $tipo->tipo ? 'selected' : '' }}>
                            {{ $tipo->tipo }}
                        </option>
                    @endforeach
                </select>

                <select class="filter-select" name="consignacion" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="1" {{ request('consignacion') === '1' ? 'selected' : '' }}>En consignación</option>
                    <option value="0" {{ request('consignacion') === '0' ? 'selected' : '' }}>Propios</option>
                </select>

                <span class="filters-count">
                    Mostrando <span>{{ $properties->total() }}</span> propiedades
                </span>

                @if(request()->anyFilled(['search', 'marca', 'tipo', 'consignacion']))
                    <a href="{{ route('autos.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-wrapper">
        <div class="container-fluid px-4">

            @if(session('success'))
                <div id="alertBox"></div>
            @endif

            <div class="table-card">
                @if(isset($properties) && count($properties) > 0)
                    <div class="table-responsive">
                        <table class="vms-table" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Propiedad</th>
                                    <th>Ubicación</th>
                                    <th>Tipo</th>
                                    <th>Construcción</th>
                                    <th>Hab.</th>
                                    <th>Baños</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($properties as $property)
                                    <tr>
                                        <td>
                                            <div class="vehicle-cell">
                                                <div class="vehicle-thumb">
                                                    {{-- Aquí verificamos si tiene imagen principal, si no, un icono de casa --}}
                                                    @if($property->images && $property->images->where('is_main', true)->first())
                                                        <img src="{{ asset('storage/' . $property->images->where('is_main', true)->first()->path) }}"
                                                            alt="{{ $property->title }}">
                                                    @else
                                                        <i class="bi bi-house-door"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="vehicle-name">{{ $property->title }}</div>
                                                    <div class="vehicle-brand">
                                                        {{ $property->contract_type == 'sale' ? 'Venta' : 'Renta' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: var(--gray-500);">
                                            <i class="bi bi-geo-alt"></i> {{ $property->neighborhood }}
                                        </td>
                                        <td>
                                            <span class="badge-tipo">{{ $property->type }}</span>
                                        </td>
                                        <td style="font-weight: 500; color: var(--gray-700);">{{ $property->m2_construction }} m²
                                        </td>
                                        <td style="color: var(--gray-500);">{{ $property->bedrooms }}</td>
                                        <td style="color: var(--gray-500);">{{ $property->bathrooms }}</td>
                                        <td>
                                            <span class="price-cell">${{ number_format($property->price, 0) }}</span>
                                        </td>
                                        <td>
                                            {{-- Badge dinámico según el status --}}
                                            @if($property->status == 'available')
                                                <span class="badge-consignacion badge-propio"><i class="bi bi-check-circle-fill"></i>
                                                    Disponible</span>
                                            @else
                                                <span class="badge-consignacion"><i class="bi bi-x-circle-fill"></i> Vendida</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons" style="justify-content: flex-end;">
                                                <a href="{{ route('propiedades.show', $property->id) }}" class="btn-action"
                                                    title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a class="btn-action btn-edit" title="Editar" data-bs-toggle="modal"
                                                    data-bs-target="#modalPropiedad" data-id="{{ $property->id }}"
                                                    data-title="{{ $property->title }}"
                                                    data-neighborhood="{{ $property->neighborhood }}"
                                                    data-type="{{ $property->type }}" data-address="{{ $property->address }}"
                                                    data-m2_land="{{ $property->m2_land }}"
                                                    data-m2_construction="{{ $property->m2_construction }}"
                                                    data-bedrooms="{{ $property->bedrooms }}"
                                                    data-bathrooms="{{ $property->bathrooms }}"
                                                    data-parking_spots="{{ $property->parking_spots }}"
                                                    data-contract_type="{{ $property->contract_type }}"
                                                    data-price="{{ $property->price }}"
                                                    data-is_featured="{{ $property->is_featured }}"
                                                    data-show_address="{{ $property->show_address }}"
                                                    data-description="{{ $property->description }}" style="cursor: pointer;">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="{{ route('propiedades.destroy', $property->id) }}" method="POST"
                                                    class="form-eliminar" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action delete btn-delete" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if($properties->hasPages())
                        <div class="pagination-wrapper">
                            <div class="w-100">
                                <div class="pagination-info">
                                    Mostrando <strong>{{ $properties->firstItem() }}</strong> a
                                    <strong>{{ $properties->lastItem() }}</strong>
                                    de <strong>{{ $properties->total() }}</strong> propiedades
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $properties->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-house-door"></i>
                        </div>
                        <div class="empty-title">Sin propiedades registradas</div>
                        <p class="empty-text">Agrega la primera propiedad al inventario para comenzar.</p>
                        <button class="btn-new-vehicle mx-auto" data-bs-toggle="modal" data-bs-target="#modalNuevaPropiedad">
                            <i class="bi bi-plus-lg"></i> Agregar Propiedad
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 
                             MODAL — NUEVO VEHÍCULO
                        -->
    <div class="modal fade" id="modalPropiedad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl ">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="modal-header-inner">
                        <div class="modal-title-group">
                            <div class="modal-icon">
                                <i class="bi bi-house-heart-fill"></i>
                            </div>
                            <div>
                                <div class="modal-title-text" id="modalTitle">Nueva Propiedad</div>
                                <div class="modal-subtitle-text">Completa los datos para registrar en el inventario</div>
                            </div>
                        </div>
                        <button class="btn-close-custom" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <form action="{{ route('propiedades.store') }}" method="POST" id="formPropiedad">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">

                    <div class="modal-body">
                        <div class="row g-4">

                            <div class="col-lg-6">
                                <div class="form-section">
                                    <div class="form-section-title">Identificación y Ubicación</div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="field-group">
                                                <label class="field-label">Título de la publicación <span
                                                        class="required">*</span></label>
                                                <input type="text" class="field-input" name="title" id="title"
                                                    placeholder="Ej: Casa moderna con alberca en Zapopan" required>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="field-group">
                                                <label class="field-label">Colonia / Zona <span
                                                        class="required">*</span></label>
                                                <input type="text" class="field-input" name="neighborhood" id="neighborhood"
                                                    placeholder="Ej: Puerta de Hierro, Americana..." required>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="field-group">
                                                <label class="field-label">Tipo <span class="required">*</span></label>
                                                <select class="field-input" name="type" id="type" required>
                                                    <option value="">Seleccionar</option>
                                                    <option value="house">Casa</option>
                                                    <option value="apartment">Departamento</option>
                                                    <option value="land">Terreno</option>
                                                    <option value="commercial">Local Comercial</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="field-group">
                                                <label class="field-label">Dirección Completa <span
                                                        class="required">*</span></label>
                                                <input type="text" class="field-input" name="address" id="address"
                                                    placeholder="Calle, número exterior, interior..." required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">Dimensiones y Distribución</div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="field-group">
                                                <label class="field-label">Terreno (m²) <span
                                                        class="required">*</span></label>
                                                <input type="number" class="field-input" name="m2_land" id="m2_land"
                                                    placeholder="0" required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="field-group">
                                                <label class="field-label">Construcción (m²) <span
                                                        class="required">*</span></label>
                                                <input type="number" class="field-input" name="m2_construction"
                                                    id="m2_construction" placeholder="0" required>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="field-group">
                                                <label class="field-label">Habitaciones</label>
                                                <input type="number" class="field-input" name="bedrooms" id="bedrooms"
                                                    min="0" value="{{ old('bedrooms', 0) }}"
                                                    onfocus="if(this.value=='0')this.value=''"
                                                    onblur="if(this.value=='')this.value='0'">
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="field-group">
                                                <label class="field-label">Baños</label>
                                                {{-- CORREGIDO: name="bathrooms" e id="bathrooms" --}}
                                                <input type="number" class="field-input" name="bathrooms" id="bathrooms"
                                                    min="0" value="{{ old('bathrooms', 0) }}"
                                                    onfocus="if(this.value=='0')this.value=''"
                                                    onblur="if(this.value=='')this.value='0'">
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="field-group">
                                                <label class="field-label">Cochera</label>
                                                <input type="number" class="field-input" name="parking_spots"
                                                    id="parking_spots" min="0" value="{{ old('parking_spots', 0) }}"
                                                    onfocus="if(this.value=='0')this.value=''"
                                                    onblur="if(this.value=='')this.value='0'">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-section">
                                    <div class="form-section-title">Comercialización</div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="field-group">
                                                <label class="field-label">Tipo de Contrato <span
                                                        class="required">*</span></label>
                                                <select class="field-input" name="contract_type" id="contract_type"
                                                    required>
                                                    <option value="sale">Venta</option>
                                                    <option value="rent">Renta</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="field-group">
                                                <label class="field-label">Precio <span class="required">*</span></label>
                                                <input type="number" class="field-input" name="price" id="price"
                                                    placeholder="0.00" min="0" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="toggle-row">
                                            <div class="toggle-info">
                                                <div class="toggle-info-icon"><i class="bi bi-star-fill"></i></div>
                                                <div>
                                                    <div class="toggle-label">Propiedad Destacada</div>
                                                    <div class="toggle-desc">Aparecerá en los primeros resultados</div>
                                                </div>
                                            </div>
                                            <div class="form-switch-custom form-check">
                                                <input class="form-check-input" type="checkbox" name="is_featured"
                                                    id="is_featured" value="1">
                                            </div>
                                        </div>

                                        <div class="toggle-row">
                                            <div class="toggle-info">
                                                <div class="toggle-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                                <div>
                                                    <div class="toggle-label">Dirección Pública</div>
                                                    <div class="toggle-desc">Mostrar calle y número en la web</div>
                                                </div>
                                            </div>
                                            <div class="form-switch-custom form-check">
                                                <input class="form-check-input" type="checkbox" name="show_address"
                                                    id="show_address" value="1" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">Detalles Adicionales</div>
                                    <div class="field-group" style="margin-bottom: 0;">
                                        <label class="field-label">Descripción de la propiedad <span
                                                style="color:var(--gray-300); font-weight:400;">(Opcional)</span></label>
                                        <textarea class="field-input" name="description" id="description" rows="7"
                                            placeholder="Menciona amenidades, acabados, cercanía a puntos de interés..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <span class="footer-note">
                            <i class="bi bi-shield-check"></i>
                            Los campos con <span style="color:var(--gold);font-weight:700;margin:0 2px;">*</span> son
                            requeridos
                        </span>
                        <div class="footer-actions">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-submit" id="btnSubmit">
                                <i class="bi bi-plus-lg" id="btnSubmitIcon"></i>
                                <span id="btnSubmitText">Registrar Propiedad</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DATA PARA JS -->
    <div id="laravel-data" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" data-success="{{ session('success') }}"
        data-error-msg="{{ $errors->first() }}">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/autos.js') }}"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                title: '¡Logrado!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#1e293b', // Ajusta al color de tu VMS
                confirmButtonText: 'Genial'
            });
        </script>
    @endif


@endsection