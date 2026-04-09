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

    public function index()
    {
        $sellers = $this->service->getAllPaginated();
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
}