document.addEventListener('DOMContentLoaded', function() {
    const modalPropiedad = document.getElementById('modalPropiedad');
    const formPropiedad = document.getElementById('formPropiedad');

    if (modalPropiedad) {
        modalPropiedad.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const btnText = document.getElementById('btnSubmitText');
            const btnIcon = document.getElementById('btnSubmitIcon');

            if (id) {
                // MODO EDICIÓN
                modalTitle.textContent = 'Editar Propiedad';
                btnText.textContent = 'Guardar Cambios';
                btnIcon.className = 'bi bi-check-lg';
                methodField.value = 'PUT';
                formPropiedad.action = `/propiedades/${id}`;

                // Campos de texto y números
                const fields = [
                    'title', 'neighborhood', 'address', 'm2_land',
                    'm2_construction', 'bedrooms', 'bathrooms',
                    'parking_spots', 'price', 'description',
                    'city', 'state' // NUEVOS
                ];

                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                // Selects
                const selects = ['type', 'contract_type', 'seller_id', 'client_id']; // NUEVOS
                selects.forEach(field => {
                    const select = document.getElementById(field);
                    if (select) {
                        select.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                // Checkboxes
                document.getElementById('is_featured').checked = button.getAttribute('data-is_featured') == '1';
                document.getElementById('show_address').checked = button.getAttribute('data-show_public_address') == '1'; // CORREGIDO

            } else {
                // MODO CREACIÓN
                modalTitle.textContent = 'Nueva Propiedad';
                btnText.textContent = 'Registrar Propiedad';
                btnIcon.className = 'bi bi-plus-lg';
                formPropiedad.reset();

                // Valores por defecto para creación
                document.getElementById('show_address').checked = true;
                document.getElementById('is_featured').checked = false;
            }
        });
    }

    // SweetAlert para eliminar
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.classList.contains('form-eliminar')) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "La propiedad se eliminará permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }
    });
});