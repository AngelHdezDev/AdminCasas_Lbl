<div class="modal fade" id="modalNuevoVendedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-inner">
                    <div class="modal-title-group">
                        <div class="modal-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <div class="modal-title-text">Nuevo Vendedor</div>
                            <div class="modal-subtitle-text">Registra la información del vendedor</div>
                        </div>
                    </div>
                    <button class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>

            <form action="{{ route('vendedores.store') }}" method="POST" id="formVendedor"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="form-section-title">Información Personal</div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Nombre Completo <span
                                                    class="required">*</span></label>
                                            <input type="text" name="name" id="name" placeholder="Ej: Juan Pérez López"
                                                class="field-input @if(!session('edit_client_id')) @error('name') is-invalid @enderror @endif"
                                                value="@if(!session('edit_client_id')){{ old('name') }}@endif" required>
                                            @if(!session('edit_client_id'))
                                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Teléfono / WhatsApp <span
                                                    class="required">*</span></label>
                                            <input type="text" name="phone" id="phone" placeholder="Ej: 33 1234 5678"
                                                class="field-input @if(!session('edit_client_id')) @error('phone') is-invalid @enderror @endif"
                                                value="@if(!session('edit_client_id')){{ old('phone') }}@endif"
                                                required>
                                            @if(!session('edit_client_id'))
                                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Correo Electrónico</label>
                                            <input type="email" name="email" id="email" placeholder="ejemplo@correo.com"
                                                {{-- Cambié edit_client_id por edit_seller_id para que sea congruente
                                                con tu Request de Vendedores --}}
                                                class="field-input @if(!session('edit_seller_id')) @error('email') is-invalid @enderror @endif"
                                                value="@if(!session('edit_seller_id')){{ old('email') }}@endif">

                                            {{-- ESTO ES LO QUE FALTA: El contenedor del mensaje --}}
                                            @if(!session('edit_seller_id'))
                                                @error('email')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Campo para contrato --}}
                                    <div class="col-12">
                                        <div class="field-group">
                                            <label class="field-label">Contrato Oficial</label>
                                            <input type="file" name="contract_path" class="field-input" id="contract_path">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="form-section-title">Detalles y Seguimiento</div>
                                <div class="field-group" style="margin-bottom:0;">
                                    <label class="field-label">Notas Internas <span
                                            style="color:var(--gray-300);font-weight:400;">(Opcional)</span></label>
                                    <textarea name="notes" id="notes" rows="8"
                                        class="field-input @if(!session('edit_seller_id')) @error('notes') is-invalid @enderror @endif"
                                        placeholder="Información relevante...">@if(!session('edit_seller_id')){{ old('notes') }}@endif</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <span class="footer-note"><i class="bi bi-shield-check"></i> Datos protegidos</span>
                    <div class="footer-actions">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-person-plus-fill"></i> Guardar Vendedor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>