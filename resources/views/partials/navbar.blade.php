<nav class="navbar navbar-expand-lg navbar-vms">
    <div class="container-fluid px-4">
        <a class="navbar-brand-vms" href="{{ route('dashboard') }}">
            <div class="brand-icon"><i class="bi bi-car-front-fill"></i></div>
            VMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link-vms {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-vms {{ request()->routeIs('propiedades.*') ? 'active' : '' }}"
                        href="{{ route('propiedades.index') }}">
                        <i class="bi bi-car-front me-1"></i>Propiedades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-vms {{ request()->routeIs('marcas.*') ? 'active' : '' }}"
                        href="{{ route('marcas.index') }}">
                        <i class="bi bi-tag me-1"></i> Marcas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-vms {{ request()->routeIs('galeria.*') ? 'active' : '' }}"
                        href="{{ route('galeria.index') }}">
                        <i class="bi bi-card-image me-1"></i> Galería
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="#" class="user-pill">
                    <i class="bi bi-person-circle"></i>
                    {{ Auth::user()->nombre ?? 'Usuario' }}
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>