<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_date'              => 'required|date',
            'items'                  => 'required|array|min:1',
            'items.*.item_id'        => 'required|exists:items,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.selling_price'  => 'required|numeric|min:0',
        ]);

        try {
            // If anything fails inside, everything is rolled back
            DB::transaction(function () use ($data) {
                $sale  = Sale::create(['sale_date' => $data['sale_date'], 'total_amount' => 0]);
                $total = 0;

                foreach ($data['items'] as $row) {
                    // Lock the item row so stock cannot change while we check it
                    $item = Item::lockForUpdate()->findOrFail($row['item_id']);

                    if (!$item->is_active) {
                        throw new \RuntimeException($item->name . ' is inactive.');
                    }

                    if ($row['quantity'] > $item->quantity) {
                        throw new \RuntimeException(
                            'Not enough stock for ' . $item->name . '. Available: ' . $item->quantity
                        );
                    }

                    $subtotal = $row['quantity'] * $row['selling_price'];

                    SaleItem::create([
                        'sale_id'       => $sale->id,
                        'item_id'       => $item->id,
                        'quantity'      => $row['quantity'],
                        'selling_price' => $row['selling_price'],   // price at the time of sale
                        'subtotal'      => $subtotal,
                    ]);

                    $item->quantity -= $row['quantity'];
                    $item->save();

                    $total += $subtotal;
                }

                $sale->update(['total_amount' => $total]);
            });
        } catch (\RuntimeException $e) {
            return redirect('/items')->with('error', $e->getMessage());
        }

        return redirect('/items')->with('success', 'Sale recorded.');
    }
}