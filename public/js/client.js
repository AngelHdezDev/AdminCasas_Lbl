document.addEventListener('DOMContentLoaded', function () {
    const modalCliente = document.getElementById('modalNuevoCliente');
    const formCliente = document.getElementById('formCliente');

    if (modalCliente) {
        modalCliente.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Botón que activó el modal
            const id = button.getAttribute('data-id');

            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const btnText = document.getElementById('btnSubmitText');
            const btnIcon = document.getElementById('btnSubmitIcon');

            if (id) {
                // MODO EDICIÓN
                modalTitle.textContent = 'Editar Cliente';
                btnText.textContent = 'Guardar Cambios';
                btnIcon.className = 'bi bi-check-lg';
                methodField.value = 'PUT';
                formCliente.action = `/clientes/${id}`;

                // Campos que coinciden con tu DB: id, name, email, phone, notes
                const fields = ['name', 'email', 'phone', 'notes'];

                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        // Extraemos el valor del atributo data- del botón
                        input.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

            } else {
                // MODO CREACIÓN
                modalTitle.textContent = 'Nuevo Cliente';
                btnText.textContent = 'Guardar Cliente';
                btnIcon.className = 'bi bi-person-plus-fill';
                formCliente.action = "/clientes";
                document.getElementById('methodField').value = 'POST';
                formCliente.reset();
                const fields = ['name', 'email', 'phone', 'notes'];
                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = "";
                        input.classList.remove('is-invalid'); 
                    }
                });
                const errorMessages = formCliente.querySelectorAll('.invalid-feedback');
                errorMessages.forEach(msg => msg.remove());
            }
        });
    }

    // SweetAlert para eliminar clientes
    window.confirmDelete = function (id) {
        Swal.fire({
            title: '¿Eliminar cliente?',
            text: "Se perderá el historial de contacto y notas de este propietario.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear un form dinámico para enviar el DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/clients/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    };
});