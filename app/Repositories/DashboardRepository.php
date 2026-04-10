<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\Seller;
use App\Models\Client;
use Carbon\Carbon;

class DashboardRepository
{
    public function getBasicStats()
    {
        return [
            'total_properties' => Property::count(),
            'sale_value' => Property::where('contract_type', 'venta')->sum('price'),
            'rent_count' => Property::where('contract_type', 'renta')->count(),
            'featured_count' => Property::where('is_featured', true)->count(),
        ];
    }

    public function getKpis()
    {
        return [
            'total_sellers' => Seller::count(),
            'total_clients' => Client::count(),
            'active_properties' => Property::where('status', 'activa')->count(),
        ];
    }

    // public function getRecentProperties($limit = 5)
    // {
    //     return Property::with('thumbnail') // Asumiendo que definiste esta relación
    //         ->latest()
    //         ->take($limit)
    //         ->get();
    // }

    public function getRecentProperties($limit = 5)
    {
        // Quitamos el 'with' para evitar errores de relación inexistente
        return Property::latest()
            ->take($limit)
            ->get();
    }

    public function getTopSellers($limit = 5)
    {
        return Seller::withCount('properties')
            ->withSum('properties', 'price')
            ->orderBy('properties_count', 'desc')
            ->take($limit)
            ->get();
    }
}