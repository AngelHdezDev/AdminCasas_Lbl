@extends('layouts.app')

@section('title', 'Galería de Imágenes - CTP')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/galeria.css') }}">
    <style>
        /* ─── ESTILOS DE SELECCIÓN MASIVA (Estilo Google Photos) ─── */

        /* Barra flotante inferior */
        .bulk-actions-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 50px;
            padding: 12px 24px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .bulk-actions-bar.active {
            opacity: 1;
            visibility: visible;
            bottom: 30px;
        }

        .bulk-select-input {
            width: 220px;
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
        }

        .btn-bulk-submit {
            background-color: #c0392b;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-bulk-submit:hover {
            background-color: #a93226;
        }

        /* Contenedor circular del Checkbox */
        .image-checkbox-wrapper {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 25;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.25s ease;
            cursor: pointer;
            margin: 0;
        }

        /* Ocultar casilla por defecto (aparece en hover) */
        .gallery-item .image-checkbox-wrapper {
            opacity: 0;
            transform: scale(0.8);
        }

        /* Mostrar al pasar el mouse por encima o si ya está marcado */
        .gallery-item:hover .image-checkbox-wrapper,
        .image-checkbox-wrapper.has-checked {
            opacity: 1;
            transform: scale(1);
        }

        /* Checkbox nativo */
        .bulk-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #c0392b;
            border-radius: 4px;
            margin: 0;
        }

        /* Iluminación de tarjeta activa al seleccionarse */
        .gallery-item.selected-card {
            border: 2px solid #c0392b !important;
            box-shadow: 0 5px 15px rgba(192, 57, 43, 0.2) !important;
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')

    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="page-header-inner">
                <div>
                    <p class="page-eyebrow">Multimedia</p>
                    <h1 class="page-title">Galería de Imágenes</h1>
                    <p class="page-subtitle">
                        {{ $imagenes->total() }} imagenes sin asignar
                    </p>
                </div>
                <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Subir Imágenes
                </button>
            </div>
        </div>
    </div>

    <div class="filters-bar">
        <div class="container-fluid px-4">
            <div class="filters-inner">
                <span class="filters-count">
                    Mostrando <span id="countVisible">{{ count($imagenes ?? []) }}</span> imágenes
                </span>

                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnSelectAllPage">
                    <i class="bi bi-check2-all"></i> Seleccionar todas
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('galeria.asignarMasivo') }}" method="POST" id="formBulkAssign">
        @csrf

        <div class="bulk-actions-bar" id="bulkBar">
            <span class="text-dark small fw-bold" id="bulkCount">0 seleccionadas</span>
            <select class="bulk-select-input" name="property_id" required>
                <option value="">— Seleccionar Propiedad —</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->title }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-bulk-submit">Asignar lote</button>
            <button type="button" class="btn-bulk-submit btn-danger-bulk" id="btnBulkDelete" style="background-color: #7f8c8d;">
                <i class="bi bi-trash"></i> Eliminar lote
            </button>
        </div>

        <div class="main-wrapper">
            <div class="container-fluid px-4">

                @if(isset($imagenes) && count($imagenes) > 0)
                    <div class="gallery-grid" id="galleryGrid">
                        @foreach($imagenes as $imagen)
                            <div class="gallery-item" data-vehiculo="{{ $imagen->id_auto ?? 'sin-asignar' }}">
                                <div class="image-container">

                                    <label class="image-checkbox-wrapper">
                                        <input type="checkbox" name="imagenes_ids[]" value="{{ $imagen->id }}"
                                            class="bulk-checkbox">
                                    </label>

                                    <img src="{{ asset('storage/' . $imagen->ruta_archivo) }}" alt="{{ $imagen->nombre_original }}" loading="lazy">
                                    <div class="image-overlay"></div>

                                    @if($imagen->id_auto)
                                        <span class="status-badge assigned">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Asignada
                                        </span>
                                    @else
                                        <span class="status-badge unassigned">
                                            <i class="bi bi-exclamation-circle-fill"></i>
                                            Sin asignar
                                        </span>
                                    @endif

                                    <div class="image-actions">
                                        <button type="button" class="btn-image-action" title="Ver imagen"
                                            onclick="viewImage('{{ asset('storage/' . $imagen->ruta_archivo) }}')">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        
                                    </div>
                                </div>

                                <div class="gallery-body">
                                    <div class="image-info">
                                        <div class="image-name">
                                            <i class="bi bi-file-image"></i>
                                            Archivo
                                        </div>
                                        <div class="image-filename" title="{{ $imagen->nombre }}">
                                            {{ $imagen->nombre }}
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-images"></i>
                        </div>
                        <div class="empty-title">Sin imágenes en la galería</div>
                        <p class="empty-text">Sube las primeras imágenes para comenzar.</p>
                        <button type="button" class="btn-upload mx-auto" data-bs-toggle="modal" data-bs-target="#modalUpload">
                            <i class="bi bi-cloud-arrow-up"></i> Subir Imágenes
                        </button>
                    </div>
                @endif

            </div>

            @if($imagenes->hasPages() || $imagenes->total() > 0)
                <div class="pagination-wrapper">
                    <div class="w-100">
                        @if($imagenes->total() > 0)
                            <div class="pagination-info">
                                Mostrando <strong>{{ $imagenes->firstItem() }}</strong> a
                                <strong>{{ $imagenes->lastItem() }}</strong>
                                de <strong>{{ $imagenes->total() }}</strong> imágenes
                            </div>
                        @endif

                        <div class="d-flex justify-content-center">
                            {{ $imagenes->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>

    @if(isset($imagenes) && count($imagenes) > 0)
        @foreach($imagenes as $imagen)
            <form action="{{ route('galeria.destroy', $imagen->id) }}" method="POST" id="delete-form-{{ $imagen->id }}"
                class="d-none">
                @csrf
                @method('DELETE')
            </form>

            <form action="{{ route('galeria.asignar', $imagen->id) }}" method="POST" id="assign-individual-{{ $imagen->id }}"
                class="d-none">
                @csrf
                <input type="hidden" name="property_id" id="input-individual-{{ $imagen->id }}">
            </form>
        @endforeach
    @endif

    <div class="modal fade" id="modalUpload" tabindex="-1" aria-labelledby="modalUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalUploadLabel" style="color: var(--gray-800);">
                        <i class="bi bi-cloud-arrow-up me-2 text-gold"></i>Subir Multimedia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3">
                    <form action="{{ route('galeria.store') }}" method="POST" enctype="multipart/form-data" id="formUpload">
                        @csrf
                        <div class="upload-drop-zone" id="dropZone">
                            <div class="upload-icon">
                                <i class="bi bi-images"></i>
                            </div>
                            <div class="upload-text">
                                <p class="mb-1"><strong>Arrastra tus fotos aquí</strong></p>
                                <p class="text-muted small">o haz clic para explorar archivos</p>
                            </div>
                            <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*" class="d-none">
                        </div>

                        <div id="previewContainer" class="mt-3" style="display: none;">
                            <p class="text-muted small mb-2 fw-medium">Archivos listos para subir:</p>
                            <div id="fileList" class="file-list-scroll" style="max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn-upload w-100 py-2" id="btnUpload" disabled>
                                <i class="bi bi-upload"></i> Subir Imágenes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/galeria.js') }}"></script>

   <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.bulk-checkbox');
            const bulkBar = document.getElementById('bulkBar');
            const bulkCount = document.getElementById('bulkCount');
            const btnSelectAllPage = document.getElementById('btnSelectAllPage');
            
            // Formulario maestro y sus elementos
            const formMaster = document.getElementById('formBulkAssign');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const selectProp = formMaster ? formMaster.querySelector('.bulk-select-input') : null;

            // Guardamos el action original del form (asignar-masivo) para poder alternar sin problemas
            const originalAction = formMaster ? formMaster.action : '';

            // Actualiza el contador y activa/desactiva la barra inferior
            function updateBulkBar() {
                const checkedCount = document.querySelectorAll('.bulk-checkbox:checked').length;
                bulkCount.textContent = `${checkedCount} seleccionadas`;

                if (checkedCount > 0) {
                    bulkBar.classList.add('active');
                } else {
                    bulkBar.classList.remove('active');
                }
            }

            // Listeners individuales para checkboxes
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const wrapper = this.closest('.image-checkbox-wrapper');
                    const card = this.closest('.gallery-item');

                    if (this.checked) {
                        wrapper.classList.add('has-checked');
                        card.classList.add('selected-card');
                    } else {
                        wrapper.classList.remove('has-checked');
                        card.classList.remove('selected-card');
                    }
                    updateBulkBar();
                });
            });

            // Botón superior para "Seleccionar todas / Deseleccionar todas"
            btnSelectAllPage.addEventListener('click', function () {
                const totalChecked = document.querySelectorAll('.bulk-checkbox:checked').length;
                const totalCheckboxes = checkboxes.length;
                const shouldCheck = totalChecked !== totalCheckboxes;

                checkboxes.forEach(cb => {
                    cb.checked = shouldCheck;
                    const wrapper = cb.closest('.image-checkbox-wrapper');
                    const card = cb.closest('.gallery-item');

                    if (shouldCheck) {
                        wrapper.classList.add('has-checked');
                        card.classList.add('selected-card');
                    } else {
                        wrapper.classList.remove('has-checked');
                        card.classList.remove('selected-card');
                    }
                });

                this.innerHTML = shouldCheck
                    ? '<i class="bi bi-dash-circle"></i> Deseleccionar todas'
                    : '<i class="bi bi-check2-all"></i> Seleccionar todas';

                updateBulkBar();
            });

            // Manejo del click en "Eliminar lote"
            if (btnBulkDelete) {
                btnBulkDelete.addEventListener('click', function () {
                    const checkedCount = document.querySelectorAll('.bulk-checkbox:checked').length;

                    if (checkedCount === 0) {
                        Swal.fire({ 
                            title: 'Atención', 
                            text: 'Por favor, selecciona al menos una imagen.', 
                            icon: 'warning',
                            confirmButtonColor: '#c0392b'
                        });
                        return;
                    }

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `Vas a eliminar permanentemente ${checkedCount} imágenes del servidor.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#c0392b',
                        cancelButtonColor: '#7f8c8d',
                        confirmButtonText: 'Sí, eliminar lote',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // 1. Apuntamos el formulario a la ruta de borrado masivo
                            formMaster.action = "{{ route('galeria.destroy-masivo') }}";
                            
                            // 2. Quitamos el required del select para que no bloquee el submit
                            if (selectProp) selectProp.removeAttribute('required');
                            
                            // 3. Enviamos
                            formMaster.submit();
                        }
                    });
                });
            }

            // Si se hace un submit normal al formulario (Asignar lote), nos aseguramos de restaurar el action y el required
            if (formMaster) {
                formMaster.addEventListener('submit', function(e) {
                    // Solo si el submit NO vino provocado por el botón de borrar
                    if (formMaster.action !== "{{ route('galeria.destroy-masivo') }}") {
                        formMaster.action = originalAction;
                        if (selectProp) selectProp.setAttribute('required', 'required');
                    }
                });
            }
        });

        // Dispara el envío del formulario oculto individual rápido
        function submitIndividualAssign(selectElement, imagenId) {
            const targetForm = document.getElementById(`assign-individual-${imagenId}`);
            const targetInput = document.getElementById(`input-individual-${imagenId}`);
            targetInput.value = selectElement.value;
            targetForm.submit();
        }

        // Dispara la eliminación individual
        function executeIndividualDelete(imagenId) {
            if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
                document.getElementById(`delete-form-${imagenId}`).submit();
            }
        }
    </script>

    {{-- Notificaciones SweetAlert --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({ title: '¡Hecho!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#c0392b' });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({ title: 'Hubo un problema', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#c0392b' });
            });
        </script>
    @endif

@endsection