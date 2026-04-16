@extends('layouts.app')


@section('title', 'Propiedades')

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
                <button class="btn-new-vehicle" onclick="window.location='{{ route('propiedades.create') }}'">
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
                    <input type="text" name="search" class="search-input"
                        placeholder="Buscar por título, descripción o calle..." value="{{ request('search') }}"
                        id="searchInput">
                </div>

                <select class="filter-select" name="type" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>Casas</option>
                    <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Departamentos
                    </option>
                    <option value="terreno" {{ request('type') == 'terreno' ? 'selected' : '' }}>Terrenos</option>
                    <option value="local" {{ request('type') == 'local' ? 'selected' : '' }}>Locales</option>
                </select>

                <select class="filter-select" name="state" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach($states ?? [] as $state)
                        <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>

                <select class="filter-select" name="contract_type" onchange="this.form.submit()">
                    <option value="">Cualquier operación</option>
                    <option value="sale" {{ request('contract_type') == 'sale' ? 'selected' : '' }}>En Venta</option>
                    <option value="rent" {{ request('contract_type') == 'rent' ? 'selected' : '' }}>En Renta</option>
                </select>

                <span class="filters-count">
                    Mostrando <span>{{ $properties->total() }}</span> propiedades
                </span>

                @if(request()->anyFilled(['search', 'type', 'state', 'contract_type']))
                    <a href="{{ route('propiedades.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
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
                                                    data-title="{{ $property->title }}" data-cp="{{ $property->cp }}"
                                                    data-neighborhood="{{ $property->neighborhood }}"
                                                    data-type="{{ $property->type }}" data-address="{{ $property->address }}"
                                                    data-m2_land="{{ $property->m2_land }}"
                                                    data-m2_construction="{{ $property->m2_construction }}"
                                                    data-bedrooms="{{ $property->bedrooms }}"
                                                    data-bathrooms="{{ $property->bathrooms }}"
                                                    data-parking_spots="{{ $property->parking_spots }}"
                                                    data-contract_type="{{ $property->contract_type }}"
                                                    data-price="{{ $property->price }}"
                                                    data-is_featured="{{ $property->is_featured ? '1' : '0' }}"
                                                    data-show_public_address="{{ $property->show_public_address ? '1' : '0' }}"
                                                    data-description="{{ $property->description }}"
                                                    data-city="{{ $property->city }}" data-state="{{ $property->state }}"
                                                    data-seller_id="{{ $property->seller_id }}"
                                                    data-client_id="{{ $property->client_id }}" style="cursor: pointer;">
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