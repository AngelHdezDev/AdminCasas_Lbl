@extends('layouts.app')


@section('title', 'Clientes')

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
                    <h1 class="page-title">Clientes</h1>
                    <p class="page-subtitle">
                        <span class="fw-bold">{{ $clients->total() }}</span> clientes registrados

                    </p>
                </div>

                <button class="btn-new-vehicle" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                    <i class="bi bi-person-plus"></i>
                    Nuevo Cliente
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
                    Mostrando <span>{{ $clients->total() }}</span> clientes
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
                @if(isset($clients) && count($clients) > 0)
                    <div class="table-responsive">
                        <table class="vms-table" id="clientsTable">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Correo Electrónico</th>
                                    <th>Notas</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <div class="vehicle-cell">
                                                <div class="vehicle-thumb">
                                                    {{-- Icono de usuario por defecto --}}
                                                    <i class="bi bi-person-circle"
                                                        style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                                </div>
                                                <div>
                                                    <div class="vehicle-name">{{ $client->name }}</div>
                                                    <div class="vehicle-brand">Registrado el
                                                        {{ $client->created_at->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-weight: 500; color: var(--gray-700);">
                                            <i class="bi bi-telephone text-muted me-1"></i> {{ $client->phone }}
                                        </td>
                                        <td style="color: var(--gray-500);">
                                            {{ $client->email ?? 'Sin correo' }}
                                        </td>
                                        <td style="color: var(--gray-500); max-width: 200px;" class="text-truncate">
                                            {{ $client->notes }}
                                        </td>
                                        <td>
                                            <div class="action-buttons" style="justify-content: flex-end;">
                                                {{-- Botón Editar con todos los data-attributes para el JS --}}
                                                <a class="btn-action btn-edit" title="Editar Cliente" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarCliente" data-id="{{ $client->id }}"
                                                    data-name="{{ $client->name }}" data-email="{{ $client->email }}"
                                                    data-phone="{{ $client->phone }}" data-notes="{{ $client->notes }}"
                                                    data-identification="{{ $client->identification_path }}"
                                                    style="cursor: pointer;">
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
                    @if($clients->hasPages())
                        <div class="pagination-wrapper">
                            <div class="w-100">
                                <div class="pagination-info">
                                    Mostrando <strong>{{ $clients->firstItem() }}</strong> a
                                    <strong>{{ $clients->lastItem() }}</strong>
                                    de <strong>{{ $clients->total() }}</strong> clientes
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $clients->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="empty-title">Sin clientes registrados</div>
                        <p class="empty-text">Agrega el primer cliente para comenzar.</p>
                        <button class="btn-new-vehicle mx-auto" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                            <i class="bi bi-person-plus"></i> Nuevo Cliente
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('clientes.modals.modal-create')
    @include('clientes.modals.modal-edit')

    <!-- DATA PARA JS -->
    <div id="laravel-data" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" data-success="{{ session('success') }}"
        data-error-msg="{{ $errors->first() }}">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/client.js') }}"></script>

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
                const editClientId = "{{ session('edit_client_id') }}";

                if (editClientId) {
                    console.log("Error en edición para el cliente:", editClientId);

                    const modalEditElement = document.getElementById('modalEditarCliente');
                    if (modalEditElement) {
                        const modalEdit = new bootstrap.Modal(modalEditElement);
                        const formEdit = document.getElementById('formEditarCliente');
                        formEdit.action = `/clientes/${editClientId}`;
                        modalEdit.show();
                    }
                } else {
                    console.log("Error en creación de nuevo cliente");
                    const modalCreateElement = document.getElementById('modalNuevoCliente');
                    if (modalCreateElement) {
                        const modalCreate = new bootstrap.Modal(modalCreateElement);
                        modalCreate.show();
                    }
                }
            });
        </script>
    @endif
@endsection