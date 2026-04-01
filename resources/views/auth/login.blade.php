@extends('layouts.app')

@section('title', 'Login - VMS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="brand-icon">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <h1>Panel de Administración</h1>
                <p>Inventario de Autos</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf

                    <div class="input-wrapper">
                        <label for="correo" class="form-label">
                            <i class="bi bi-envelope-fill"></i>
                            Correo Electrónico
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="email" class="form-control" id="correo" name="correo" value="{{ old('correo') }}"
                                placeholder="usuario@ejemplo.com" required autofocus>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label for="contra" class="form-label">
                            <i class="bi bi-lock-fill"></i>
                            Contraseña
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-shield-lock-fill"></i>
                            </span>
                            <input type="password" class="form-control" id="contra" name="contra"
                                placeholder="Ingresa tu contraseña" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Iniciar Sesión
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <small>
                    <i class="bi bi-shield-check"></i>
                    Sistema Seguro de Gestión de Inventario
                </small>
            </div>
        </div>

        <div class="copyright">
            <small>© 2026 Sistema de Inventario de Autos · VMS</small>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>

@endsection
