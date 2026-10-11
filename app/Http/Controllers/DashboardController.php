<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems     = Item::where('is_active', true)->count();
        $totalSuppliers = Supplier::count();

        $lowStockCount = Item::where('is_active', true)
            ->whereColumn('quantity', '<=', 'min_stock')
            ->count();

        // Sales total for the current month
        $monthlySales = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');

        // Latest 5 stock movements and latest 5 sales
        $recentTransactions = Transaction::with(['item', 'supplier'])
            ->latest('id')
            ->take(5)
            ->get();

        $recentSales = Sale::with('items.item')
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalItems',
            'totalSuppliers',
            'lowStockCount',
            'monthlySales',
            'recentTransactions',
            'recentSales'
        ));
    }
}