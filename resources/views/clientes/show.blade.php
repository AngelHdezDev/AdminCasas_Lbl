@extends('layouts.app') {{-- O tu layout base del CRM --}}

@section('title', 'Clientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/clientes-detail.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4 page-client-details">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('clientes.index') }}" class="btn-back-link text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Volver a clientes
            </a>
            <h2 class="h3 text-white mt-1 mb-0">{{ $client->name }}</h2>
            <span class="text-warning small"><i class="bi bi-person-badge"></i> Expediente del Cliente</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="details-card-panel h-100">
                <div class="panel-section-title">
                    <i class="bi bi-person-fill text-primary"></i> Información Personal
                </div>

                <div class="info-profile-group">
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($client->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="profile-label">Nombre Completo</div>
                        <div class="profile-value-highlight">{{ $client->name }}</div>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <div class="row g-3">
                    <div class="col-12">
                        <div class="data-field-box">
                            <span class="data-label"><i class="bi bi-whatsapp text-success"></i> Teléfono / WhatsApp</span>
                            <span class="data-value">
                                <a href="https://wa.me/{{ preg_replace('/\s+/', '', $client->phone) }}" target="_blank" class="text-white text-decoration-none">
                                    {{ $client->phone }} <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="data-field-box">
                            <span class="data-label"><i class="bi bi-envelope text-info"></i> Correo Electrónico</span>
                            <span class="data-value">
                                @if($client->email)
                                    <a href="mailto:{{ $client->email }}" class="text-white text-decoration-none">{{ $client->email }}</a>
                                @else
                                    <span class="text-muted italic">No registrado</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="data-field-box">
                            <span class="data-label"><i class="bi bi-calendar-check"></i> Fecha de Registro</span>
                            <span class="data-value">{{ $client->created_at->format('d/m/Y \a \l\a\s H:i a') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="d-flex flex-column gap-4 h-100">
                
                <div class="details-card-panel flex-grow-1">
                    <div class="panel-section-title">
                        <i class="bi bi-journal-text text-warning"></i> Notas Internas de Seguimiento
                    </div>
                    <div class="notes-content-display">
                        @if($client->notes)
                            {!! nl2br(e($client->notes)) !!}
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-chat-left-dots d-block fs-3 mb-2 opacity-50"></i>
                                <span class="small italic">Este cliente aún no cuenta con anotaciones o comentarios de seguimiento.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="details-card-panel">
                    <div class="panel-section-title d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-card-image text-danger"></i> Identificación Oficial</span>
                        @if($client->identification_path)
                            <a href="{{ route('clientes.archivo', $client->id) }}" target="_blank" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size:0.75rem;">
                                <i class="bi bi-fullscreen"></i> Ver Completa
                            </a>
                        @endif
                    </div>
                    
                    <div class="identification-display-area mt-3">
                        @if($client->identification_path)
                            <div class="id-document-frame">
                                <img src="{{ route('clientes.archivo', $client->id) }}" alt="Identificación Oficial de {{ $client->name }}">
                            </div>
                        @else
                            <div class="id-document-empty">
                                <i class="bi bi-shield-slash"></i>
                                <span>No se ha cargado una identificación oficial para este registro.</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection