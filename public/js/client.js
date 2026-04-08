document.addEventListener('DOMContentLoaded', function () {
    const modalEditar = document.getElementById('modalEditarCliente');
    const formEditar = document.getElementById('formEditarCliente');
    const modalNuevo = document.getElementById('modalNuevoCliente');

    // ── MODAL EDITAR: poblar al abrir ──
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            // Si fue abierto por el JS de errores (sin relatedTarget), no repoblar
            if (!event.relatedTarget) return;

            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-id'); // Obtenemos el ID
            const hasFile = button.getAttribute('data-identification'); // Obtenemos si tiene archivo

            formEditar.action = `/clientes/${clientId}`;

            document.getElementById('edit_name').value = button.getAttribute('data-name') || '';
            document.getElementById('edit_email').value = button.getAttribute('data-email') || '';
            document.getElementById('edit_phone').value = button.getAttribute('data-phone') || '';
            document.getElementById('edit_notes').value = button.getAttribute('data-notes') || '';

            // ── Lógica para la Previsualización de Imagen ──
            const previewContainer = document.getElementById('preview-container-edit');
            if (previewContainer) {
                console.log("Tiene archivo de identificación:", hasFile);
                if (hasFile && hasFile !== '') {
                    // Si tiene archivo, inyectamos la imagen usando la ruta segura
                    previewContainer.innerHTML = `
                    <button type="button" 
                            class="btn btn-danger btn-sm position-absolute" 
                            style="top: 10px; right: 10px; z-index: 10;"
                            onclick="confirmDeleteFile(${clientId})">
                        <i class="bi bi-trash"></i>
                    </button>
                    <img src="/clientes/archivo/${clientId}" 
                             class="img-fluid rounded" 
                             style="max-height: 180px; object-fit: contain;">`;
                } else {
                    // Si no tiene, mostramos el estado vacío
                    previewContainer.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="small mb-0">Sin vista previa disponible</p>
                        </div>`;
                }
            }

            // Limpiar errores visuales de sesiones anteriores
            formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formEditar.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        });

        // ── MODAL EDITAR: limpiar al cerrar ──
        modalEditar.addEventListener('hidden.bs.modal', function () {
            formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formEditar.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

            // Opcional: Limpiar la imagen al cerrar para que no se vea el flash de la anterior al abrir otro
            const previewContainer = document.getElementById('preview-container-edit');
            if (previewContainer) previewContainer.innerHTML = '';
        });
    }

    // ── MODAL NUEVO: limpiar al cerrar ──
    if (modalNuevo) {
        modalNuevo.addEventListener('hidden.bs.modal', function () {
            const formNuevo = document.getElementById('formCliente');
            formNuevo.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formNuevo.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        });
    }

    // ── SWEETALERT ELIMINAR ──
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('form-eliminar')) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                title: '¿Eliminar cliente?',
                text: "Se perderá el historial de contacto y notas.",
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

function confirmDeleteFile(clientId) {
    Swal.fire({
        title: '¿Eliminar identificación?',
        text: "Se borrará el archivo físico del servidor permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Creamos un formulario dinámico para enviar la petición DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            // Esta ruta debe coincidir con la que definas en web.php
            form.action = `/clientes/${clientId}/archivo`; 
            
            // Necesitamos el token CSRF y el método DELETE para Laravel
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