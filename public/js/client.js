document.addEventListener('DOMContentLoaded', function () {
    const modalEditar = document.getElementById('modalEditarCliente');
    const formEditar = document.getElementById('formEditarCliente');
    const modalNuevo = document.getElementById('modalNuevoCliente');

    // ── MODAL EDITAR: poblar al abrir ──
    if (modalEditar) {
        document.addEventListener('click', function (event) {
            const button = event.target.closest('.btn-edit[data-bs-target="#modalEditarCliente"]');
            if (!button) return;

            event.preventDefault();
            event.stopPropagation();

            bootstrap.Modal.getOrCreateInstance(modalEditar).show(button);
        }, true);

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

window.confirmDeleteFile = function (clientId) {
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
};

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-eliminar-cliente');
        if (!button) return;

        const url = button.getAttribute('data-url');
        const name = button.getAttribute('data-name') || 'este cliente';
        const laravelData = document.getElementById('laravel-data');
        const csrfToken = laravelData ? laravelData.getAttribute('data-csrf-token') : '';

        Swal.fire({
            title: '¿Eliminar cliente?',
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
                        throw new Error(data.message || 'No se pudo eliminar el cliente.');
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
    const form = document.getElementById('formEditarCliente');
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

    function updateClientRow(client) {
        const editButton = document.querySelector(`.btn-edit[data-bs-target="#modalEditarCliente"][data-id="${client.id}"]`);
        if (!editButton) return;

        const row = editButton.closest('tr');
        editButton.dataset.name = client.name || '';
        editButton.dataset.email = client.email || '';
        editButton.dataset.phone = client.phone || '';
        editButton.dataset.notes = client.notes || '';
        editButton.dataset.identification = client.identification_path || '';

        if (!row) return;

        const nameCell = row.querySelector('.vehicle-name');
        if (nameCell) nameCell.textContent = client.name || '';

        const cells = row.querySelectorAll('td');
        if (cells[1]) cells[1].innerHTML = `<i class="bi bi-telephone text-muted me-1"></i> ${client.phone || ''}`;
        if (cells[2]) cells[2].textContent = client.email || 'Sin correo';
        if (cells[3]) cells[3].textContent = client.notes || '';
        if (cells[4] && client.identification_path) {
            cells[4].innerHTML = `
                <div class="position-relative d-inline-block">
                    <img src="${client.identification_url}" alt="ID ${client.name || ''}"
                        loading="lazy" class="rounded shadow-sm border"
                        style="width: 50px; height: 40px; object-fit: cover; cursor: pointer;"
                        onclick="window.open(this.src, '_blank')">
                </div>`;
        }
    }

    function closeClientEditModal() {
        const modalElement = document.getElementById('modalEditarCliente');
        if (!modalElement) return;

        const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
        const cleanup = () => {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
            bootstrap.Modal.getInstance(modalElement)?.dispose();
        };

        modalElement.addEventListener('hidden.bs.modal', cleanup, { once: true });
        modal.hide();

        setTimeout(cleanup, 350);
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
                updateClientRow(data.client);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2200
                });

                closeClientEditModal();
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

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formCliente');
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    const laravelData = document.getElementById('laravel-data');
    const csrfToken = laravelData ? laravelData.getAttribute('data-csrf-token') : '';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

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

    function closeCreateModal() {
        const modalElement = document.getElementById('modalNuevoCliente');
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

    function appendClientRow(client) {
        const tableBody = document.querySelector('#clientsTable tbody');
        if (!tableBody) {
            window.location.reload();
            return;
        }

        const identificationCell = client.identification_path
            ? `<div class="position-relative d-inline-block">
                    <img src="${escapeHtml(client.identification_url)}" alt="ID ${escapeHtml(client.name)}"
                        loading="lazy" class="rounded shadow-sm border"
                        style="width: 50px; height: 40px; object-fit: cover; cursor: pointer;"
                        onclick="window.open(this.src, '_blank')">
                </div>`
            : `<span class="badge bg-light text-muted border">
                    <i class="bi bi-x-circle"></i> Sin ID
                </span>`;

        tableBody.insertAdjacentHTML('afterbegin', `
            <tr>
                <td>
                    <div class="vehicle-cell">
                        <div class="vehicle-thumb">
                            <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                        </div>
                        <div>
                            <div class="vehicle-name">${escapeHtml(client.name)}</div>
                            <div class="vehicle-brand">Registrado el ${escapeHtml(client.created_at)}</div>
                        </div>
                    </div>
                </td>
                <td style="font-weight: 500; color: var(--gray-700);">
                    <i class="bi bi-telephone text-muted me-1"></i> ${escapeHtml(client.phone)}
                </td>
                <td style="color: var(--gray-500);">${client.email ? escapeHtml(client.email) : 'Sin correo'}</td>
                <td style="color: var(--gray-500); max-width: 200px;" class="text-truncate">${escapeHtml(client.notes)}</td>
                <td class="align-middle text-center">${identificationCell}</td>
                <td>
                    <div class="action-buttons" style="justify-content: flex-end;">
                        <a href="${escapeHtml(client.show_url)}" class="btn-action" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a class="btn-action btn-edit" title="Editar Cliente" data-bs-toggle="modal"
                            data-bs-target="#modalEditarCliente" data-id="${escapeHtml(client.id)}"
                            data-name="${escapeHtml(client.name)}" data-email="${escapeHtml(client.email)}"
                            data-phone="${escapeHtml(client.phone)}" data-notes="${escapeHtml(client.notes)}"
                            data-identification="${escapeHtml(client.identification_path)}" style="cursor: pointer;">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <div style="display:inline;">
                            <button type="button" class="btn-action delete btn-delete btn-eliminar-cliente"
                                data-id="${escapeHtml(client.id)}" data-name="${escapeHtml(client.name)}"
                                data-url="${escapeHtml(client.delete_url)}" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `);
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
                appendClientRow(data.client);
                form.reset();
                closeCreateModal();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2200
                });
            })
            .catch(error => {
                Swal.fire({
                    title: 'No se pudo guardar',
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

window.confirmDeleteFile = function (clientId) {
    Swal.fire({
        title: 'Eliminar identificacion?',
        text: 'Se borrara el archivo fisico del servidor permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/clientes/${clientId}/archivo`, {
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
                    throw new Error(data.message || 'No se pudo eliminar la identificacion.');
                }

                return data;
            })
            .then(data => {
                const previewContainer = document.getElementById('preview-container-edit');
                if (previewContainer) {
                    previewContainer.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="small mb-0">Sin vista previa disponible</p>
                        </div>`;
                }

                const editButton = document.querySelector(`.btn-edit[data-bs-target="#modalEditarCliente"][data-id="${clientId}"]`);
                if (editButton) {
                    editButton.dataset.identification = '';
                    const row = editButton.closest('tr');
                    const fileCell = row ? row.querySelectorAll('td')[4] : null;
                    if (fileCell) {
                        fileCell.innerHTML = `
                            <span class="badge bg-light text-muted border">
                                <i class="bi bi-x-circle"></i> Sin ID
                            </span>`;
                    }
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2200
                });
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#c0392b'
                });
            });
    });
};
