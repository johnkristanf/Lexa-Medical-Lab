<?php

namespace App\Http\Controllers;

use App\Logs;
use App\MedicalRequestStatus;
use App\Models\archive_supplies;
use App\Models\Batch;
use App\Models\Categories;
use App\Models\InventoryLogs;
use App\Models\MedicalSupplies;
use App\Models\Patient;
use App\Models\RequestedSupply;
use App\Models\Stock;
use App\Models\SupplyRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use function Pest\Laravel\get;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MedicalSupplyController extends Controller
{
    public function printPDFReport()
    {
        $supplies = MedicalSupplies::with('batches')->get();

        $logobaselexa = $this->getLogolexaInventoryPDF();

        $pdf = Pdf::loadView('pdf.inventory_report', [
            'supplies' => $supplies,
            'logobaselexa'=>$logobaselexa
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('inventory_report.pdf');
    }

     private function getLogolexaInventoryPDF()
    {
        $logoPath = public_path('img/lexa-logo-removedbg.png');

        if (file_exists($logoPath)) {
            $imageData = base64_encode(file_get_contents($logoPath));
            $mimeType = mime_content_type($logoPath);
            return 'data:' . $mimeType . ';base64,' . $imageData;
        }

        return null;
    }

    public function renderAdminDashboard(Request $request)
    {
        $lowStockSupplies = MedicalSupplies::with(['stocks', 'batches'])
            ->get()
            ->filter(function ($supply) {
                $critical = $supply->stocks->first()?->critical_stock ?? 10;

                return $supply->quantity <= $critical;
            })
            ->values();

        $inventoryLogs = InventoryLogs::with('medical_supplies:id,brand_name,quantity')->get();

        $thresholdDate = Carbon::now()->addDays(30);

        $nearlyExpired = Batch::with('medicalSupply')
            ->whereHas('medicalSupply', fn ($q) => $q->whereNull('deleted_at'))
            ->whereDate('expiration_date', '<=', $thresholdDate)
            ->whereDate('expiration_date', '>=', Carbon::today())
            ->get();

        $fiveLatestPatients = Patient::latest()
        ->take(5)
        ->get(['id', 'patient_id', 'first_name', 'last_name', 'created_at']);

        return Inertia::render('Admin/ItemDashboard', [
            'supplies' => $lowStockSupplies,
            'inventory_logs' => $inventoryLogs,
            'nearlyExpired' => $nearlyExpired,
            'latestPatients' => $fiveLatestPatients
        ]);
    }

    public function dashboardSupplyCreate(Request $request)
    {
        $inventoryLogs = InventoryLogs::with('medical_supplies')->get();

        $supplies = MedicalSupplies::with(['stocks', 'batches'])
            ->get()
            ->filter(function ($supply) {
                $critical = $supply->stocks->first()?->critical_stock ?? 10;

                return $supply->quantity <= $critical;
            })
            ->values();

        $thresholdDate = Carbon::now()->addDays(30);

        $nearlyExpired = Batch::with('medicalSupply')
            ->whereHas('medicalSupply', fn ($q) => $q->whereNull('deleted_at'))
            ->whereDate('expiration_date', '<=', $thresholdDate)
            ->whereDate('expiration_date', '>=', Carbon::today())
            ->get();

        $data = Patient::select(
            'priority_types.code as code',
            DB::raw('COUNT(patients.id) as total')
        )
        ->join('priority_types', 'patients.priority_id', '=', 'priority_types.id')
        ->groupBy('priority_types.code', 'priority_types.priority_level') // add priority_level
        ->get();


        $fiveLatestPatients = Patient::latest()
        ->take(5)
        ->get(['id', 'patient_id', 'first_name', 'last_name', 'created_at']);

        return Inertia::render('Inventory/Dashboard', [
            'supplies' => $supplies,
            'inventory_logs' => $inventoryLogs,
            'nearlyExpired' => $nearlyExpired,
            'patient_analytics' => $data,
            'latestPatients' => $fiveLatestPatients,
    ]);

    }

     public function mostUsedSupples(Request $request)
    {
    $filter = $request->get('filter', 'all');

    $query = InventoryLogs::select(
        'categories.name',
        DB::raw('SUM(inventory_logs.total_quantity) as total_quantity')
    )
        ->join('medical_supplies', 'inventory_logs.supply_id', '=', 'medical_supplies.id')
        ->join('categories', 'medical_supplies.category_id', '=', 'categories.id')
        ->where('inventory_logs.operation_type', 'DEDUCTED')
        ->groupBy('categories.name')
        ->orderByDesc('total_quantity');

        // apply filters
        if ($filter === 'day') {
            $query->whereDate('inventory_logs.created_at', now()->toDateString());
        } elseif ($filter === 'month') {
            $query->whereMonth('inventory_logs.created_at', now()->month)
                ->whereYear('inventory_logs.created_at', now()->year);
        } elseif ($filter === 'year') {
            $query->whereYear('inventory_logs.created_at', now()->year);
        }

        return response()->json($query->get());
    }



    public function inventory(Request $request)
    {
        $searchQuery = $request->query('search');

        Log::info('searchQuery inventory: ', [$searchQuery]);

        $nearlyExpired = MedicalSupplies::with(['batches' => function ($query) {
            $query->where('expiration_date', '<=', Carbon::now()->addDays(30));
        }])
            ->whereHas('batches', function ($query) {
                $query->where('expiration_date', '<=', Carbon::now()->addDays(30));
            })
            ->get();

        $supplies = MedicalSupplies::with(['batches', 'category'])
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('participants', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('brand_name', 'LIKE', "%{$searchQuery}%")
                        ->orWhereHas('batches', function ($batchQuery) use ($searchQuery) {
                            $batchQuery->where('lot_number', 'LIKE', "%{$searchQuery}%");
                        });
                });
            })

            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        $inventoryLogs = InventoryLogs::with([
            'medical_supplies' => function ($query) {
                $query->select('id', 'brand_name', 'quantity');
            },
        ])->get();

        $supplyUpdate = MedicalSupplies::find($request->input('supply_id'));
        $categories_supply = Categories::all();

        return Inertia::render('Inventory/Index', [
            'supplies' => $supplies,
            'inventory_logs' => $inventoryLogs,
            'nearlyExpired' => $nearlyExpired,
            'supplyUpdate' => $supplyUpdate,
            'categories' => $categories_supply,
            'filters' => $request->only(['perPage', 'page', 'search']),
        ]);
    }

    public function updateSupply(Request $request, $id)
    {
        $supply = MedicalSupplies::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validated['quantity'] > $supply->quantity) {
            return back()->withErrors(['quantity' => 'Cannot deduct more than current supply.']);
        }

        $supply->quantity -= $validated['quantity'];
        $supply->save();

        InventoryLogs::create([
            'requester_name' => Auth::user()->name,
            'operation_type' => Logs::DEDUCTED,
            'total_quantity' => $validated['quantity'],
            'supply_id' => $supply->id,
        ]);

        return redirect()->back()->with('success', 'Supply quantity deducted successfully.');
    }

    public function suppliescreate(Request $request)
    {
        $searchQuery = $request->query('search');

        $supplies = MedicalSupplies::with(['category'])
        ->when($searchQuery, function ($query) use ($searchQuery) {
        $query->where(function ($q) use ($searchQuery) {
            $q->where('participants', 'LIKE', "%{$searchQuery}%")
              ->orWhere('brand_name', 'LIKE', "%{$searchQuery}%")
              ->orWhereHas('batches', function ($batchQuery) use ($searchQuery) {
                  $batchQuery->where('lot_number', 'LIKE', "%{$searchQuery}%");
              })
              ->orWhereHas('category', function ($categoryQuery) use ($searchQuery) {
                  $categoryQuery->where('name', 'LIKE', "%{$searchQuery}%");
              });
        });
    })
    ->get();

        $categories_supplies = Categories::with('medical_supplies')->get();

        return Inertia::render('Inventory/Supplies', [
            'supplies' => $supplies,
            'categories' => $categories_supplies,
        ]);
    }

    public function addStockSupply(Request $request, $id)
    {

        $supply = MedicalSupplies::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'critical_stock' => 'nullable|integer|min:0',
        ]);

        $supply->quantity += $validated['quantity'];
        $supply->save();

        //  Get the latest or current stock record
        $stock = $supply->stocks()->latest()->first();

        if ($stock) {
            //  Update critical_stock only (don't create new one)
            $stock->critical_stock = $validated['critical_stock'] ?? $stock->critical_stock;
            $stock->save();
        } else {
            $supply->stocks()->create([
                'batch_id' => $supply->batches->first()->id ?? null,
                'critical_stock' => $validated['critical_stock'] ?? 10,
            ]);
        }

        //  Log the inventory addition
        InventoryLogs::create([
            'requester_name' => Auth::user()->name,
            'operation_type' => Logs::ADDED,
            'total_quantity' => $validated['quantity'],
            'supply_id' => $supply->id,
        ]);

        return redirect()->back()->with([
            'success' => 'Quantity and critical stock updated successfully.',
        ]);
    }

    public function stockSupplycreate(Request $request)
    {

        $searchQuery = $request->query('search');
        $supplies = MedicalSupplies::with(['stocks', 'batches', 'category'])
         ->when($searchQuery, function ($query) use ($searchQuery) {
        $query->where('brand_name', 'LIKE', "%{$searchQuery}%")
              ->orWhereHas('batches', function ($batchQuery) use ($searchQuery) {
                  $batchQuery->where('lot_number', 'LIKE', "%{$searchQuery}%");
              });
    })
    ->get();

        return Inertia::render('Inventory/Stock', [
            'supplies' => $supplies,
        ]);
    }

    public function batchNumbercreate(Request $request)
    {
        $supplies = MedicalSupplies::with('batches')->get();

        return Inertia::render('Inventory/Batches', [
            'supplies' => $supplies,

        ]);
    }

    public function store(Request $request)
    {

        // THIS IS A RIGHT METHOD
        $validated = $request->validate([
            'participants' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|integer|min:0',
            'manufacture_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:manufacture_date',
            'lot_number' => 'nullable|string|max:255',
            'batch_number' => 'required|string|max:255',
            'crtical_stock' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',

        ]);

        $createdSupply = MedicalSupplies::create([
            'participants' => $validated['participants'],
            'brand_name' => $validated['brand_name'],
            'unit' => $validated['unit'],
            'quantity' => $validated['quantity'],
            'manufacture_date' => $validated['manufacture_date'],
            'expiration_date' => $validated['expiration_date'],
            'lot_number' => $validated['lot_number'],
            'batch_number' => $validated['batch_number'],
            'category_id' => $validated['category_id'],

        ]);

        $createdSupply->batches()->create([
            'quantity' => $validated['quantity'],
            'batch_number' => $validated['batch_number'],
            'manufacture_date' => $validated['manufacture_date'],
            'expiration_date' => $validated['expiration_date'],
        ]);

        // Log the inventory
        InventoryLogs::create([
            'requester_name' => Auth::user()->name,
            'operation_type' => Logs::ADDED,
            'total_quantity' => $validated['quantity'],
            'supply_id' => $createdSupply->id,
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
                },
            ])
            ->get();

        return Inertia::render('Inventory/Request', [
            'medical_supply_requests' => $supplyRequest,
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

            Log::info('Pivot quantity'.$quantity);

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
    public function inventorySupplyRequest(Request $request)
    {
        $searchQuery = $request->query('search');

            $supplyRequest = SupplyRequest::select('id', 'to', 'po_number', 'status')
        ->with([
            'requested_supply' => function ($query) {
                $query->select('id', 'request_id', 'quantity', 'unit', 'item_description', 'unit_price', 'total_price');
            },
        ])
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where('to', 'LIKE', "%{$searchQuery}%")
                ->orWhere('po_number', 'LIKE', "%{$searchQuery}%")
                ->orWhere('status', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('requested_supply', function ($subQuery) use ($searchQuery) {
                    $subQuery->where('item_description', 'LIKE', "%{$searchQuery}%");
                });
        })
        ->get();

        Log::info('supplyRequest', [
            'supplyRequest' => $supplyRequest,
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

        Log::info('Status: ', $validated);

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

        Log::info('Request Data', [
            'poNumber' => $poNumber,
            'to' => $to,
            'items' => $items,
        ]);

        $supplyRequest = SupplyRequest::create([
            'to' => $to,
            'po_number' => $poNumber,
            'status' => MedicalRequestStatus::PENDING->value,
            'user_id' => Auth::user()->id,
        ]);

        $supplyRequest->requested_supply()->createMany($validated['items']);

        return redirect()->back()->with('success', 'Supply Request Submitted');
    }

    public function CategoriesSupplycreate(Request $request)
    {
        $searchQuery = $request->query('search');

        $categories_supply = Categories::when($searchQuery, function ($query) use ($searchQuery) {
        $query->where('name', 'LIKE', "%{$searchQuery}%")
                ->orWhere('description', 'LIKE', "%{$searchQuery}%");
    })
    ->get();

        $updateCategory = categories::find($request->input('id'));

        return Inertia::render('Inventory/CategorySupply', [
            'categories' => $categories_supply,
            'updateCategory' => $updateCategory
        ]);
    }

    public function categoriesStoreData(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        Categories::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function archiveSuppliescreate(Request $request)
    {
        $searchQuery = $request->query('search');

    $archive_supplies = archive_supplies::with(['batches', 'medical_supply'])
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->whereHas('medical_supply', function ($supplyQuery) use ($searchQuery) {
                $supplyQuery->where('brand_name', 'LIKE', "%{$searchQuery}%");
            });
        })
        ->get();

        return Inertia::render('Inventory/ArchiveSupplies', [
            'arcvhivedSupplies' => $archive_supplies,
        ]);
    }

    public function archiveSuppliesData($id)
    {
        DB::transaction(function () use ($id) {

            $supply = MedicalSupplies::with('batches')->findOrFail($id);

            if ($supply->batches->isNotEmpty()) {
                foreach ($supply->batches as $batch) {
                    archive_supplies::create([
                        'medical_supplies_id' => $supply->id,
                        'batch_id' => $batch->id,
                    ]);

                    $batch->delete();
                }
            } else {
                archive_supplies::create([
                    'medical_supplies_id' => $supply->id,
                    'batch_id' => null,
                ]);
            }

            InventoryLogs::where('supply_id', $supply->id)->delete();
            $supply->delete(); // soft delete
        });

        return back()->with('success', 'Supply archived successfully.');
    }

    public function updateCategory(Request $request, Categories $category){

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        $category->update($validate);

        return redirect()->back()->with('success', 'Update Category Successfully');

    }

    public function deleteCategory($id)
    {
        $delete = Categories::findorFail($id);
        $delete->delete();

        return redirect()->back()->back('success', 'Deleted Category Successfully');
    }

     public function printRequestSupplyPDF()
    {
        $supplies_requested = RequestedSupply::all();

        $logobase = $this->logoBase();

        $pdf = Pdf::loadView('pdf.request_supply_report', [
            'supplies_requested' => $supplies_requested,
            'logobase' => $logobase
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('request_supply_report.pdf');
    }

    private function logoBase()
    {
        $logoPath = public_path('img/lexa-logo-removedbg.png');

        if (file_exists($logoPath)) {
            $imageData = base64_encode(file_get_contents($logoPath));
            $mimeType = mime_content_type($logoPath);
            return 'data:' . $mimeType . ';base64,' . $imageData;
        }

        return null;
    }



}
