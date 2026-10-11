<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        // Default range: first day of this month until today
        $from = $request->input('from') ?: now()->startOfMonth()->toDateString();
        $to   = $request->input('to')   ?: now()->toDateString();

        // ---------- Sales ----------
        $sales = Sale::with('items.item')
            ->whereBetween('sale_date', [$from, $to])
            ->orderBy('sale_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $salesCount   = $sales->count();
        $totalRevenue = $sales->sum('total_amount');

        // Sales by item (bar chart)
        $byItem = SaleItem::selectRaw('item_id, SUM(subtotal) as revenue')
            ->whereHas('sale', function ($q) use ($from, $to) {
                $q->whereBetween('sale_date', [$from, $to]);
            })
            ->groupBy('item_id')
            ->orderByDesc('revenue')
            ->with('item')
            ->get();

        $itemLabels = $byItem->map(fn ($row) => $row->item->name)->values();
        $itemValues = $byItem->map(fn ($row) => (float) $row->revenue)->values();

        // ---------- Stock movements ----------
        $transactions = Transaction::with(['item', 'supplier'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $stockIn  = $transactions->where('type', 'in')->sum('quantity');
        $stockOut = $transactions->where('type', 'out')->sum('quantity');

        // ---------- Current inventory ----------
        $items = Item::with('supplier')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Valuation = quantity x current unit price
        $totalValue    = $items->sum(fn ($item) => $item->quantity * $item->unit_price);
        $lowStockItems = $items->filter(fn ($item) => $item->isLowStock());

        // Stock value by category (doughnut chart)
        $byCategory = $items
            ->groupBy(fn ($item) => $item->category ?: 'Uncategorized')
            ->map(fn ($group) => (float) $group->sum(fn ($i) => $i->quantity * $i->unit_price));

        $categoryLabels = $byCategory->keys()->values();
        $categoryValues = $byCategory->values();

        return view('reports.index', compact(
            'from', 'to',
            'sales', 'salesCount', 'totalRevenue', 'itemLabels', 'itemValues',
            'transactions', 'stockIn', 'stockOut',
            'items', 'totalValue', 'lowStockItems',
            'categoryLabels', 'categoryValues'
        ));
    }
}