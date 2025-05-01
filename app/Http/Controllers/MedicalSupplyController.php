<?php

namespace App\Http\Controllers;

use App\Logs;
use App\MedicalRequestStatus;
use App\Models\InventoryLogs;
use App\Models\MedicalSupplies;
use App\Models\SupplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

use function Pest\Laravel\get;

class MedicalSupplyController extends Controller
{

    // INVENTORY OFFICER SIDE METHODS
    public function inventory()
    {
        $suppliesInventory = MedicalSupplies::all();
        $inventoryLogs = InventoryLogs::with([
            'medical_supplies' => function ($query) {
                $query->select('id', 'brand_name', 'quantity');
            }
        ])
            ->get();

        return Inertia::render('Inventory/Index', [
            'supplies'       => $suppliesInventory,
            'inventory_logs' => $inventoryLogs
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

        $createdSupply = MedicalSupplies::create($validated);

        InventoryLogs::create([
            'requester_name' => Auth::user()->name,
            'operation_type' => Logs::ADDED,
            'total_quantity' => $createdSupply->quantity,
            'supply_id' => $createdSupply->id,
        ]);

        return redirect()->back()->with('success', 'Supply added successfully.');
    }



    public function requests()
    {
        $supplyRequest = SupplyRequest::select('id', 'to', 'po_number', 'status', 'remarks', 'release_datetime')
            ->with([
                'medical_supplies' => function ($query) {
                    $query->select('medical_supplies.id', 'medical_supplies.participants', 'medical_supplies.brand_name', 'medical_supplies.unit')
                        ->withPivot('quantity');
                }
            ])
            ->get();

        return Inertia::render('Inventory/Request', [
            'medical_supply_requests' => $supplyRequest
        ]);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'release_datetime' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
            'request_id' => ['required', 'exists:supply_requests,id'],
        ]);


        $supplyRequest = SupplyRequest::with(['medical_supplies', 'users'])->find($validated['request_id']);

        $supplyRequest->release_datetime = $validated['release_datetime'];
        $supplyRequest->remarks = $validated['remarks'];
        $supplyRequest->status = MedicalRequestStatus::RELEASE->value;
        $supplyRequest->save();


        // THE AUTOMATED DEDUCTION AFTER SAVING STATUS
        foreach ($supplyRequest->medical_supplies as $medicalSupply) {
            $quantity = $medicalSupply->pivot->quantity;

            Log::info("Pivot quantity" . $quantity);

            $medicalSupply->quantity -= $quantity;
            $medicalSupply->save();


            // LOG EACH OPERATION
            InventoryLogs::create([
                'requester_name' => $supplyRequest->users->name,
                'operation_type' => Logs::DEDUCTED,
                'total_quantity' => $quantity,
                'supply_id' => $medicalSupply->id,
            ]);
        }

        return redirect()->back()->with('success', 'Supply updated successfully.');
    }



    // MEDICAL STAFF SIDE METHODS
    public function medicalSupplyRequest()
    {
        $supplyRequest = SupplyRequest::select('id', 'to', 'po_number', 'status', 'remarks', 'release_datetime')
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
            'status'    => MedicalRequestStatus::PENDING->value,
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
