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
        $seller = $this->service->updateSeller($seller, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendedor actualizado con exito.',
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'email' => $seller->email,
                    'phone' => $seller->phone,
                    'notes' => $seller->notes,
                    'contract_path' => $seller->contract_path,
                    'contract_url' => route('vendedores.archivo', $seller->id),
                ],
            ]);
        }

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
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendedor desactivado con exito.'
                ]);
            }

            return redirect()->back()->with('success', 'Vendedor desactivado con éxito.');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo procesar la solicitud.'
            ], 500);
        }

        return redirect()->back()->with('error', 'No se pudo procesar la solicitud.');
    }

    public function showDetail($seller)
    {
        try {
            // 1. Buscamos al cliente por su ID usando el modelo correcto
            $sellerModel = seller::findOrFail($seller);

            // 2. Renombramos a '$client' para que coincida exactamente con la vista Blade
            $seller = $sellerModel;

            // 3. Retornamos la vista premium de detalles
            return view('vendedores.show', compact('seller'));

        } catch (\Exception $e) {
            // En caso de que busquen un ID que no exista, redirige con error
            return redirect()->route('vendedores.index')
                ->with('error', 'El vendedor solicitado no existe o fue eliminado.');
        }
    }
}
