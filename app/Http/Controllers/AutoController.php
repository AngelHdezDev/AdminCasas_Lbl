<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auto\StoreAutoRequest;
use App\Http\Requests\Auto\UpdateAutoRequest;
use App\Models\Auto;
use App\Models\Marca;
use App\Models\Imagen;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class AutoController extends Controller
{
    public function store(StoreAutoRequest $request)
    {
        try {

            $auto = Auto::create([
                'id_marca' => $request->id_marca,
                'modelo' => $request->modelo,
                'year' => $request->year,
                'color' => $request->color,
                'transmision' => $request->transmision,
                'combustible' => $request->combustible,
                'kilometraje' => $request->kilometraje,
                'precio' => $request->precio,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'ocultar_kilometraje' => $request->has('ocultar_kilometraje') ? 1 : 0,
                'consignacion' => $request->has('consignacion') ? 1 : 0,
                'created_by' => auth()->id(),
            ]);

            \Log::info('Vehículo creado:', ['id' => $auto->id]);

            return redirect()->back()->with('success', 'Vehículo registrado correctamente en el inventario.');

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error("Error de BD al registrar vehículo: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error en la base de datos: ' . $e->getMessage());

        } catch (Exception $e) {
            \Log::error("Error al registrar vehículo: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo guardar el vehículo. Revisa los datos e intenta de nuevo.');
        }
    }

    public function index(Request $request)
    {
        $query = Auto::with(['marca', 'thumbnail'])->active();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('modelo', 'LIKE', "%{$search}%")
                    ->orWhere('color', 'LIKE', "%{$search}%")
                    ->orWhere('year', 'LIKE', "%{$search}%")

                    ->orWhereHas('marca', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    });
            });
        }


        if ($request->filled('marca')) {
            $query->where('id_marca', $request->input('marca'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }


        if ($request->filled('consignacion')) {
            $query->where('consignacion', $request->input('consignacion'));
        }

        $vehiculos = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $marcas = Marca::where('active', 1)
            ->orderBy('nombre', 'asc')
            ->get();
        $tipos = Auto::select('tipo')->distinct()->orderBy('tipo', 'asc')->get();

        return view('autos.autos', compact('vehiculos', 'marcas', 'tipos'));
    }

    public function destroy($id)
    {
        try {

            $auto = Auto::where('id_auto', $id)->firstOrFail();


            $auto->update(['active' => 0]);


            return redirect()->route('autos.index')
                ->with('success', 'El vehículo ' . $auto->modelo . ' ha sido desactivado correctamente.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return redirect()->route('autos.index')
                ->with('error', 'No se encontró el vehículo que intentas desactivar.');

        } catch (Exception $e) {
            return redirect()->route('autos.index')
                ->with('error', 'Ocurrió un fallo al intentar desactivar el vehículo: ' . $e->getMessage());
        }
    }

    public function update(UpdateAutoRequest $request, $id)
    {

        try {

            $vehiculo = Auto::where('id_auto', $id)->firstOrFail();


            $data = $request->validated();
            \Log::info('Datos recibidos:', $data);


            $data['ocultar_kilometraje'] = $request->has('ocultar_kilometraje') ? 1 : 0;
            $data['consignacion'] = $request->has('consignacion') ? 1 : 0;
            // $data['active'] = $request->has('active') ? 1 : 0; 

            if ($request->hasFile('imagen')) {
                if ($vehiculo->imagen && file_exists(public_path($vehiculo->imagen))) {
                    unlink(public_path($vehiculo->imagen));
                }
                $file = $request->file('imagen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/autos'), $filename);
                $data['imagen'] = 'uploads/autos/' . $filename;
            }

            $vehiculo->update($data);

            return redirect()->route('autos.index')
                ->with('success', 'El vehículo ' . $vehiculo->modelo . ' ha sido actualizado correctamente.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('autos.index')
                ->with('error', 'No se encontró el vehículo para actualizar.');

        } catch (Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar la actualización: ' . $e->getMessage());
        }
    }

    public function showDetail($id_auto)
    {
        // Buscamos el auto por su llave primaria personalizada
        // Usamos findOrFail para que si no existe, mande un error 404 automáticamente
        $auto = Auto::with(['marca', 'imagenes'])->findOrFail($id_auto);

        return view('autos.autosDetail', compact('auto'));
    }


    public function eliminarImagen($id)
    {
        try {
            $imagen = Imagen::findOrFail($id);

            if ($imagen->ruta_imagen && Storage::disk('public')->exists($imagen->ruta_imagen)) {
                Storage::disk('public')->delete($imagen->ruta_imagen);
            }

            $imagen->delete();

            return back()->with('success', 'Imagen eliminada correctamente.');

        } catch (Exception $e) {
            return back()->with('error', 'No se pudo eliminar la imagen: ' . $e->getMessage());
        }
    }

}
