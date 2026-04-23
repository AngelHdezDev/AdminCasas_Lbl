/**
 * detalle-vehiculo.js
 * Versión final optimizada: Delegación de eventos y prevención de burbujeo.
 */

document.addEventListener('DOMContentLoaded', function () {

    // 1. GESTIÓN DE GALERÍA (Cambiar Imagen Principal)
    // Se liga al clic en las miniaturas
    window.changeImage = function (imageUrl, thumbnailElement) {
        const featuredImage = document.getElementById('featuredImage');
        if (featuredImage) {
            featuredImage.src = imageUrl;
        }

        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
        });

        if (thumbnailElement) {
            thumbnailElement.classList.add('active');
        }
    };

    // 2. VISTA PANTALLA COMPLETA
    window.viewFullscreen = function () {
        const featuredImage = document.getElementById('featuredImage');
        if (featuredImage) {
            Swal.fire({
                imageUrl: featuredImage.src,
                imageAlt: 'Vista de imagen',
                showCloseButton: true,
                showConfirmButton: false,
                background: '#ffffff',
                customClass: { popup: 'fullscreen-image-popup', image: 'img-fluid' }
            });
        }
    };

    // 3. NAVEGACIÓN CON TECLADO
    document.addEventListener('keydown', function (e) {
        const thumbnails = Array.from(document.querySelectorAll('.thumbnail-item'));
        const activeThumbnail = document.querySelector('.thumbnail-item.active');
        if (!activeThumbnail || thumbnails.length <= 1) return;

        const currentIndex = thumbnails.indexOf(activeThumbnail);
        if (e.key === 'ArrowLeft' && currentIndex > 0) {
            thumbnails[currentIndex - 1].querySelector('img').click();
        } else if (e.key === 'ArrowRight' && currentIndex < thumbnails.length - 1) {
            thumbnails[currentIndex + 1].querySelector('img').click();
        }
    });

    // 4. ELIMINACIÓN DE IMÁGENES (EL "LIGADO" DEFINITIVO)
    // Usamos delegación de eventos para evitar problemas con el @foreach
    // En tu detalle-vehiculo.js, actualiza el listener de clics:

    // detalle-vehiculo.js

    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.btn-delete-thumbnail');

        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();

            const form = deleteBtn.closest('.delete-image-form');
            const url = form.getAttribute('action');
            const token = form.querySelector('input[name="_token"]').value;

            Swal.fire({
                title: '¿Eliminar imagen?',
                text: "La página se recargará para actualizar la galería.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostramos un indicador de carga mientras el servidor responde
                    Swal.fire({
                        title: 'Eliminando...',
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // ÉXITO: Recargamos la página completa
                                window.location.reload();
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Hubo un problema de comunicación con el servidor.', 'error');
                        });
                }
            });
        }
    });
    // 5. ELIMINACIÓN DE VEHÍCULO COMPLETO
    const btnDeleteVehicle = document.querySelector('.btn-delete');
    if (btnDeleteVehicle) {
        btnDeleteVehicle.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('.delete-form');

            Swal.fire({
                title: '¿Eliminar este vehículo?',
                text: 'Se borrarán todos los datos e imágenes asociadas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                confirmButtonText: 'Sí, eliminar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }
});