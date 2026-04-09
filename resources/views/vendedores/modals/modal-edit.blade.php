@php
    // Usamos el ID de la sesión si hay un error de validación para recuperar al vendedor
    $sellerError = session('edit_seller_id') ? \App\Models\Seller::find(session('edit_seller_id')) : null;
@endphp

<div class="modal fade" id="modalEditarVendedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-inner">
                    <div class="modal-title-group">
                        <div class="modal-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <div class="modal-title-text">Editar Vendedor</div>
                            <div class="modal-subtitle-text">Actualiza la información del vendedor</div>
                        </div>
                    </div>
                    <button class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>

            {{-- La ruta se llena dinámicamente con JS, pero dejamos el fallback por si hay error de validación --}}
            <form action="{{ $sellerError ? route('vendedores.update', $sellerError->id) : '' }}" method="POST"
                id="formEditarVendedor" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="form-section-title">Información Personal</div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Nombre Completo <span class="required">*</span></label>
                                            <input type="text" name="name" id="edit_name"
                                                class="field-input @error('name') is-invalid @enderror"
                                                value="{{ old('name', $sellerError->name ?? '') }}" required>
                                            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Teléfono / WhatsApp <span class="required">*</span></label>
                                            <input type="text" name="phone" id="edit_phone"
                                                class="field-input @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $sellerError->phone ?? '') }}" required>
                                            @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Correo Electrónico</label>
                                            <input type="email" name="email" id="edit_email"
                                                class="field-input @error('email') is-invalid @enderror"
                                                value="{{ old('email', $sellerError->email ?? '') }}">
                                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="field-group">
                                            {{-- CAMBIO: name="contract_file" para que coincida con tu Service --}}
                                            <label class="field-label">Actualizar Contrato/ID</label>
                                            <input type="file" name="contract_file" class="field-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="form-section-title">Detalles y Seguimiento</div>
                                <div class="field-group mb-3">
                                    <label class="field-label">Notas Internas</label>
                                    <textarea name="notes" id="edit_notes" rows="8"
                                        class="field-input">{{ old('notes', $sellerError->notes ?? '') }}</textarea>
                                </div>

                                <div class="field-group">
                                    <label class="field-label">Archivo Actual</label>

                                    {{-- Este contenedor lo llena el JS al abrir el modal --}}
                                    <div id="preview-container-edit"
                                        class="d-flex align-items-center justify-content-center border rounded bg-light position-relative"
                                        style="min-height: 200px; overflow: hidden;">
                                        
                                        {{-- Fallback para errores de validación --}}
                                        @if($sellerError && $sellerError->contract_path)
                                            <button type="button" class="btn btn-danger btn-sm position-absolute"
                                                style="top: 10px; right: 10px; z-index: 10;"
                                                onclick="confirmDeleteContract({{ $sellerError->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <img src="{{ route('vendedores.archivo', $sellerError->id) }}"
                                                class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
                                                <p class="small mb-0">Sin archivo disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <div class="footer-actions">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-submit">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>