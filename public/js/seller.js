document.addEventListener('DOMContentLoaded', function () {
    const modalEditar = document.getElementById('modalEditarVendedor');
    const formEditar = document.getElementById('formEditarVendedor');
    const modalNuevo = document.getElementById('modalNuevoVendedor');

    // ── MODAL EDITAR: Poblar datos al abrir ──
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            // Si el modal se abre por error de validación (sin botón disparador), no hacemos nada
            if (!event.relatedTarget) return;

            const button = event.relatedTarget;
            
            // 1. Extraer información de los atributos data- del botón
            const sellerId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const notes = button.getAttribute('data-notes');
            const hasFile = button.getAttribute('data-contract');

            // 2. Actualizar la acción del formulario con el ID del vendedor
            formEditar.action = `/vendedores/${sellerId}`;

            // 3. Llenar los campos del formulario
            // IMPORTANTE: Los IDs deben coincidir con tu HTML
            if(document.getElementById('edit_name')) document.getElementById('edit_name').value = name || '';
            if(document.getElementById('edit_email')) document.getElementById('edit_email').value = email || '';
            if(document.getElementById('edit_phone')) document.getElementById('edit_phone').value = phone || '';
            if(document.getElementById('edit_notes')) document.getElementById('edit_notes').value = notes || '';

            // 4. Lógica para la Previsualización del Archivo (Contrato)
            const previewContainer = document.getElementById('preview-container-edit');
            if (previewContainer) {
                if (hasFile && hasFile !== '' && hasFile !== 'null') {
                    // Si tiene archivo, mostramos botón de borrar y link de vista previa
                    previewContainer.innerHTML = `
                        <button type="button" 
                                class="btn btn-danger btn-sm position-absolute" 
                                style="top: 10px; right: 10px; z-index: 10;"
                                onclick="confirmDeleteContract(${sellerId})">
                            <i class="bi bi-trash"></i>
                        </button>
                        
                        <img src="/vendedores/archivo/${sellerId}" 
                             class="img-fluid rounded" 
                             style="max-height: 180px; object-fit: contain;">`;
                } else {
                    // Estado vacío
                    previewContainer.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="bi bi-file-earmark-x" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="small mb-0">Sin archivo adjunto</p>
                        </div>`;
                }
            }

            // Limpiar estilos de error de validaciones previas
            formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formEditar.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        });

        // Limpiar previsualización al cerrar para evitar "flashes" de datos anteriores
        modalEditar.addEventListener('hidden.bs.modal', function () {
            const previewContainer = document.getElementById('preview-container-edit');
            if (previewContainer) previewContainer.innerHTML = '';
        });
    }

    // ── MODAL NUEVO: Limpiar formulario al cerrar ──
    if (modalNuevo) {
        modalNuevo.addEventListener('hidden.bs.modal', function () {
            const formNuevo = document.getElementById('formNuevoVendedor');
            if (formNuevo) {
                formNuevo.reset();
                formNuevo.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            }
        });
    }

    // ── SWEETALERT: Confirmación para Eliminar Vendedor ──
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('form-eliminar-vendedor')) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                title: '¿Eliminar vendedor?',
                text: "Se perderá el historial de contacto y notas de este vendedor.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }
    });
});

// ── FUNCIÓN: Confirmación para Eliminar solo el Archivo ──
function confirmDeleteContract(sellerId) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: "El archivo se borrará permanentemente del servidor.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Creamos formulario dinámico para enviar DELETE a la ruta puente
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/vendedores/${sellerId}/archivo`; 
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}