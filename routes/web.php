<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PropertyController;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.authenticate');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'getMarcas'])
    ->middleware('auth')
    ->name('dashboard');

Route::post('/marcas', [MarcaController::class, 'store'])->middleware('auth')->name('marcas.store');
// Route::get('/dashboard', [DashboardController::class, 'getMarcas'])->middleware('auth')->name('dashboard');


//rutas para propiedades
Route::get('/propiedades', [PropertyController::class, 'index'])->middleware('auth')->name('propiedades.index');
Route::post('/propiedades', [PropertyController::class, 'store'])->middleware('auth')->name('propiedades.store');
Route::delete('/propiedades/{id}', [PropertyController::class, 'destroy'])->middleware('auth')->name('propiedades.destroy');
Route::put('/propiedades/{id}', [PropertyController::class, 'update'])->middleware('auth')->name('propiedades.update');
Route::get('/propiedades/details/{id_property}', [PropertyController::class, 'showDetail'])->middleware('auth')->name('propiedades.show');
Route::delete('/propiedades/imagen/{id}', [PropertyController::class, 'eliminarImagen'])->middleware('auth')->name('propiedades.imagen.delete');
Route::patch('/propiedades/imagen/{id}/portada', [GalleryController::class, 'setPortada'])->name('propiedades.imagen.portada');


// Ruta para marcas
Route::get('/marcas', [MarcaController::class, 'index'])->middleware('auth')->name('marcas.index');
Route::post('/marcas', [MarcaController::class, 'store'])->middleware('auth')->name('marcas.store');
Route::put('/marcas/{id}', [MarcaController::class, 'update'])->middleware('auth')->name('marcas.update');
Route::delete('/marcas/{id}', [MarcaController::class, 'changeStatus'])->middleware('auth')->name('marcas.changeStatus');

// Ruta para galería
Route::get('/galeria', [GalleryController::class, 'index'])->middleware('auth')->name('galeria.index');
Route::post('/admin/asignar-foto/{id}', [GalleryController::class, 'asignar'])->middleware('auth')->name('galeria.asignar');
Route::delete('/admin/eliminar-foto-temporal/{id}', [GalleryController::class, 'destroy'])->middleware('auth')->name('galeria.destroy');
