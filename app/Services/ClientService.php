<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Exception;

class ClientService
{
    /**
     * Lógica para registrar un nuevo dueño (Cliente)
     */
    public function createClient(array $data)
    {
        return Client::create($data);
    }

    /**
     * Actualizar datos del cliente
     */
    public function updateClient(Client $client, array $data)
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($data);
            return $client;
        });
    }

    /**
     * Validar si se puede eliminar (Regla de negocio)
     */
    public function deleteClient(Client $client)
    {
        // Regla: No eliminar si tiene propiedades consignadas
        if ($client->properties()->count() > 0) {
            throw new Exception("No se puede eliminar un cliente con propiedades activas.");
        }

        return $client->delete();
    }

    public function getAllPaginated($perPage = 10)
    {
        // Usamos eager loading de properties por si quieres mostrar cuántas casas tiene cada uno
        return Client::withCount('properties')
            ->latest()
            ->paginate($perPage);
    }
}