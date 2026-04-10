<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        // Pasamos todos los inputs ($request->all()) como filtros
        $clients = $this->clientService->getClientsForIndex(10, $request->all());

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

    // El $client (o $id) viene de la definición de tu ruta
    public function update(UpdateClientRequest $request, $client)
    {
        try {
            // 1. Buscamos al cliente
            $clientModel = Client::findOrFail($client);

            // 2. Procesamos la actualización
            $this->clientService->updateClient($clientModel, $request->validated());

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Este catch atrapa los errores de validación (como el correo duplicado)
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('edit_client_id', $client); // <--- Aquí pasas el ID que recibiste arriba

        } catch (\Exception $e) {
            // Este catch atrapa errores generales del sistema
            return redirect()->back()
                ->with('error', 'Ocurrió un error inesperado.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $result = $this->clientService->deleteClient($id);

        if ($result) {
            return redirect()->back()->with('success', 'Cliente eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo eliminar el cliente.');
    }

    public function deleteFile($id)
    {
        $client = Client::findOrFail($id);

        // El servicio se encarga de todo el proceso
        $this->clientService->deleteClientFile($client);

        return redirect()->back()->with('success', 'Identificación eliminada del registro.');
    }
}