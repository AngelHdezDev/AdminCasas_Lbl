// 1. Función Global de Preview (Fuera del DOMContentLoaded para que sea accesible)
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Validaciones de archivo
        if (file.size > 2048000) {
            Swal.fire({ icon: 'warning', title: 'Archivo muy grande', text: 'La imagen no debe superar los 2MB', background: '#1a1f3a', color: '#e4e8f0' });
            event.target.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const laravelData = document.getElementById('laravel-data');
    if (!laravelData) return;

    // 2. Extraer datos del "puente" Blade
    const hasErrors = laravelData.dataset.hasErrors === 'true';
    const successMsg = laravelData.dataset.success; // Captura session('success')
    const errorMsg = laravelData.dataset.errorMsg;

    // 3. Lógica de Éxito (SweetAlert)
    if (successMsg && successMsg !== 'null' && successMsg !== '') {
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: successMsg,
            background: '#1a1f3a',
            color: '#e4e8f0',
            confirmButtonColor: '#00d4ff',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // 4. Lógica de Errores (Reabrir modales)
    if (hasErrors) {
        // Determinamos qué modal abrir por el contenido del error
        const txtError = errorMsg ? errorMsg.toLowerCase() : "";
        // Si el error menciona campos de marca, abrimos marca. Si no, vehículo.
        const esMarca = txtError.includes('nombre') || txtError.includes('imagen');

        const idModal = esMarca ? 'modalNuevaMarca' : 'modalNuevoVehiculo';
        const modalEl = document.getElementById(idModal);

        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }

        Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: errorMsg || 'Verifica los datos',
            background: '#1a1f3a',
            color: '#e4e8f0',
            confirmButtonColor: '#ff6b35'
        });
    }

    // 5. Validación y Bloqueo de Formularios (Submit)
    const setupForm = (formId, isVehiculo = false) => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            // Evitar doble envío
            if (form.dataset.enviando === 'true') {
                e.preventDefault();
                return false;
            }

            // Validación manual rápida antes de enviar
            if (isVehiculo) {
                const precio = parseFloat(document.getElementById('precio').value);
                if (precio < 0) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Precio inválido', text: 'El precio debe ser mayor a 0', background: '#1a1f3a', color: '#e4e8f0' });
                    return;
                }
            }

            // Si todo está bien, bloquear botón
            form.dataset.enviando = 'true';
            const boton = form.querySelector('button[type="submit"]');
            if (boton) {
                boton.disabled = true;
                boton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
            }
        });
    };

    setupForm('formNuevaMarca');
    setupForm('formNuevoVehiculo', true);

    // 6. Limpiar formularios al cerrar
    // 6. Limpiar formularios al cerrar
    ['modalNuevaMarca', 'modalNuevoVehiculo'].forEach(id => {
        document.getElementById(id)?.addEventListener('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            if (form) {
                form.reset();
                form.dataset.enviando = 'false';
                const btn = form.querySelector('button[type="submit"]');

                if (btn) {
                    btn.disabled = false;
                    // CORRECCIÓN AQUÍ: Usamos el ID del modal para saber qué texto poner
                    if (id === 'modalNuevaMarca') {
                        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Marca';
                    } else {
                        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Vehículo';
                    }
                }
            }
            // Limpiar preview específicamente si es el de marcas
            if (id === 'modalNuevaMarca') {
                const preview = document.getElementById('imagePreview');
                if (preview) preview.style.display = 'none';
            }
        });
    });
});