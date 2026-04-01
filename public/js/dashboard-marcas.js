function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
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
    const hasErrors = laravelData.getAttribute('data-has-errors') === 'true';
    const successMsg = laravelData.getAttribute('data-success');
    const errorMsg = laravelData.getAttribute('data-error-msg');

   
    if (successMsg !== 'false' && successMsg !== '') {
        Swal.fire({
            icon: 'success',
            title: '¡Logrado!',
            text: successMsg,
            timer: 2000,
            showConfirmButton: false
        });
    }

    if (hasErrors) {
        Swal.fire({
            icon: 'error',
            title: 'Ups... algo salió mal',
            text: errorMsg || 'Verifica los datos ingresados',
            confirmButtonColor: '#3085d6'
        });

        const modalElement = document.getElementById('modalNuevaMarca');
        if (modalElement) {
            new bootstrap.Modal(modalElement).show();
        }
    }

    const formMarca = document.getElementById('formNuevaMarca') || document.querySelector('form');
    if (formMarca) {
        formMarca.addEventListener('submit', function (e) {
            if (formMarca.dataset.enviando === 'true') {
                e.preventDefault();
                return false;
            }
            formMarca.dataset.enviando = 'true';
            const boton = formMarca.querySelector('button[type="submit"]');
            boton.disabled = true;
            boton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
        });
    }
});