document.addEventListener('DOMContentLoaded', function() {
    const modalVehiculo = document.getElementById('modalNuevoVehiculo');
    const formVehiculo = document.getElementById('formVehiculo');

    if (modalVehiculo) {
        modalVehiculo.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const autoId = button.getAttribute('data-id');

            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const btnText = document.getElementById('btnSubmitText');
            const btnIcon = document.getElementById('btnSubmitIcon');

            if (autoId) {
                // Modo edición
                modalTitle.textContent = 'Editar Vehículo';
                btnText.textContent = 'Guardar Cambios';
                btnIcon.className = 'bi bi-check-lg';
                methodField.value = 'PUT';
                formVehiculo.action = `/autos/${autoId}`;

                // Array de campos que son inputs/textareas normales
                const textFields = ['modelo', 'year', 'color', 'kilometraje', 'precio', 'descripcion'];

                // Procesar campos de texto normales
                textFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                // Procesar selects (campos que usan <select>)
                const selectFields = ['id_marca', 'tipo', 'transmision', 'combustible'];
                
                selectFields.forEach(field => {
                    const select = document.getElementById(field);
                    if (select) {
                        const value = button.getAttribute(`data-${field}`);
                        if (value) {
                            select.value = value;
                            select.dispatchEvent(new Event('change'));
                        }
                    }
                });

                // Checkboxes
                document.getElementById('ocultar_kilometraje').checked = button.getAttribute('data-ocultar') === '1';
                document.getElementById('consignacion').checked = button.getAttribute('data-consignacion') === '1';

            } else {
                // Modo creación
                modalTitle.textContent = 'Nueva Propiedad';
                btnText.textContent = 'Registrar Propiedad';
                btnIcon.className = 'bi bi-plus-lg';
                methodField.value = 'POST';
                formVehiculo.action = "/autos";
                formVehiculo.reset();
            }
        });
    }

    // Resto de tu código (búsqueda, eliminación)...
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    if (searchInput && filterForm) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }

    document.addEventListener('submit', function(e) {
        if (e.target && e.target.classList.contains('form-eliminar')) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El vehículo se eliminará permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }
    });
});