<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        try {
            $this->clientService->storeClient($validated);
            return redirect()->back()->with('success', 'Cliente registrado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar cliente.');
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