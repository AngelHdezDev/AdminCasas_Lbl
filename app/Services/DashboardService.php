<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $repo;

    public function __construct(DashboardRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getDashboardData()
    {
        $stats = $this->repo->getBasicStats();
        $kpis = $this->repo->getKpis();

        return [
            // Stats Grid
            'totalPropiedades' => $stats['total_properties'],
            'valorVenta'       => $stats['sale_value'],
            'totalRenta'       => $stats['rent_count'],
            'totalDestacadas'  => $stats['featured_count'],
            
            // KPIs
            'totalSellers'       => $kpis['total_sellers'],
            'totalClients'       => $kpis['total_clients'],
            'propiedadesActivas' => $kpis['active_properties'],

            // Listados
            'propiedadesRecientes' => $this->repo->getRecentProperties(),
            'agentesTop'           => $this->repo->getTopSellers(),
            
            // Datos calculados (Lógica de negocio)
            'diffPropiedades' => 5.5, // Aquí podrías implementar la lógica de comparación mensual
            'rentaNuevas'     => 2,
            'diffValor'       => 1.2,
        ];
    }
}