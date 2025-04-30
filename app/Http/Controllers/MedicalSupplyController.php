<?php

namespace App\Http\Controllers;

use App\Models\MedicalSupplies;
use App\Models\SupplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MedicalSupplyController extends Controller
{
    public function inventory()
    {
        $suppliesInventory = MedicalSupplies::all();

        return Inertia::render('Inventory/Index', [
            'supplies' => $suppliesInventory
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participants' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|integer|min:0',
            'manufacture_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:manufacture_date',
            'lot_number' => 'nullable|string|max:255',
        ]);

        MedicalSupplies::create($validated);

        return redirect()->back()->with('success', 'Supply added successfully.');
    }


    public function medicalSupplyRequest()
    {
        $supplyRequest = SupplyRequest::select('id', 'to', 'po_number', 'status', 'release_datetime')
            ->with([
                'medical_supplies' => function ($query) {
                    $query->select('medical_supplies.id', 'medical_supplies.participants', 'medical_supplies.brand_name', 'medical_supplies.unit')
                        ->withPivot('quantity');
                }
            ])
            ->get();

        Log::info("supplyRequest", [
            'supplyRequest' => $supplyRequest
        ]);


        $suppliesDropdownSelect = MedicalSupplies::select(
            'id',
            'brand_name',
            'unit',
            'quantity'
        )->get();

        return Inertia::render('Medical/MedicalSupplyRequest', [
            'medical_supply_requests' => $supplyRequest,
            'supplies_dropdown_select' => $suppliesDropdownSelect
        ]);
    }


    public function medicalSupplyRequestCreate(Request $request)
    {
        $validated = $request->validate([
            'po_number' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'supplies' => 'required|array|min:1',
            'supplies.*.id' => 'required|integer|exists:medical_supplies,id',
            'supplies.*.quantity' => 'required|integer|min:1',
        ]);

        $poNumber = $validated['po_number'];
        $to = $validated['to'];
        $supplies = $validated['supplies'];


        Log::info("Request Data", [
            'poNumber' => $poNumber,
            'to' => $to,
            'supplies' => $supplies,
        ]);

        $supplyRequest = SupplyRequest::create([
            'to'        => $to,
            'po_number' => $poNumber,
            'status'    => 'pending',
            'user_id'      => Auth::user()->id
        ]);


        // INSERT VALUE TO THE PIVOTED TABLE
        foreach ($supplies as $supply) {
            $supplyRequest->medical_supplies()->attach($supply['id'], [
                'quantity' => $supply['quantity']
            ]);
        }

        return redirect()->back()->with('success', 'Supply Request Submitted');
    }
}
