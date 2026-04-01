@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')


    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div class="container-fluid px-4">
            <p class="page-eyebrow">Sistema de Gestión</p>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                Resumen general del inventario y operaciones
            </p>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-wrapper">
        <div class="container-fluid px-4">

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                    </div>
                    <div class="stat-label">Total Vehículos</div>
                    <div class="stat-value">{{ $totalVehiculos ?? 0 }}</div>
                    <div class="stat-change {{ $diffVehiculos >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi {{ $diffVehiculos >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                        {{ $diffVehiculos >= 0 ? '+' : '' }}{{ number_format($diffVehiculos, 1) }}% este mes
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                    <div class="stat-label">Valor Inventario</div>
                    <div class="stat-value">${{ number_format($valorInventario ?? 0, 0) }}</div>
                    <div class="stat-change {{ $diffInventario >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi {{ $diffInventario >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                        {{ $diffInventario >= 0 ? '+' : '' }}{{ number_format($diffInventario, 1) }}% trimestre
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-bookmark-star-fill"></i>
                        </div>
                    </div>
                    <div class="stat-label">En Consignación</div>
                    <div class="stat-value">{{ $totalConsignacion ?? 0 }}</div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i>
                        +{{ $consignacionNuevos ?? 0 }} nuevos
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                    </div>
                    <div class="stat-label">Marcas Activas</div>
                    <div class="stat-value">{{ $totalMarcas ?? 0 }}</div>
                    <div class="stat-change {{ $marcasDescontinuadas > 0 ? 'negative' : 'positive' }}">
                        <i class="bi {{ $marcasDescontinuadas > 0 ? 'bi-arrow-down' : 'bi-check-circle' }}"></i>
                        {{ $marcasDescontinuadas > 0 ? '-' : '' }}{{ $marcasDescontinuadas ?? 0 }} descontinuadas
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">

                <!-- Recent Vehicles -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="bi bi-clock-history"></i>
                            Vehículos Recientes
                        </h2>
                        <a href="{{ route('autos.index') }}" class="card-link">
                            Ver todos →
                        </a>
                    </div>
                    <div class="card-body">
                        @if(isset($vehiculosRecientes) && count($vehiculosRecientes) > 0)
                            @foreach($vehiculosRecientes as $vehiculo)
                                <div class="vehicle-list-item">
                                    <div class="vehicle-thumb">
                                        <div class="vehicle-thumb">
                                            {{-- Preguntamos por la relación que acabas de crear --}}
                                            @if($vehiculo->thumbnail)
                                                <img src="{{ asset('storage/' . $vehiculo->thumbnail->imagen) }}" alt="">
                                            @else
                                                {{-- Si el auto no tiene ninguna imagen con thumbnail = 1 --}}
                                                <i class="bi bi-car-front"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="vehicle-info">
                                        <div class="vehicle-name">{{ $vehiculo->marca->nombre ?? '' }} {{ $vehiculo->modelo }}
                                        </div>
                                        <div class="vehicle-meta">{{ $vehiculo->year }} · {{ $vehiculo->color }} ·
                                            {{ $vehiculo->tipo }}
                                        </div>
                                    </div>
                                    <div class="vehicle-price">${{ number_format($vehiculo->precio, 0) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-car-front"></i>
                                </div>
                                <p class="empty-text">No hay vehículos registrados</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Top Brands -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="bi bi-trophy"></i>
                            Top Marcas por su valor
                        </h2>
                        <a href="{{ route('marcas.index') }}" class="card-link">
                            Ver todas →
                        </a>
                    </div>
                    <div class="card-body">
                        @if(isset($marcasTop) && count($marcasTop) > 0)
                            @foreach($marcasTop as $marca)
                                <div class="brand-list-item">
                                    <div class="brand-logo-small">
                                        @if($marca->imagen)
                                            <img src="{{ asset('storage/' . $marca->imagen) }}" width="50">
                                        @else
                                            {{ strtoupper(substr($marca->nombre, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="brand-info">
                                        <div class="brand-name">{{ $marca->nombre }}</div>
                                        <div class="brand-count">{{ $marca->autos_count ?? 0}} vehículos</div>
                                    </div>
                                    <div class="brand-value">${{ number_format($marca->autos_sum_precio ?? 0, 0) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-tag"></i>
                                </div>
                                <p class="empty-text">No hay marcas registradas</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Activity Timeline -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="bi bi-activity"></i>
                        Actividad Reciente
                    </h2>
                </div>
                <div class="card-body">
                    @if(isset($actividades) && count($actividades) > 0)
                        @foreach($actividades as $actividad)
                            <div class="activity-item">
                                <div class="activity-icon-wrapper">
                                    <i class="{{ $actividad->icono}}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $actividad->titulo }}</div>
                                    <div class="activity-desc">{{ $actividad->descripcion }}</div>
                                </div>
                                <div class="activity-time">{{ $actividad->tiempo }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-tag"></i>
                            </div>
                            <p class="empty-text">No hay actividades registradas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection