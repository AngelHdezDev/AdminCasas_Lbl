<?php

namespace App\Repositories;

use App\Models\Seller;

class SellerRepository
{
    public function getAllPaginated($perPage, array $filters = [])
    {
        return Seller::withCount('properties')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends($filters); 
    }

    public function getAll()
    {
        return Seller::orderBy('created_at', 'desc')->get();
    }

    public function store(array $data)
    {
        return Seller::create($data);
    }

    public function find($id)
    {
        // Cambia $this->model por Seller
        return Seller::find($id);
    }

    public function update(Seller $seller, array $data)
    {
        $seller->update($data);
        return $seller;

    }


}