document.addEventListener('DOMContentLoaded', function () {
    const modalPropiedad = document.getElementById('modalPropiedad');
    const formPropiedad = document.getElementById('formPropiedad');
    const cpInput = document.getElementById('cp'); // Asegúrate que tu input tenga id="cp"

    if (modalPropiedad) {
        modalPropiedad.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button ? button.getAttribute('data-id') : null;

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

                const fields = [
                    'title', 'neighborhood', 'address', 'm2_land',
                    'm2_construction', 'bedrooms', 'bathrooms',
                    'parking_spots', 'price', 'description',
                    'city', 'state', 'cp' // Añadido CP aquí también
                ];

                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                const selects = ['type', 'contract_type', 'seller_id', 'client_id'];
                selects.forEach(field => {
                    const select = document.getElementById(field);
                    if (select) {
                        select.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                document.getElementById('is_featured').checked = button.getAttribute('data-is_featured') == '1';
                document.getElementById('show_address').checked = button.getAttribute('data-show_public_address') == '1';

            } else {
                // MODO CREACIÓN
                modalTitle.textContent = 'Nueva Propiedad';
                btnText.textContent = 'Registrar Propiedad';
                btnIcon.className = 'bi bi-plus-lg';
                methodField.value = 'POST';
                formPropiedad.action = "/propiedades";
                formPropiedad.reset();

                document.getElementById('show_address').checked = true;
                document.getElementById('is_featured').checked = false;

                // Limpiar el select de colonias al crear nueva
                const coloniaSelect = document.getElementById('neighborhood'); // neighborhood es tu select de colonias
                if (coloniaSelect) coloniaSelect.innerHTML = '<option value="">Ingrese CP...</option>';
            }
        });
    }

    // --- LÓGICA DE BÚSQUEDA DE CP (PETICIÓN AL SERVIDOR) ---
  
    const neighborhood = document.getElementById('neighborhood');
    const stateInput = document.getElementById('state');
    const cityInput = document.getElementById('city');
 

    if (cpInput) {
        cpInput.addEventListener('input', function (e) {
            const cp = e.target.value.trim();

            if (cp.length === 5 && /^\d+$/.test(cp)) {
                cpInput.classList.add('is-loading');

                fetch(`/api/consulta-cp/${cp}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Error en el servidor');
                        return response.json();
                    })
                    .then(result => {
                        if (result.colonias && result.colonias.length > 0) {
                            const first = result.colonias[0];

                            if (stateInput) stateInput.value = first.estado || '';
                            if (cityInput) cityInput.value = first.municipio || '';

                            // Llenar select de colonias
                            if (neighborhood) {
                                neighborhood.innerHTML = '<option value="">-- Selecciona una colonia --</option>';
                                result.colonias.forEach(item => {
                                    const option = document.createElement('option');
                                    option.value = item.colonia;
                                    option.textContent = `${item.colonia} (${item.tipoAsentamiento})`;
                                    neighborhood.appendChild(option);
                                });
                                neighborhood.style.display = 'block'; // mostrar
                            }

                            cpInput.classList.remove('is-invalid');
                        } else {
                            cpInput.classList.add('is-invalid');
                            if (neighborhood) neighborhood.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición:', error);
                        cpInput.classList.add('is-invalid');
                    })
                    .finally(() => cpInput.classList.remove('is-loading'));
            } else if (cp.length === 0) {
                // Limpiar campos si borran el CP
                if (stateInput) stateInput.value = '';
                if (cityInput) cityInput.value = '';
                if (colonySelect) {
                    colonySelect.innerHTML = '<option value="">-- Selecciona una colonia --</option>';
                    colonySelect.style.display = 'none';
                }
            }
        });
    }

    // SweetAlert para eliminar (Simplificado para usar delegación)
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('form-eliminar')) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "La propiedad se eliminará permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) e.target.submit();
            });
        }
    });
});