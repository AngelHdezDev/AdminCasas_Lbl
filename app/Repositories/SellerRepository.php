<?php

namespace App\Repositories;

use App\Models\Seller;

class SellerRepository
{
    public function getAllPaginated($perPage)
    {
        return Seller::withCount('properties')
            ->latest()
            ->paginate($perPage);
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