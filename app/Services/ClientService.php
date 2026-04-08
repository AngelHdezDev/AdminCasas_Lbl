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

    public function deleteClient($client)
    {
        // Regla de negocio: Consultamos al repo si tiene propiedades
        if ($this->repository->hasProperties($client)) {
            throw new Exception("No se puede eliminar un cliente con propiedades activas.");
        }

        // Si tenía un archivo de identificación, lo borramos del disco al eliminar al cliente
        if ($client->identification_path) {
            Storage::disk('local')->delete($client->identification_path);
        }

        return $this->repository->delete($client);
    }

    public function getAllPaginated($perPage = 10)
    {
        return $this->repository->getAllPaginated($perPage);
    }
}