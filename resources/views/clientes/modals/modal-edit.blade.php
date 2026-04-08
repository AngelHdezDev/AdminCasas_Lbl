@php
    $clientError = session('edit_client_id') ? \App\Models\Client::find(session('edit_client_id')) : null;
@endphp

<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-inner">
                    <div class="modal-title-group">
                        <div class="modal-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <div class="modal-title-text">Editar Cliente</div>
                            <div class="modal-subtitle-text">Actualiza la información del cliente</div>
                        </div>
                    </div>
                    <button class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>

            <form action="{{ $clientError ? route('clientes.update', $clientError->id) : '' }}" 
                  method="POST" id="formEditarCliente" enctype="multipart/form-data">
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
                                                   value="{{ old('name', $clientError->name ?? '') }}" required>
                                            @error('name') <div class="invalid-feedback" style="display:block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Teléfono / WhatsApp <span class="required">*</span></label>
                                            <input type="text" name="phone" id="edit_phone" 
                                                   class="field-input @error('phone') is-invalid @enderror" 
                                                   value="{{ old('phone', $clientError->phone ?? '') }}" required>
                                            @error('phone') <div class="invalid-feedback" style="display:block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Correo Electrónico</label>
                                            <input type="email" name="email" id="edit_email" 
                                                   class="field-input @error('email') is-invalid @enderror" 
                                                   value="{{ old('email', $clientError->email ?? '') }}">
                                            @error('email') <div class="invalid-feedback" style="display:block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Actualizar Identificación</label>
                                            <input type="file" name="identification_path" class="field-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="form-section-title">Detalles y Seguimiento</div>
                                <div class="field-group">
                                    <label class="field-label">Notas Internas</label>
                                    <textarea name="notes" id="edit_notes" rows="8" class="field-input">{{ old('notes', $clientError->notes ?? '') }}</textarea>
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