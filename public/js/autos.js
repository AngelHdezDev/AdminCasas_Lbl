document.addEventListener('DOMContentLoaded', function () {
    // Elementos del modal y formulario
    const modalPropiedad = document.getElementById('modalPropiedad');
    const formPropiedad = document.getElementById('formPropiedad');
    const cpInput = document.getElementById('cp');
    const neighborhoodSelect = document.getElementById('neighborhood');
    const stateInput = document.getElementById('state');
    const cityInput = document.getElementById('city');

    // Función reutilizable para cargar colonias desde la API
    function cargarColonias(cp) {
        return fetch(`/api/consulta-cp/${cp}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(result => {
                if (result.colonias && result.colonias.length > 0) {
                    const primeraColonia = result.colonias[0];
                    if (stateInput) stateInput.value = primeraColonia.estado || '';
                    if (cityInput) cityInput.value = primeraColonia.municipio || '';

                    if (neighborhoodSelect) {
                        neighborhoodSelect.innerHTML = '<option value="">-- Selecciona una colonia --</option>';
                        result.colonias.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.colonia;
                            option.textContent = `${item.colonia} (${item.tipoAsentamiento})`;
                            neighborhoodSelect.appendChild(option);
                        });
                    }
                    return result.colonias;
                } else {
                    throw new Error('No se encontraron colonias para este CP');
                }
            });
    }

    // --- LÓGICA DEL MODAL (creación / edición) ---
    if (modalPropiedad) {
        modalPropiedad.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button ? button.getAttribute('data-id') : null;

            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const btnText = document.getElementById('btnSubmitText');
            const btnIcon = document.getElementById('btnSubmitIcon');

            if (id) {
                // ========== MODO EDICIÓN ==========
                modalTitle.textContent = 'Editar Propiedad';
                btnText.textContent = 'Guardar Cambios';
                btnIcon.className = 'bi bi-check-lg';
                methodField.value = 'PUT';
                formPropiedad.action = `/propiedades/${id}`;

                // Campos de texto / número
                const fields = [
                    'title', 'address', 'm2_land', 'm2_construction',
                    'bedrooms', 'bathrooms', 'parking_spots', 'price',
                    'description', 'city', 'state', 'cp'
                ];
                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                // Selects simples
                const selects = ['type', 'contract_type', 'seller_id', 'client_id'];
                selects.forEach(field => {
                    const select = document.getElementById(field);
                    if (select) {
                        select.value = button.getAttribute(`data-${field}`) || '';
                    }
                });

                // Checkboxes
                const featuredCheck = document.getElementById('is_featured');
                if (featuredCheck) featuredCheck.checked = button.getAttribute('data-is_featured') === '1';
                const showAddressCheck = document.getElementById('show_address');
                if (showAddressCheck) showAddressCheck.checked = button.getAttribute('data-show_public_address') === '1';

                // --- Cargar colonias según el CP guardado ---
                const cpGuardado = button.getAttribute('data-cp') || '';
                const coloniaGuardada = button.getAttribute('data-neighborhood') || '';

                if (cpGuardado && cpGuardado.length === 5) {
                    // Mostrar estado de carga (opcional)
                    if (neighborhoodSelect) neighborhoodSelect.innerHTML = '<option value="">Cargando colonias...</option>';

                    cargarColonias(cpGuardado)
                        .then(() => {
                            if (neighborhoodSelect && coloniaGuardada) {
                                // Intentar seleccionar la colonia guardada
                                neighborhoodSelect.value = coloniaGuardada;
                                // Si no existe exactamente, se deja en blanco (el usuario puede elegir)
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar colonias en edición:', error);
                            if (neighborhoodSelect) {
                                neighborhoodSelect.innerHTML = '<option value="">Error al cargar colonias</option>';
                            }
                        });
                } else {
                    // Si no hay CP, limpiar el select
                    if (neighborhoodSelect) {
                        neighborhoodSelect.innerHTML = '<option value="">Ingrese un CP para cargar colonias...</option>';
                    }
                }
            } else {
                // ========== MODO CREACIÓN ==========
                modalTitle.textContent = 'Nueva Propiedad';
                btnText.textContent = 'Registrar Propiedad';
                btnIcon.className = 'bi bi-plus-lg';
                methodField.value = 'POST';
                formPropiedad.action = "/propiedades";
                formPropiedad.reset();

                // Valores por defecto
                const showAddressCheck = document.getElementById('show_address');
                if (showAddressCheck) showAddressCheck.checked = true;
                const featuredCheck = document.getElementById('is_featured');
                if (featuredCheck) featuredCheck.checked = false;

                // Limpiar campos de ubicación y select de colonias
                if (stateInput) stateInput.value = '';
                if (cityInput) cityInput.value = '';
                if (cpInput) cpInput.value = '';
                if (neighborhoodSelect) {
                    neighborhoodSelect.innerHTML = '<option value="">Ingrese un CP para cargar colonias...</option>';
                }
            }
        });
    }

    // --- BÚSQUEDA AUTOMÁTICA AL ESCRIBIR EL CP ---
    if (cpInput) {
        cpInput.addEventListener('input', function (e) {
            const cp = e.target.value.trim();

            // Validar que tenga exactamente 5 dígitos
            if (cp.length === 5 && /^\d+$/.test(cp)) {
                cpInput.classList.add('is-loading');

                cargarColonias(cp)
                    .catch(error => {
                        console.error('Error en búsqueda de CP:', error);
                        cpInput.classList.add('is-invalid');
                        if (neighborhoodSelect) {
                            neighborhoodSelect.innerHTML = '<option value="">No se encontraron colonias</option>';
                        }
                        if (stateInput) stateInput.value = '';
                        if (cityInput) cityInput.value = '';
                    })
                    .finally(() => {
                        cpInput.classList.remove('is-loading');
                    });
            } else if (cp.length === 0) {
                // Si borran el CP, limpiar campos dependientes
                if (stateInput) stateInput.value = '';
                if (cityInput) cityInput.value = '';
                if (neighborhoodSelect) {
                    neighborhoodSelect.innerHTML = '<option value="">Ingrese un CP para cargar colonias...</option>';
                }
                cpInput.classList.remove('is-invalid');
            } else {
                // CP inválido (menos de 5 dígitos o con letras)
                cpInput.classList.add('is-invalid');
            }
        });
    }

    // --- ELIMINACIÓN CON SWEETALERT (delegación de eventos) ---
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