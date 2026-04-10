<?php

namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\Storage;
use Exception;

class ClientService
{
    protected $repository;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createClient(array $data)
    {
        // Lógica de Storage: Guardar la identificación
        if (isset($data['identification_path'])) {
            $data['identification_path'] = $data['identification_path']->store('identification_client', 'local');
        }

        return $this->repository->create($data);
    }

    public function updateClient($client, array $data)
    {
        if (isset($data['identification_path']) && $data['identification_path'] instanceof \Illuminate\Http\UploadedFile) {
            if ($client->identification_path && Storage::disk('local')->exists($client->identification_path)) {
                Storage::disk('local')->delete($client->identification_path);
            }
            $data['identification_path'] = $data['identification_path']->store('identification_client', 'local');

        } else {
            unset($data['identification_path']);
        }

        return $this->repository->update($client, $data);
    }

    public function deleteClient($id)
    {
        return $this->repository->deactivate($id);
    }

    public function getClientsForIndex($perPage = 10, array $filters = [])
    {
        return $this->repository->getAllPaginated($perPage, $filters);
    }

    public function deleteClientFile($client)
    {
        // 1. Borrar el archivo físico si existe
        if ($client->identification_path && Storage::disk('local')->exists($client->identification_path)) {
            Storage::disk('local')->delete($client->identification_path);
        }

        // 2. Actualizar la base de datos poniendo el campo en null
        return $this->repository->update($client, [
            'identification_path' => null
        ]);
    }
}