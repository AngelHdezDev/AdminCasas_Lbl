<?php

namespace App\Services;

use App\Repositories\SellerRepository;

class SellerService
{
    protected $repo;

    public function __construct(SellerRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllPaginated($perPage = 10)
    {
        return $this->repo->getAllPaginated($perPage);
    }


    public function storeSeller(array $data)
    {
        // Si el checkbox no viene, Laravel no manda nada, así que lo forzamos a 0 o 1
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        return $this->repo->store($data);
    }

    public function updateSeller($id, array $data)
    {
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        return $this->repo->update($id, $data);
    }
}