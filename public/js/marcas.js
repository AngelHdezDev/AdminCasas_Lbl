document.addEventListener('DOMContentLoaded', function () {
    // ── 1. VARIABLES DEL DOM (IDs ÚNICOS) ─────────────────────────────
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('imagen');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const previewName = document.getElementById('previewName');
    const previewSize = document.getElementById('previewSize');
    const uploadInitialState = document.getElementById('uploadInitialState');
    const uploadPreviewState = document.getElementById('uploadPreviewState');
    const uploadPreviewImage = document.getElementById('uploadPreviewImage');
    const btnRemoveImage = document.getElementById('btnRemoveImage');
    const formMarca = document.getElementById('formNuevaMarca');
    const laravelData = document.getElementById('laravel-data');

    // ── 2. BUSCADOR EN TIEMPO REAL ────────────────────────────────────
    let timeout = null;
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.closest('form').submit();
            }, 600);
        });
    }

    // ── 3. LÓGICA DUAL DEL MODAL (CREAR / EDITAR) ──────────────────────
    const modalElement = document.getElementById('modalNuevaMarca');
    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Botón que disparó el modal
            const tipo = button.getAttribute('data-tipo');
            const methodInput = document.getElementById('formMethod');
            const modalTitle = this.querySelector('.modal-title-text');
            const btnSubmit = this.querySelector('.btn-submit');

            if (tipo === 'editar') {
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const imagenUrl =button.getAttribute('data-imagen');

                modalTitle.textContent = 'Editar Marca';
                btnSubmit.innerHTML = '<i class="bi bi-check-lg"></i> Actualizar Marca';
                formMarca.action = `/marcas/${id}`;
                methodInput.value = 'PUT';

                console.log("Editando ID:", id);
                console.log("Nueva Action del Form:", formMarca.action);

                document.getElementById('nombre').value = nombre;
                fileInput.required = false; // Al editar, la imagen es opcional

                if (imagenUrl) {
                    console.log("Imagen URL:", imagenUrl);
                    uploadPreviewImage.src = imagenUrl;
                    uploadInitialState.style.display = 'none';
                    uploadPreviewState.style.display = 'block';
                }
            } else {
                // Configuración para NUEVA MARCA
                modalTitle.textContent = 'Nueva Marca';
                btnSubmit.innerHTML = '<i class="bi bi-check-lg"></i> Guardar Marca';
                formMarca.action = "/marcas";
                methodInput.value = 'POST';
                formMarca.reset();
                fileInput.required = true;
                removeImage(); // Limpia previews anteriores
            }
        });
    }

    // ── 4. GESTIÓN DE IMAGEN (UPLOAD & PREVIEW) ───────────────────────
    function handleFileSelect(file) {
        if (!file) return;

        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'warning', title: 'Archivo muy grande', text: 'Máximo 2MB' });
            removeImage();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            // Preview principal en la zona de carga
            uploadPreviewImage.src = e.target.result;
            uploadInitialState.style.display = 'none';
            uploadPreviewState.style.display = 'block';

            // Info detallada en el contenedor inferior
            if (previewImage) previewImage.src = e.target.result;
            if (previewName) previewName.textContent = file.name;
            if (previewSize) previewSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            if (previewContainer) previewContainer.classList.add('active');

            uploadZone.style.borderColor = 'var(--gold)';
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        fileInput.value = '';
        uploadInitialState.style.display = 'block';
        uploadPreviewState.style.display = 'none';
        if (previewContainer) previewContainer.classList.remove('active');
        uploadPreviewImage.src = '';
        uploadZone.style.borderColor = '';
        uploadZone.style.background = '';
    }

    // Eventos Drag & Drop
    if (uploadZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(name => {
            uploadZone.addEventListener(name, (e) => { e.preventDefault(); e.stopPropagation(); });
        });

        ['dragenter', 'dragover'].forEach(name => {
            uploadZone.addEventListener(name, () => uploadZone.classList.add('dragging'));
        });

        ['dragleave', 'drop'].forEach(name => {
            uploadZone.addEventListener(name, () => uploadZone.classList.remove('dragging'));
        });

        uploadZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });
    }

    // Evento Input File normal
    fileInput?.addEventListener('change', (e) => {
        if (e.target.files.length > 0) handleFileSelect(e.target.files[0]);
    });

    // Botón remover
    btnRemoveImage?.addEventListener('click', (e) => {
        e.preventDefault();
        removeImage();
    });

    // ── 5. NOTIFICACIONES Y ELIMINACIÓN ──────────────────────────────
    if (laravelData) {
        const success = laravelData.dataset.success;
        const errorGeneral = laravelData.dataset.error;
        const validationError = laravelData.dataset.validationError; // El mensaje exacto
        const hasErrors = laravelData.dataset.hasErrors === 'true';

        if (success) {
            Swal.fire({ icon: 'success', title: '¡Hecho!', text: success, timer: 2000, showConfirmButton: false });
        }

        // Si hay un error de validación (ej: marca duplicada), mostrarlo con prioridad
        if (validationError) {
            Swal.fire({
                icon: 'error',
                title: 'Validación fallida',
                text: validationError // Aquí aparecerá "Esta marca ya se encuentra registrada"
            })
        }
        // Si no es de validación pero hay un error general del try-catch
        else if (errorGeneral) {
            Swal.fire({ icon: 'error', title: 'Error', text: errorGeneral });
        }
    }

    // ── 5. NOTIFICACIONES Y CAMBIO DE ESTADO ──────────────────────────────
    // ... (mantén tu lógica de laravelData igual) ...

    document.querySelectorAll('.btn-toggle-status').forEach(btn => {
        btn.addEventListener('click', function () {
            // Obtenemos los datos dinámicos del botón
            const nombre = this.getAttribute('data-marca-nombre');
            const active = this.getAttribute('data-active'); // "1" o "0"
            const form = this.closest('form');

            // Configuramos el mensaje según el estado actual
            const esDesactivar = (active == "1");
            const titulo = esDesactivar ? '¿Desactivar marca?' : '¿Activar marca?';
            const texto = esDesactivar
                ? `La marca ${nombre} ya no se mostrará en el catálogo.`
                : `La marca ${nombre} volverá a estar visible.`;
            const btnText = esDesactivar ? 'Sí, desactivar' : 'Sí, activar';
            const btnColor = esDesactivar ? '#c0392b' : '#27ae60';

            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: btnText,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Dispara el método destroy de tu controlador
                }
            });
        });
    });

    // Quitar estados de error al escribir
    document.querySelectorAll('.field-input').forEach(el => {
        el.addEventListener('input', () => el.classList.remove('is-invalid'));
    });
});