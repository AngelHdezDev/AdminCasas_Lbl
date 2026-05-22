@extends('layouts.app') {{-- Reemplaza por tu layout base si es diferente ── --}}

@section('title', 'Vendedores')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vendedores-detail.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4 page-seller-details">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('vendedores.index') }}" class="btn-back-link text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Volver a vendedores
            </a>
            <h2 class="h3 text-white mt-1 mb-0">{{ $seller->name }}</h2>
            <span class="text-warning small"><i class="bi bi-person-badge"></i> Expediente del Vendedor</span>
        </div>
        <div>
            <button class="btn btn-warning px-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalEditarVendedor">
                <i class="bi bi-pencil-square"></i> Editar Vendedor
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="details-card-panel h-100">
                <div class="panel-section-title">
                    <i class="bi bi-person-fill text-primary"></i> Información del Vendedor
                </div>

                <div class="info-profile-group">
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($seller->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="profile-label">Nombre Completo</div>
                        <div class="profile-value-highlight">{{ $seller->name }}</div>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <div class="row g-3">
                    <div class="col-12">
                        <div class="data-field-box">
                            <span class="data-label"><i class="bi bi-whatsapp text-success"></i> Teléfono / WhatsApp</span>
                            <span class="data-value">
                                <a href="https://wa.me/{{ preg_replace('/\s+/', '', $seller->phone) }}" target="_blank" class="text-white text-decoration-none">
                                    {{ $seller->phone }} <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="data-field-box">
                            <span class="data-label"><i class="bi bi-envelope text-info"></i> Correo Electrónico</span>
                            <span class="data-value">
                                @if($seller->email)
                                    <a href="mailto:{{ $seller->email }}" class="text-white text-decoration-none">{{ $seller->email }}</a>
                                @else
                                    <span class="text-muted italic">No registrado</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="data-field-box">
                            <span class="data-label"><i class="bi bi-calendar-check"></i> Fecha de Alta</span>
                            <span class="data-value">{{ $seller->created_at->format('d/m/Y \a \l\a\s H:i a') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="d-flex flex-column gap-4 h-100">
                
                <div class="details-card-panel flex-grow-1">
                    <div class="panel-section-title">
                        <i class="bi bi-journal-text text-warning"></i> Notas Internas / Bitácora
                    </div>
                    <div class="notes-content-display">
                        @if($seller->notes)
                            {!! nl2br(e($seller->notes)) !!}
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-chat-left-dots d-block fs-3 mb-2 opacity-50"></i>
                                <span class="small italic">Este de vendedor no cuenta con anotaciones registradas.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="details-card-panel">
                    <div class="panel-section-title d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-card-image text-danger"></i> Identificación Oficial</span>
                        @if($seller->contract_path)
                            <a href="{{ route('vendedores.archivo', $seller->id) }}" target="_blank" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size:0.75rem;">
                                <i class="bi bi-fullscreen"></i> Ver Completa
                            </a>
                        @endif
                    </div>
                    
                    <div class="identification-display-area mt-3">
                        @if($seller->contract_path)
                            <div class="id-document-frame">
                                <img src="{{ route('vendedores.archivo', $seller->id) }}" alt="Contrato de {{ $seller->name }}">
                            </div>
                        @else
                            <div class="id-document-empty">
                                <i class="bi bi-shield-slash"></i>
                                <span>No se ha cargado una identificación oficial para este vendedor.</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection