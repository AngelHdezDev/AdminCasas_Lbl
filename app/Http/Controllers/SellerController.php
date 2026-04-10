<?php

namespace App\Http\Controllers;

use App\Services\SellerService;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Http\Requests\StoreSellerRequest;
use App\Http\Requests\UpdateSellerRequest;

class SellerController extends Controller
{
    protected $service;

    public function __construct(SellerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $sellers = $this->service->getAllPaginated(10, $request->all());

        return view('vendedores.vendedores', compact('sellers'));
    }

    public function store(StoreSellerRequest $request)
    {
        $this->service->storeSeller($request->validated());
        return redirect()->back()->with('success', 'Vendedor guardado correctamente.');
    }

    public function update(UpdateSellerRequest $request, Seller $seller)
    {
        // Pasamos el objeto directamente al servicio
        $this->service->updateSeller($seller, $request->validated());

        return redirect()->back()->with('success', 'Vendedor actualizado con éxito.');
    }

    public function deleteFile($id)
    {
        $success = $this->service->deleteSellerFile($id);

        if ($success) {
            return redirect()->back()->with('success', 'Archivo eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo eliminar el archivo.');
    }

    public function destroy($id)
    {
        $result = $this->service->deleteSeller($id);

        if ($result) {
            return redirect()->back()->with('success', 'Vendedor desactivado con éxito.');
        }

        return redirect()->back()->with('error', 'No se pudo procesar la solicitud.');
    }
}