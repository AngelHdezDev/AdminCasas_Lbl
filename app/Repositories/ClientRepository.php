<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository
{
    public function getAllPaginated($perPage)
    {
        return Client::withCount('properties')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update(Client $client, array $data)
    {
        $client->update($data);
        return $client;
    }

    public function delete(Client $client)
    {
        return $client->delete();
    }
    
    public function hasProperties(Client $client)
    {
        return $client->properties()->count() > 0;
    }
}