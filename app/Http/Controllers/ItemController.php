<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::with('supplier')
            ->where('is_active', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('category', 'like', "%$search%");
                });
            })
            ->orderBy('name')
            ->get();

        $lowStockCount = Item::where('is_active', true)
            ->whereColumn('quantity', '<=', 'min_stock')
            ->count();

                $suppliers = Supplier::orderBy('name')->get();

        // Items available for sale (active and in stock)
        $saleItems = Item::where('is_active', true)
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'quantity', 'unit_price']);

        return view('items.index', compact('items', 'search', 'lowStockCount', 'suppliers', 'saleItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:255',
            'quantity'    => 'required|integer|min:0',
            'unit_price'  => 'required|numeric|min:0',
            'min_stock'   => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Item and its opening stock record are saved together or not at all
        DB::transaction(function () use ($data) {
            $item = Item::create($data);

            if ($item->quantity > 0) {
                Transaction::create([
                    'item_id'     => $item->id,
                    'supplier_id' => $item->supplier_id,
                    'type'        => 'in',
                    'quantity'    => $item->quantity,
                    'date'        => now()->toDateString(),
                ]);
            }
        });

        return redirect('/items')->with('success', 'Item added.');
    }

    public function update(Request $request, Item $item)
    {
        // Quantity is not edited here. It changes only through Stock In / Stock Out / Sales.
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:255',
            'unit_price'  => 'required|numeric|min:0',
            'min_stock'   => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $item->update($data);

        return redirect('/items')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        if ($item->transactions()->exists() || $item->saleItems()->exists()) {
            $item->update(['is_active' => false]);

            return redirect('/items')
                ->with('success', 'Item has history, so it was marked inactive instead of deleted.');
        }

        $item->delete();

        return redirect('/items')->with('success', 'Item deleted.');
    }
}