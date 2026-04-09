@extends('layouts.app')


@section('title', 'Vendedores')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/autos.css') }}">
@endpush

@section('content')
    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="page-header-inner">
                <div>
                    <p class="page-eyebrow">Administración</p>
                    <h1 class="page-title">Vendedores</h1>
                    <p class="page-subtitle">
                        <span class="fw-bold">{{ $sellers->total() }}</span> vendedores registrados

                    </p>
                </div>

                <button class="btn-new-vehicle" data-bs-toggle="modal" data-bs-target="#modalNuevoVendedor">
                    <i class="bi bi-person-plus"></i>
                    Nuevo Vendedor
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

                <select class="filter-select" name="neighborhood" onchange="this.form.submit()">
                    <option value="">Todas las zonas</option>
                    @foreach($neighborhoods ?? [] as $neighborhood)
                        <option value="{{ $neighborhood }}" {{ request('neighborhood') == $neighborhood ? 'selected' : '' }}>
                            {{ $neighborhood }}
                        </option>
                    @endforeach
                </select>

                <select class="filter-select" name="contract_type" onchange="this.form.submit()">
                    <option value="">Cualquier operación</option>
                    <option value="sale" {{ request('contract_type') == 'sale' ? 'selected' : '' }}>En Venta</option>
                    <option value="rent" {{ request('contract_type') == 'rent' ? 'selected' : '' }}>En Renta</option>
                </select>

                <span class="filters-count">
                    Mostrando <span>{{ $sellers->total() }}</span> vendedores
                </span>

                @if(request()->anyFilled(['search', 'type', 'neighborhood', 'contract_type']))
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
                @if(isset($sellers) && count($sellers) > 0)
                    <div class="table-responsive">
                        <table class="vms-table" id="clientsTable">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Correo Electrónico</th>
                                    <th>Notas</th>
                                    <th>Identificación</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sellers as $seller)
                                    <tr>
                                        <td>
                                            <div class="vehicle-cell">
                                                <div class="vehicle-thumb">
                                                    {{-- Icono de usuario por defecto --}}
                                                    <i class="bi bi-person-circle"
                                                        style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                                </div>
                                                <div>
                                                    <div class="vehicle-name">{{ $seller->name }}</div>
                                                    <div class="vehicle-brand">Registrado el
                                                        {{ $seller->created_at->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-weight: 500; color: var(--gray-700);">
                                            <i class="bi bi-telephone text-muted me-1"></i> {{ $seller->phone }}
                                        </td>
                                        <td style="color: var(--gray-500);">
                                            {{ $seller->email ?? 'Sin correo' }}
                                        </td>
                                        <td style="color: var(--gray-500); max-width: 200px;" class="text-truncate">
                                            {{ $seller->notes }}
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($seller->contract_path)
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ route('vendedores.archivo', $seller->id) }}" alt="ID {{ $seller->name }}"
                                                        loading="lazy" class="rounded shadow-sm border"
                                                        style="width: 50px; height: 40px; object-fit: cover; cursor: pointer;"
                                                        onclick="window.open(this.src, '_blank')">
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border">
                                                    <i class="bi bi-x-circle"></i> Sin ID
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons" style="justify-content: flex-end;">
                                                {{-- Botón Editar con todos los data-attributes para el JS --}}
                                                <a class="btn-action btn-edit" title="Editar Vendedor" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarVendedor" data-id="{{ $seller->id }}"
                                                    data-name="{{ $seller->name }}" data-email="{{ $seller->email }}"
                                                    data-phone="{{ $seller->phone }}" data-notes="{{ $seller->notes }}"
                                                    data-contract="{{ $seller->contract_path }}" style="cursor: pointer;">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                {{-- Formulario de eliminación --}}
                                                <form method="POST" class="form-eliminar" style="display:inline;">
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
                    @if($sellers->hasPages())
                        <div class="pagination-wrapper">
                            <div class="w-100">
                                <div class="pagination-info">
                                    Mostrando <strong>{{ $sellers->firstItem() }}</strong> a
                                    <strong>{{ $sellers->lastItem() }}</strong>
                                    de <strong>{{ $sellers->total() }}</strong> vendedores
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $sellers->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="empty-title">Sin vendedores registrados</div>
                        <p class="empty-text">Agrega el primer vendedor para comenzar.</p>
                        <button class="btn-new-vehicle mx-auto" data-bs-toggle="modal" data-bs-target="#modalNuevoVendedor">
                            <i class="bi bi-person-plus"></i> Nuevo Vendedor
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('vendedores.modals.modal-create')
    @include('vendedores.modals.modal-edit')

    <!-- DATA PARA JS -->
    <div id="laravel-data" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" data-success="{{ session('success') }}"
        data-error-msg="{{ $errors->first() }}">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/seller.js') }}"></script>

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

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const editSellerId = "{{ session('edit_seller_id') }}"; // Nombre correcto

                if (editSellerId) {
                    const modalEditElement = document.getElementById('modalEditarVendedor');
                    if (modalEditElement) {
                        const modalEdit = new bootstrap.Modal(modalEditElement);
                        const formEdit = document.getElementById('formEditarVendedor');
                        // Usamos la variable corregida
                        formEdit.action = `/vendedores/${editSellerId}`;
                        modalEdit.show();
                    }
                } else {
                    console.log("Error en creación de nuevo vendedor");
                    const modalCreateElement = document.getElementById('modalNuevoVendedor');
                    if (modalCreateElement) {
                        const modalCreate = new bootstrap.Modal(modalCreateElement);
                        modalCreate.show();
                    }
                }
            });
        </script>
    @endif
@endsection