<?php

namespace App\Http\Controllers;

use App\Services\SellerService;
use Illuminate\Http\Request;
use App\Models\Seller;

class SellerController extends Controller
{
    protected $service;

    public function __construct(SellerService $service) {
        $this->service = $service;
    }

    public function index()
    {
        $sellers = $this->service->getAllPaginated();
        return view('vendedores.vendedores', compact('sellers'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:sellers',
            'phone' => 'nullable'
        ]);

        $this->service->storeSeller($data);
        return back()->with('success', 'Vendedor guardado');
    }
}