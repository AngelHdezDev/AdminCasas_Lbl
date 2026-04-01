<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Marca;
use App\Models\Auto;
use App\Http\Requests\StorePropertyRequest;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use Exception;
use Illuminate\Support\Facades\Log;


class PropertyController extends Controller
{
    protected $service;

    public function __construct(PropertyService $service)
    {
        $this->service = $service;
    }
  

    public function store(StorePropertyRequest $request): JsonResponse
    {

        $property = $this->service->createProperty($request->validated());

        return response()->json([
            'message' => 'Propiedad registrada con éxito',
            'data' => $property
        ], 201);
    }

    public function index()
    {
        $properties = $this->service->getAllPaginated(12);

        return view('autos.autos', compact('properties'));
    }
}
