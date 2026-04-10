@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard2.css') }}">
@endpush

@section('content')

    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div class="container-fluid px-4">
            <p class="page-eyebrow">Sistema de Gestión</p>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                Resumen general de propiedades y operaciones
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
                            <i class="bi bi-house-fill"></i>
                        </div>
                    </div>
                    <div class="stat-label">Total Propiedades</div>
                    <div class="stat-value">{{ $totalPropiedades ?? 0 }}</div>
                    <div class="stat-change {{ ($diffPropiedades ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi {{ ($diffPropiedades ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                        {{ ($diffPropiedades ?? 0) >= 0 ? '+' : '' }}{{ number_format($diffPropiedades ?? 0, 1) }}% este mes
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                    <div class="stat-label">Valor en Venta</div>
                    <div class="stat-value">${{ number_format($valorVenta ?? 0, 0) }}</div>
                    <div class="stat-change {{ ($diffValor ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi {{ ($diffValor ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                        {{ ($diffValor ?? 0) >= 0 ? '+' : '' }}{{ number_format($diffValor ?? 0, 1) }}% trimestre
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-key-fill"></i>
                        </div>
                    </div>
                    <div class="stat-label">En Renta</div>
                    <div class="stat-value">{{ $totalRenta ?? 0 }}</div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i>
                        +{{ $rentaNuevas ?? 0 }} nuevas
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="bi bi-bookmark-star-fill"></i>
                        </div>
                    </div>
                    <div class="stat-label">Destacadas</div>
                    <div class="stat-value">{{ $totalDestacadas ?? 0 }}</div>
                    <div class="stat-change {{ ($destacadasVencidas ?? 0) > 0 ? 'negative' : 'positive' }}">
                        <i class="bi {{ ($destacadasVencidas ?? 0) > 0 ? 'bi-arrow-down' : 'bi-check-circle' }}"></i>
                        {{ ($destacadasVencidas ?? 0) > 0 ? '-' . $destacadasVencidas . ' vencidas' : 'Al día' }}
                    </div>
                </div>
            </div>

            <!-- KPI Row -->
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-icon kpi-gold">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Agentes Activos</div>
                        <div class="kpi-value">{{ $totalSellers ?? 0 }}</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon kpi-blue">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Clientes Registrados</div>
                        <div class="kpi-value">{{ $totalClients ?? 0 }}</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon kpi-green">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Propiedades Activas</div>
                        <div class="kpi-value">{{ $propiedadesActivas ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">

                <!-- Recent Properties -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="bi bi-clock-history"></i>
                            Propiedades Recientes
                        </h2>
                        <a href="{{ route('propiedades.index') }}" class="card-link">
                            Ver todas →
                        </a>
                    </div>
                    <div class="card-body">
                        @if(isset($propiedadesRecientes) && count($propiedadesRecientes) > 0)
                            @foreach($propiedadesRecientes as $propiedad)
                                <div class="prop-list-item">                                 
                                    <div class="prop-thumb">
                                        {{--
                                        Cambiamos la lógica temporalmente:
                                        Como no hay relación, siempre mostrará el icono de la casa
                                        --}}
                                        <i class="bi bi-house"></i>
                                    </div>
                                    <div class="prop-info">
                                        <div class="prop-name">{{ $propiedad->title }}</div>
                                        <div class="prop-meta">
                                            @if($propiedad->bedrooms)
                                                <span><i class="bi bi-door-closed"></i> {{ $propiedad->bedrooms }} rec</span>
                                            @endif
                                            @if($propiedad->bathrooms)
                                                <span><i class="bi bi-droplet"></i> {{ $propiedad->bathrooms }} baños</span>
                                            @endif
                                            @if($propiedad->m2_construction)
                                                <span><i class="bi bi-rulers"></i> {{ $propiedad->m2_construction }} m²</span>
                                            @endif
                                            @if($propiedad->neighborhood)
                                                <span>· {{ $propiedad->neighborhood }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="prop-right">
                                        <div class="prop-price">${{ number_format($propiedad->price, 0) }}</div>
                                        <span class="prop-badge badge-{{ $propiedad->contract_type }}">
                                            {{ ucfirst($propiedad->contract_type) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-house"></i>
                                </div>
                                <p class="empty-text">No hay propiedades registradas</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Top Sellers -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="bi bi-trophy"></i>
                            Top Agentes
                        </h2>
                        <a href="{{ route('vendedores.index') }}" class="card-link">
                            Ver todos →
                        </a>
                    </div>
                    <div class="card-body">
                        @if(isset($agentesTop) && count($agentesTop) > 0)
                            @foreach($agentesTop as $agente)
                                <div class="agent-list-item">
                                    <div class="agent-avatar">
                                        {{ strtoupper(substr($agente->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $agente->name)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div class="agent-info">
                                        <div class="agent-name">{{ $agente->name }}</div>
                                        <div class="agent-count">{{ $agente->properties_count ?? 0 }} propiedades</div>
                                    </div>
                                    <div class="agent-value">${{ number_format($agente->properties_sum_price ?? 0, 0) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <p class="empty-text">No hay agentes registrados</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Activity Timeline -->
            <!-- <div class="content-card">
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
                                    <i class="{{ $actividad->icono }}"></i>
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
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <p class="empty-text">No hay actividades registradas</p>
                        </div>
                    @endif
                </div>
            </div> -->

        </div>
    </div>
@endsection