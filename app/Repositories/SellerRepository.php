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

    public function getAll() {
        return Seller::orderBy('created_at', 'desc')->get();
    }

    public function store(array $data) {
        return Seller::create($data);
    }

    public function update($id, array $data) {
        $seller = Seller::findOrFail($id);
        $seller->update($data);
        return $seller;
    }
}