<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostalCodeController extends Controller
{
    //
    public function getPostalCode($cp)
    {
        try {
            $response = Http::timeout(5)->get("https://mexico-api.devaleff.com/api/codigo-postal/{$cp}");

            if ($response->failed()) {
                return response()->json(['error' => 'No se encontró el CP'], 404);
            }

            return $response->json();

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error de conexión con el proveedor'], 500);
        }
    }
}
