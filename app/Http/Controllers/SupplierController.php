<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSupplier($request);

        Supplier::create($data);

        return redirect('/suppliers')->with('success', 'Supplier added.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $this->validateSupplier($request);

        $supplier->update($data);

        return redirect('/suppliers')->with('success', 'Supplier updated.');
    }

   public function destroy(Supplier $supplier)
    {
        if ($supplier->transactions()->exists() || $supplier->items()->exists()) {
            return redirect('/suppliers')
                ->with('error', 'This supplier is linked to items or stock records and cannot be deleted.');
        }

        $supplier->delete();

        return redirect('/suppliers')->with('success', 'Supplier deleted.');
    }

    private function validateSupplier(Request $request)
    {
        return $request->validate([
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);
    }
}