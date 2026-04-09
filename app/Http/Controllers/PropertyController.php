<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Marca;
use App\Models\Property;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Models\Seller;
use App\Models\Client;


class PropertyController extends Controller
{
    protected $service;

    public function __construct(PropertyService $service)
    {
        $this->service = $service;
    }


    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = $this->service->createProperty($request->validated());

        return redirect()->route('propiedades.index')
            ->with('success', 'Propiedad registrada con éxito');
    }

    public function index(Request $request)
    {
        $properties = $this->service->getAllPaginated(10, $request->all());
        $vendedores = Seller::orderBy('name', 'asc')->get();
        $clientes = Client::orderBy('name', 'asc')->get();

        return view('autos.autos', compact('properties', 'vendedores', 'clientes'));
    }
    public function update(UpdatePropertyRequest $request, $id): RedirectResponse
    {
        // Buscamos la propiedad manualmente por el ID de la ruta
        $property = Property::findOrFail($id);

        // Pasamos el modelo encontrado al servicio
        $this->service->updateProperty($property, $request->validated());

        return redirect()->route('propiedades.index')
            ->with('success', 'Propiedad actualizada con éxito');
    }

    public function showDetail($id_property)
    {
        $property = Property::findOrFail($id_property);

        return view('autos.autosDetail', compact('property'));
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'active' => false
        ]);

        return redirect()->route('propiedades.index')
            ->with('success', 'La propiedad ha sido dada de baja correctamente.');
    }
}
