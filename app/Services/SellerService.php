<?php

namespace App\Services;

use App\Repositories\SellerRepository;
use App\Models\Seller;
use Illuminate\Support\Facades\Storage;
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
        $data['is_active'] = 1;

        if (request()->hasFile('contract_path')) {
            $path = request()->file('contract_path')->store('contracts', 'local');
            $data['contract_path'] = $path;
        }

        return $this->repo->store($data);

    }
    public function updateSeller(Seller $seller, array $data)
    {
        // Manejo del checkbox
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        // Manejo del contrato
        if (request()->hasFile('contract_file')) {
            // Borrar el archivo anterior si existe
            if ($seller->contract_path && \Storage::disk('local')->exists($seller->contract_path)) {
                \Storage::disk('local')->delete($seller->contract_path);
            }

            // Guardar el nuevo contrato
            $data['contract_path'] = request()->file('contract_file')->store('contracts', 'local');
        }

        // Enviamos al repositorio para guardar los cambios
        return $this->repo->update($seller, $data);
    }

    
    public function deleteSellerFile($id)
    {
        $seller = $this->repo->find($id);

        if ($seller && $seller->contract_path) {
            // 1. Borrar archivo físico del disco 'local'
            if (Storage::disk('local')->exists($seller->contract_path)) {
                Storage::disk('local')->delete($seller->contract_path);
            }

            // 2. Limpiar el path en la base de datos a través del repositorio
            return $this->repo->update($id, ['contract_path' => null]);
        }

        return false;
    }
}