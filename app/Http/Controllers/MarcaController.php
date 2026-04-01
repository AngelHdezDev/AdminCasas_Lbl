<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Marca;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use Exception;
use Illuminate\Support\Facades\Log;


class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $query = Marca::query();

            $query->withCount([
                'autos' => function ($query) {
                    $query->where('active', 1);
                }
            ]);


            if ($request->has('search') && $request->search != '') {
                $query->where('nombre', 'LIKE', '%' . $request->search . '%');
            }


            $marcas = $query->latest()->paginate(12);

            return view('marcas.marcas', compact('marcas'));

        } catch (Exception $e) {
            \Log::error("Error al cargar marcas: " . $e->getMessage());

            return back()->with('error', 'Error al cargar las marcas.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarcaRequest $request)
    {
        try {
            $rutaBaseDatos = null;

            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('marcas', 'public');
                $rutaBaseDatos = $path;
            }

            Marca::create([
                'nombre' => $request->nombre,
                'imagen' => $rutaBaseDatos,
                'created_by' => auth()->id()
            ]);

            return back()->with('success', '¡Marca registrada exitosamente!');

        } catch (Exception $e) {
            Log::error("Error al guardar marca: " . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error al guardar la marca.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarcaRequest $request, $id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $marca->nombre = $request->nombre;

            // Si el usuario sube una nueva imagen
            if ($request->hasFile('imagen')) {

                // 1. Borrar la imagen anterior si existe en el disco public
                if ($marca->imagen && \Storage::disk('public')->exists($marca->imagen)) {
                    \Storage::disk('public')->delete($marca->imagen);
                }

                // 2. Guardar la nueva imagen en 'storage/app/public/marcas'
                $path = $request->file('imagen')->store('marcas', 'public');

                // 3. Actualizar la ruta en la base de datos (guardará 'marcas/nombre.jpg')
                $marca->imagen = $path;
            }

            $marca->save();

            return redirect()->route('marcas.index')
                ->with('success', 'La marca se ha actualizado correctamente.');

        } catch (Exception $e) {
            \Log::error("Error al actualizar marca: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la marca.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function changeStatus($id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $marca->active = ($marca->active == 1) ? 0 : 1;
            $marca->save();
            $mensaje = $marca->active == 1 ? 'Marca activada correctamente.' : 'Marca desactivada correctamente.';
            return back()->with('success', $mensaje);

        } catch (Exception $e) {
            return back()->with('error', 'Ocurrió un error al cambiar el estado: ' . $e->getMessage());
        }
    }
}
