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
            $client = $this->clientService->createClient($request->validated());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente guardado con exito.',
                    'client' => [
                        'id' => $client->id,
                        'name' => $client->name,
                        'email' => $client->email,
                        'phone' => $client->phone,
                        'notes' => $client->notes,
                        'created_at' => $client->created_at->format('d/m/Y'),
                        'identification_path' => $client->identification_path,
                        'identification_url' => route('clientes.archivo', $client->id),
                        'show_url' => route('clientes.show', $client->id),
                        'delete_url' => route('clientes.destroy', $client->id),
                    ],
                ], 201);
            }

            return redirect()->route('clientes.index')
                ->with('success', '¡Cliente guardado con éxito!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el registro.'
                ], 500);
            }

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
            $clientModel = $this->clientService->updateClient($clientModel, $request->validated());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente actualizado correctamente.',
                    'client' => [
                        'id' => $clientModel->id,
                        'name' => $clientModel->name,
                        'email' => $clientModel->email,
                        'phone' => $clientModel->phone,
                        'notes' => $clientModel->notes,
                        'identification_path' => $clientModel->identification_path,
                        'identification_url' => route('clientes.archivo', $clientModel->id),
                    ],
                ]);
            }

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Este catch atrapa los errores de validación (como el correo duplicado)
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('edit_client_id', $client); // <--- Aquí pasas el ID que recibiste arriba

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrio un error inesperado.'
                ], 500);
            }

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
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente eliminado correctamente.'
                ]);
            }

            return redirect()->back()->with('success', 'Cliente eliminado correctamente.');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el cliente.'
            ], 500);
        }

        return redirect()->back()->with('error', 'No se pudo eliminar el cliente.');
    }

    public function deleteFile($id)
    {
        $client = Client::findOrFail($id);

        // El servicio se encarga de todo el proceso
        $this->clientService->deleteClientFile($client);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Identificacion eliminada del registro.',
                'client' => [
                    'id' => $client->id,
                    'identification_path' => null,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Identificación eliminada del registro.');
    }

    public function showDetail($client)
    {
        try {
            // 1. Buscamos al cliente por su ID usando el modelo correcto
            $clientModel = Client::findOrFail($client);

            // 2. Renombramos a '$client' para que coincida exactamente con la vista Blade
            $client = $clientModel;

            // 3. Retornamos la vista premium de detalles
            return view('clientes.show', compact('client'));

        } catch (\Exception $e) {
            // En caso de que busquen un ID que no exista, redirige con error
            return redirect()->route('clientes.index')
                ->with('error', 'El cliente solicitado no existe o fue eliminado.');
        }
    }
}
