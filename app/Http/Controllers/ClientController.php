<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClientRequest;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $clients = $this->clientService->getAllPaginated();
        return view('clientes.clientes', compact('clients'));
    }
    public function store(StoreClientRequest $request)
    {
        try {
            $this->clientService->createClient($request->validated());

            return redirect()->route('clientes.index')
                ->with('success', '¡Cliente guardado con éxito!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al procesar el registro.')
                ->withInput();
        }
    }

    public function destroy(Client $client)
    {
        try {
            $this->clientService->deleteClient($client);
            return redirect()->route('clients.index')->with('success', 'Cliente eliminado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}