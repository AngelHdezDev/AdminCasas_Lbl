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
            // Asegúrate de que no haya espacios ni letras extras
            formEditar.action = `/vendedores/${sellerId.trim()}`;

            // 3. Llenar los campos del formulario
            // IMPORTANTE: Los IDs deben coincidir con tu HTML
            if (document.getElementById('edit_name')) document.getElementById('edit_name').value = name || '';
            if (document.getElementById('edit_email')) document.getElementById('edit_email').value = email || '';
            if (document.getElementById('edit_phone')) document.getElementById('edit_phone').value = phone || '';
            if (document.getElementById('edit_notes')) document.getElementById('edit_notes').value = notes || '';

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

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-eliminar-vendedor');
        if (!button) return;

        const url = button.getAttribute('data-url');
        const name = button.getAttribute('data-name') || 'este vendedor';
        const laravelData = document.getElementById('laravel-data');
        const csrfToken = laravelData ? laravelData.getAttribute('data-csrf-token') : '';

        Swal.fire({
            title: '¿Eliminar vendedor?',
            text: `Se dara de baja a "${name}".`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            button.disabled = true;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'No se pudo eliminar el vendedor.');
                    }

                    return data;
                })
                .then(data => {
                    const row = button.closest('tr');
                    if (row) row.remove();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2200
                    });

                    const tableBody = document.querySelector('#clientsTable tbody');
                    if (tableBody && tableBody.children.length === 0) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#c0392b'
                    });
                })
                .finally(() => {
                    button.disabled = false;
                });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formEditarVendedor');
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    const laravelData = document.getElementById('laravel-data');
    const csrfToken = laravelData ? laravelData.getAttribute('data-csrf-token') : '';

    function clearAjaxErrors() {
        form.querySelectorAll('.is-invalid').forEach(field => field.classList.remove('is-invalid'));
        form.querySelectorAll('.ajax-invalid-feedback').forEach(feedback => feedback.remove());
    }

    function showAjaxErrors(errors) {
        let firstField = null;

        Object.entries(errors).forEach(([name, messages]) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (!field) return;

            field.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback ajax-invalid-feedback';
            feedback.style.display = 'block';
            feedback.textContent = messages[0];

            const container = field.closest('.field-group') || field.parentElement;
            container.appendChild(feedback);

            if (!firstField) firstField = field;
        });

        if (firstField) firstField.focus();
    }

    function updateSellerRow(seller) {
        const editButton = document.querySelector(`.btn-edit[data-bs-target="#modalEditarVendedor"][data-id="${seller.id}"]`);
        if (!editButton) return;

        const row = editButton.closest('tr');
        editButton.dataset.name = seller.name || '';
        editButton.dataset.email = seller.email || '';
        editButton.dataset.phone = seller.phone || '';
        editButton.dataset.notes = seller.notes || '';
        editButton.dataset.contract = seller.contract_path || '';

        if (!row) return;

        const nameCell = row.querySelector('.vehicle-name');
        if (nameCell) nameCell.textContent = seller.name || '';

        const cells = row.querySelectorAll('td');
        if (cells[1]) cells[1].innerHTML = `<i class="bi bi-telephone text-muted me-1"></i> ${seller.phone || ''}`;
        if (cells[2]) cells[2].textContent = seller.email || 'Sin correo';
        if (cells[3]) cells[3].textContent = seller.notes || '';
        if (cells[4] && seller.contract_path) {
            cells[4].innerHTML = `
                <div class="position-relative d-inline-block">
                    <img src="${seller.contract_url}" alt="ID ${seller.name || ''}"
                        loading="lazy" class="rounded shadow-sm border"
                        style="width: 50px; height: 40px; object-fit: cover; cursor: pointer;"
                        onclick="window.open(this.src, '_blank')">
                </div>`;
        }
    }

    function closeSellerEditModal() {
        const modalElement = document.getElementById('modalEditarVendedor');
        if (!modalElement) return;

        const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.hide();

        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
        }, 300);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearAjaxErrors();

        const originalText = submitButton ? submitButton.innerHTML : '';
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Guardando...';
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        })
            .then(async response => {
                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    if (response.status === 422 && data.errors) {
                        showAjaxErrors(data.errors);
                    }
                    throw new Error(data.message || 'Revisa los campos marcados e intenta de nuevo.');
                }

                return data;
            })
            .then(data => {
                updateSellerRow(data.seller);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2200
                });

                closeSellerEditModal();
                const fileInput = form.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = '';
            })
            .catch(error => {
                Swal.fire({
                    title: 'No se pudo actualizar',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#c0392b'
                });
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
    });
});
