<?php

namespace App\Http\Controllers;

use App\Logs;
use App\MedicalRequestStatus;
use App\Models\Batch;
use App\Models\InventoryLogs;
use App\Models\MedicalSupplies;
use App\Models\Patient;
use App\Models\softdeletesupplies;
use App\Models\SupplyRequest;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestPurpose;
use App\Models\TestRequest;
use function Pest\Laravel\get;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MedicalSupplyController extends Controller
{



    public function dashboardSupplyCreate(Request $request)
    {
        $supplies = MedicalSupplies::with('batches')->get();
        $inventoryLogs = InventoryLogs::with('medical_supplies:id,brand_name,quantity')->get();

        $thresholdDate = Carbon::now()->addDays(30);
        $nearlyExpired = Batch::with('medicalSupply')
            ->whereDate('expiration_date', '<=', $thresholdDate)
            ->whereDate('expiration_date', '>=', Carbon::today())
            ->get();

        return Inertia::render('Inventory/Dashboard', [
            'supplies' => $supplies,
            'inventory_logs' => $inventoryLogs,
            'nearlyExpired' => $nearlyExpired,
        ]);
    }


    public function inventory()
    {
        $nearlyExpired = MedicalSupplies::with(['batches' => function ($query) {
            $query->where('expiration_date', '<=', Carbon::now()->addDays(30));
        }])
            ->whereHas('batches', function ($query) {
                $query->where('expiration_date', '<=', Carbon::now()->addDays(30));
            })
            ->get();

        $supplies = MedicalSupplies::with('batches')->get();

        $inventoryLogs = InventoryLogs::with([
            'medical_supplies' => function ($query) {
                $query->select('id', 'brand_name', 'quantity');
            }
        ])->get();

        return Inertia::render('Inventory/Index', [
            'supplies'       => $supplies,
            'inventory_logs' => $inventoryLogs,
            'nearlyExpired'  => $nearlyExpired,
        ]);
    }


    public function batchNumbercreate(Request $request)
    {
        $supplies = MedicalSupplies::with('batches')->get();
        return Inertia::render('Inventory/Batches', [
            'supplies' => $supplies

        ]);
    }

    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'participants' => 'required|string|max:255',
        //     'brand_name' => 'required|string|max:255',
        //     'unit' => 'required|string|max:50',
        //     'quantity' => 'required|integer|min:0',
        //     'manufacture_date' => 'required|date',
        //     'expiration_date' => 'required|date|after_or_equal:manufacture_date',
        //     'lot_number' => 'nullable|string|max:255',
        //     'batch_number' => 'nullable|string|max:255',
        // ]);

        // $createdSupply = MedicalSupplies::create($validated);

        // InventoryLogs::create([
        //     'requester_name' => Auth::user()->name,
        //     'operation_type' => Logs::ADDED,
        //     'total_quantity' => $createdSupply->quantity,
        //     'supply_id' => $createdSupply->id,
        // ]);

        // return redirect()->back()->with('success', 'Supply added successfully.');

        $validated = $request->validate([
            'participants' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|integer|min:0',
            'manufacture_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:manufacture_date',
            'lot_number' => 'nullable|string|max:255',
            'batch_number' => 'required|string|max:255',
        ]);

        // Create main supply
        $createdSupply = MedicalSupplies::create([
            'participants'       => $validated['participants'],
            'brand_name'         => $validated['brand_name'],
            'unit'               => $validated['unit'],
            'quantity'           => $validated['quantity'],
            'manufacture_date'   => $validated['manufacture_date'],
            'expiration_date'    => $validated['expiration_date'],
            'lot_number'         => $validated['lot_number'], // ✅ Add this
            'batch_number'     => $validated['batch_number'],

        ]);

        // Create batch for the supply
        $createdSupply->batches()->create([
            'quantity'         => $validated['quantity'],
            'batch_number'     => $validated['batch_number'],
            'manufacture_date' => $validated['manufacture_date'],
            'expiration_date'  => $validated['expiration_date'],
        ]);

        // Log the inventory
        InventoryLogs::create([
            'requester_name' => Auth::user()->name,
            'operation_type' => Logs::ADDED,
            'total_quantity' => $validated['quantity'],
            'supply_id'      => $createdSupply->id,
        ]);

        return redirect()->back()->with('success', 'Supply and batch added successfully.');
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

    public function archiveSuppliescreate(Request $request)
    {

        return Inertia::render('Inventory/ArchiveSupplies');
    }

    public function renderAdminDashboard(Request $request)
    {
        $supplies = MedicalSupplies::with('batches')->get();
        $inventoryLogs = InventoryLogs::with('medical_supplies:id,brand_name,quantity')->get();

        $thresholdDate = Carbon::now()->addDays(30);
        $nearlyExpired = Batch::with('medicalSupply')
            ->whereDate('expiration_date', '<=', $thresholdDate)
            ->whereDate('expiration_date', '>=', Carbon::today())
            ->get();

        return Inertia::render('Admin/ItemDashboard', [
            'supplies' => $supplies,
            'inventory_logs' => $inventoryLogs,
            'nearlyExpired' => $nearlyExpired,
        ]);
    }

    // public function archiveSuppliesData($id)
    // {


    //     $supply = MedicalSupplies::with('batch')->findOrFail($id);
    //     $batch = $supply->batch;

    //     // Store into archived_supplies table
    //     softdeletesupplies::create([
    //         'medical_supply_id' => $supply->id,
    //         'batch_id' => $batch?->id,
    //         'manufacture_date' => $batch?->manufacture_date ?? now(),
    //         'expiration_date' => $batch?->expiration_date ?? now()->addYear(),
    //     ]);

    //     // Optional: delete original supply and/or batch
    //     $batch?->delete();
    //     $supply->delete();

    //     return redirect()->back()->with('message', 'Supply archived successfully.');
    // }
}
