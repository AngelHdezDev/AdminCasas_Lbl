<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\Auto;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function getMarcas()
    {
        try {

            $marcas = Marca::orderBy('nombre', 'asc')->get();
            $totalVehiculos = Auto::where('active', true)->count();
            $valorInventario = Auto::where('active', true)->sum('precio');
            $totalConsignacion = Auto::where('consignacion', true)->where('active', true)->count();
            $totalMarcas = Marca::where('active', true)->count();

            // --- 2. PERIODOS DE TIEMPO ---
            $inicioMesActual = \Carbon\Carbon::now()->startOfMonth();
            $inicioMesPasado = \Carbon\Carbon::now()->subMonth()->startOfMonth();
            $finMesPasado = \Carbon\Carbon::now()->subMonth()->endOfMonth();
            $inicioTrimestrePasado = \Carbon\Carbon::now()->subMonths(3)->startOfMonth();

            // --- 3. CÁLCULOS DINÁMICOS ---

            // A. Vehículos: Comparación Mes a Mes
            $conteoMesPasado = Auto::where('active', true)
                ->whereBetween('created_at', [$inicioMesPasado, $finMesPasado])
                ->count();

            // Si el mes pasado hubo 0, el crecimiento es 0% (o 100% si prefieres indicar que todo es nuevo)
            $diffVehiculos = ($conteoMesPasado > 0)
                ? (($totalVehiculos - $conteoMesPasado) / $conteoMesPasado) * 100
                : ($totalVehiculos > 0 ? 100 : 0);

            // B. Inventario: Comparación Trimestral
            $valorTrimestrePasado = Auto::where('active', true)
                ->where('created_at', '<', $inicioTrimestrePasado)
                ->sum('precio');

            $diffInventario = ($valorTrimestrePasado > 0)
                ? (($valorInventario - $valorTrimestrePasado) / $valorTrimestrePasado) * 100
                : ($valorInventario > 0 ? 100 : 0);

            // C. Consignación: Nuevos en los últimos 7 días
            $consignacionNuevos = Auto::where('consignacion', true)
                ->where('active', true)
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
                ->count();

            // D. Marcas: Desactivadas en el mes en curso
            $marcasDescontinuadas = Marca::where('active', false)
                ->where('created_at', '>=', $inicioMesActual)
                ->count();

            // --- 4. LISTADOS ---
            $vehiculosRecientes = Auto::with('thumbnail')
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $marcasTop = Marca::where('active', true)
                ->withCount([
                    'autos' => function ($query) {
                        $query->where('active', true);
                    }
                ])
                ->withSum([
                    'autos' => function ($query) {
                        $query->where('active', true);
                    }
                ], 'precio')
                ->orderBy('autos_sum_precio', 'desc')
                ->limit(5)
                ->get();

            $actividades = ActivityLog::orderBy('created_at', 'desc')->limit(10)->get();

            return view('dashboard.dashboard', compact(
                'marcas',
                'totalVehiculos',
                'diffVehiculos',
                'valorInventario',
                'diffInventario',
                'totalConsignacion',
                'consignacionNuevos',
                'totalMarcas',
                'marcasDescontinuadas',
                'vehiculosRecientes',
                'marcasTop',
                'actividades'
            ));

        } catch (\Exception $e) {
            dd("Error técnico en Dashboard: " . $e->getMessage());
        }
    }
}
