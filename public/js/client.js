document.addEventListener('DOMContentLoaded', function () {
    const modalEditar = document.getElementById('modalEditarCliente');
    const formEditar  = document.getElementById('formEditarCliente');
    const modalNuevo  = document.getElementById('modalNuevoCliente');

    // ── MODAL EDITAR: poblar al abrir ──
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            // Si fue abierto por el JS de errores (sin relatedTarget), no repoblar
            if (!event.relatedTarget) return;

            const button = event.relatedTarget;

            formEditar.action = `/clientes/${button.getAttribute('data-id')}`;

            document.getElementById('edit_name').value  = button.getAttribute('data-name')  || '';
            document.getElementById('edit_email').value = button.getAttribute('data-email') || '';
            document.getElementById('edit_phone').value = button.getAttribute('data-phone') || '';
            document.getElementById('edit_notes').value = button.getAttribute('data-notes') || '';

            // Limpiar errores visuales de sesiones anteriores
            formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formEditar.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        });

        // ── MODAL EDITAR: limpiar al cerrar ──
        modalEditar.addEventListener('hidden.bs.modal', function () {
            formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formEditar.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
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