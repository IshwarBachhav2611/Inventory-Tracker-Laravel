<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id'     => 'required|exists:items,id',
            'type'        => 'required|in:in,out',
            'quantity'    => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date'        => 'required|date',
        ]);

        $error = null;

        DB::transaction(function () use ($data, &$error) {
            // lockForUpdate stops two requests from changing the same item at once
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            if ($data['type'] === 'out' && $data['quantity'] > $item->quantity) {
                $error = 'Not enough stock. Available: ' . $item->quantity;
                return;
            }

            if ($data['type'] === 'in') {
                $item->quantity += $data['quantity'];
            } else {
                $item->quantity -= $data['quantity'];
            }
            $item->save();

            Transaction::create([
                'item_id'     => $item->id,
                // supplier is only for Stock In
                'supplier_id' => $data['type'] === 'in' ? ($data['supplier_id'] ?? null) : null,
                'type'        => $data['type'],
                'quantity'    => $data['quantity'],
                'date'        => $data['date'],
            ]);
        });

        if ($error) {
            return redirect('/items')->with('error', $error);
        }

        return redirect('/items')->with('success', 'Stock updated.');
    }
}