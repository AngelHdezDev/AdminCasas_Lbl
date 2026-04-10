<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository
{
    public function getAllPaginated($perPage = 10, array $filters = [])
    {
        return Client::query()
            ->where('is_active', 1)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage)
            ->appends($filters);
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

    public function deactivate($id)
    {
        $client = Client::find($id);
        if ($client) {
            $client->is_active = 0;
            return $client->save();
        }
        return false;
    }

    public function hasProperties(Client $client)
    {
        return $client->properties()->count() > 0;
    }
}