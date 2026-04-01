// galeria.js — Vista de Galería de Imágenes
document.addEventListener('DOMContentLoaded', function () {

    // ── Modal de Upload con Drag & Drop ───────────────────────────────
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('imagenes');
    const previewContainer = document.getElementById('previewContainer');
    const fileList = document.getElementById('fileList');
    const btnUpload = document.getElementById('btnUpload');

    if (dropZone && fileInput) {
        // Click en la zona para abrir selector
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag & Drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.style.borderColor = 'var(--gold)';
                dropZone.style.background = 'var(--white)';
            });
        });

        // CORRECCIÓN 1: Había una comilla simple huérfana después de eventName
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.style.borderColor = 'var(--gray-200)';
                dropZone.style.background = 'var(--gray-50)';
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            fileInput.files = files;
            handleFiles(files);
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            if (files.length === 0) {
                if (previewContainer) previewContainer.style.display = 'none';
                if (btnUpload) btnUpload.disabled = true;
                return;
            }

            fileList.innerHTML = '';
            let validFiles = 0;

            Array.from(files).forEach((file) => {
                if (!file.type.startsWith('image/')) return;

                validFiles++;
                const fileItem = document.createElement('div');
                fileItem.style.cssText = `
                    display: flex;
                    align-items: center;
                    gap: 0.8rem;
                    padding: 0.7rem 1rem;
                    background: var(--white);
                    border: 1px solid var(--gray-200);
                    border-radius: 8px;
                    margin-bottom: 0.5rem;
                `;

                fileItem.innerHTML = `
                    <i class="bi bi-file-earmark-image" style="font-size: 1.2rem; color: var(--gold);"></i>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.85rem; font-weight: 500; color: var(--gray-700); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${file.name}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--gray-400);">
                            ${formatBytes(file.size)}
                        </div>
                    </div>
                    <i class="bi bi-check-circle-fill" style="color: var(--success-color); font-size: 1.1rem;"></i>
                `;
                fileList.appendChild(fileItem);
            });

            if (validFiles > 0) {
                previewContainer.style.display = 'block';
                btnUpload.disabled = false;
                btnUpload.innerHTML = `<i class="bi bi-upload"></i> Subir ${validFiles} imagen${validFiles > 1 ? 'es' : ''}`;
            } else {
                previewContainer.style.display = 'none';
                btnUpload.disabled = true;
            }
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    }

    // ── Limpiar modal al cerrar ───────────────────────────────────────
    document.getElementById('modalUpload')?.addEventListener('hidden.bs.modal', () => {
        const form = document.getElementById('formUpload');
        if (form) form.reset();
        if (previewContainer) previewContainer.style.display = 'none';
        if (fileList) fileList.innerHTML = '';
        if (btnUpload) {
            btnUpload.disabled = true;
            btnUpload.innerHTML = '<i class="bi bi-upload"></i> Subir Imágenes';
        }
    });

    // ── Habilitar botón de asignar ────────────────────────────────────
    document.querySelectorAll('.vehicle-select').forEach(select => {
        const form = select.closest('form');
        const btnAssign = form.querySelector('.btn-assign');
        const originalValue = select.dataset.original;

        select.addEventListener('change', function() {
            btnAssign.disabled = (this.value === originalValue);
        });
    });

    // ── Confirmación antes de asignar ─────────────────────────────────
    document.querySelectorAll('.assign-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Detenemos siempre el envío inicial
            const select = this.querySelector('.vehicle-select');
            const selectedOption = select.options[select.selectedIndex];
            const vehicleText = selectedOption.text;
            
            const isUnassigning = (select.value === '');

            Swal.fire({
                title: isUnassigning ? '¿Desasignar imagen?' : '¿Asignar imagen?',
                text: isUnassigning ? 'La imagen quedará sin vehículo asociado' : `La imagen se vinculará a: ${vehicleText}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isUnassigning ? '#2d2d2a' : '#27ae60',
                cancelButtonColor: '#9d9d96',
                confirmButtonText: isUnassigning ? 'Sí, desasignar' : 'Sí, asignar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    this.submit(); // CORRECCIÓN 2: Ahora sí enviamos el form tras confirmar
                }
            });
        });
    });

    // ── Filtro por vehículo ───────────────────────────────────────────
    const filterVehiculo = document.getElementById('filterVehiculo');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const countEl = document.getElementById('countVisible');

    if (filterVehiculo) {
        filterVehiculo.addEventListener('change', function() {
            const vehiculoId = this.value;
            let visible = 0;

            galleryItems.forEach(item => {
                const itemVehiculo = item.dataset.vehiculo;
                const show = (vehiculoId === '' || itemVehiculo === vehiculoId || (vehiculoId === 'sin-asignar' && itemVehiculo === 'sin-asignar'));
                item.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (countEl) countEl.textContent = visible;
        });
    }

    // ── Eliminar imagen ───────────────────────────────────────────────
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#9d9d96',
                confirmButtonText: 'Sí, eliminar',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // ── Ver imagen en grande ──────────────────────────────────────────
    // CORRECCIÓN 3: Sacar la función del DOMContentLoaded para que sea global (window)
    window.viewImage = function(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Vista de imagen',
            showCloseButton: true,
            showConfirmButton: false,
            width: 'auto',
            padding: '1rem'
        });
    };

    // ── Notificación de éxito ─────────────────────────────────────────
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        Swal.fire({
            icon: 'success',
            title: '¡Operación exitosa!',
            timer: 2000,
            showConfirmButton: false
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});