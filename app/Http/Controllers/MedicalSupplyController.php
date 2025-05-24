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
use App\Models\Patient;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestPurpose;
use App\Models\TestRequest;
use Illuminate\Validation\Rule;

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
        $supplyRequest->status = MedicalRequestStatus::RECEIVED->value;
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
    public function inventorySupplyRequest()
    {
        $supplyRequest = SupplyRequest::select('id', 'to', 'po_number', 'status')
            ->with([
                'requested_supply' => function ($query) {
                    $query->select('id', 'request_id', 'quantity', 'unit', 'item_description', 'unit_price', 'total_price');
                }
            ])
            ->get();


        Log::info("supplyRequest", [
            'supplyRequest' => $supplyRequest
        ]);



        return Inertia::render('Medical/MedicalSupplyRequest', [
            'medical_supply_requests' => $supplyRequest,
        ]);
    }


    public function updateRequestStatus(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|integer|exists:supply_requests,id',
            'status_tag' => ['required', 'string', Rule::in(['pending', 'received'])],
        ]);

        Log::info("Status: ", $validated);


        $supplyRequest = SupplyRequest::findOrFail($validated['request_id']);
        $supplyRequest->status = (string) $validated['status_tag'];
        $supplyRequest->save();

        return back()->with('success', 'Supply request status updated successfully.');
    }


    public function medicalSupplyRequestCreate(Request $request)
    {
        $validated = $request->validate([
            'po_number' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.item_description' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
        ]);

        $poNumber = $validated['po_number'];
        $to = $validated['to'];
        $items = $validated['items'];


        Log::info("Request Data", [
            'poNumber' => $poNumber,
            'to' => $to,
            'items' => $items,
        ]);

        $supplyRequest = SupplyRequest::create([
            'to'        => $to,
            'po_number' => $poNumber,
            'status'    => MedicalRequestStatus::PENDING->value,
            'user_id'      => Auth::user()->id
        ]);

        $supplyRequest->requested_supply()->createMany($validated['items']);

        return redirect()->back()->with('success', 'Supply Request Submitted');
    }

    public function patientDetailscreate()
    {
        $patientsDetails = Patient::all();
        $testTypesPurpose = TestPurpose::all();
        $testTypesRequest = TestRequest::all();
        $testCategory = TestCategory::with('testTypes')->get();


        return Inertia::render('Patient/PatientDetails', [
            'patients' => $patientsDetails,
            'testTypesPurpose' => $testTypesPurpose,
            'testTypesRequest' => $testTypesRequest,
            'testCategory' => $testCategory,
        ]);
    }

    public function patientDetailsStore(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:patients,email',
        ]);
        $patient = Patient::create($validated);

        return redirect()->back()->with('success', 'Patient details added successfully.');
    }
}
