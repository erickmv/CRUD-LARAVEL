<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Dashboard principal
 * @author Erick Adrian Mendez Villalpando
 */
class DashboardController extends Controller
{
    /**
     * Generar estadísticas del dashboard
     */
    public function index()
    {
        // Contadores básicos
        $totalClients = Client::count();
        $totalProducts = Product::count();
        $activeClients = Client::where('active', true)->count();
        $totalStock = Product::sum('stock');
        $totalValue = Product::sum(\DB::raw('price * stock'));

        // Gráfico de clientes por mes (últimos 6 meses)
        $clientsByMonth = Client::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Gráfico de productos por mes
        $productsByMonth = Product::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top productos por stock
        $topProducts = Product::orderBy('stock', 'desc')->limit(5)->get();

        // Status de clientes
        $clientsStatus = [
            'active' => Client::where('active', true)->count(),
            'inactive' => Client::where('active', false)->count()
        ];

        return view('dashboard', compact(
            'totalClients',
            'totalProducts', 
            'activeClients',
            'totalStock',
            'totalValue',
            'clientsByMonth',
            'productsByMonth',
            'topProducts',
            'clientsStatus'
        ));
    }
}