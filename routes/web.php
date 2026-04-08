<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Storage;


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


Route::get('/galeria', [GaleriaController::class, 'index'])->name('galeria.index');
Route::post('/galeria', [GaleriaController::class, 'store'])->name('galeria.store');
Route::post('/galeria/asignar/{id}', [GaleriaController::class, 'asignar'])->name('galeria.asignar');
Route::delete('/galeria/{id}', [GaleriaController::class, 'destroy'])->name('galeria.destroy');

Route::get('/clientes', [ClientController::class, 'index'])->middleware('auth')->name('clientes.index');
Route::post('/clientes', [ClientController::class, 'store'])->middleware('auth')->name('clientes.store');
Route::put('/clientes/{client}', [ClientController::class, 'update'])->middleware('auth')->name('clientes.update');
Route::delete('/clientes/{client}', [ClientController::class, 'destroy'])->middleware('auth')->name('clientes.destroy');

Route::get('/api/consulta-cp/{cp}', [App\Http\Controllers\PostalCodeController::class, 'consultaCP']);

Route::get('/clientes/archivo/{id}', function ($id) {
    $cliente = \App\Models\Client::findOrFail($id);

    if ($cliente->identification_path && Storage::disk('local')->exists($cliente->identification_path)) {
        return Storage::disk('local')->response($cliente->identification_path);
    }

    abort(404, "Archivo no encontrado");
})->middleware('auth')->name('clientes.archivo');