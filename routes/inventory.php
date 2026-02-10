<?php

use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:manage-inventory-supplies'])->group(function () {

    // RENDER TO DASHBOARD PAGE
    Route::get('/medical/supply/dashboard/create', [MedicalSupplyController::class, 'dashboardSupplyCreate'])
        ->name('inventory.dashboard');

    Route::get('/most/used/supply', [MedicalSupplyController::class, 'mostUsedSupples'])
        ->name('most.used.supply');

    // RENDER INVETORY SUPPLIES PAGE
    Route::get('/inventory/supplies', [MedicalSupplyController::class, 'inventory'])
        ->name('inventory.supplies');

    // UPDATE QUANTIY OF SUPPLY LEFT
    Route::put('/medical/supply/update/{id}', [MedicalSupplyController::class, 'updateSupply'])
        ->name('supply.update');

    // RENDER TO STOCK PAGE
    Route::get('/medical/stock/create', [MedicalSupplyController::class, 'stockSupplycreate'])
        ->name('medical.stock.create');

    // INSERT SUPPLY REQUEST
    Route::post('/medical/supply/request/create', [MedicalSupplyController::class, 'medicalSupplyRequestCreate'])
        ->name('medical.request.create');

    // RENDER BATCH NUMBER PAGE
    Route::get('/medical/supply/batches', [MedicalSupplyController::class, 'batchNumbercreate'])
        ->name('inventory.supply.batches');

    // STORED DATA BATCH NUMBER
    // Route::post('/medical/supply/batch/store', [MedicalSupplyController::class, 'storeBatchNumber'])
    //     ->name('add.batch');

    // RENDER CATEGORY SUPPLY PAGE
    Route::get('/category/supplies/data/create', [MedicalSupplyController::class, 'CategoriesSupplycreate'])
        ->name('category.supplies.create');

    // UPDATE CATEGORIES
     Route::put('/update/category/{category}', [MedicalSupplyController::class, 'updateCategory'])
        ->name('update.category');

    // DELETE CATEGORY
    Route::delete('/delete/category/{id}', [MedicalSupplyController::class, 'deleteCategory'])
        ->name('delete.category');

    // STORED DATA CATEGORY SUPPLY
    Route::post('/category/store/data', [MedicalSupplyController::class, 'categoriesStoreData'])
        ->name('categories.store.data');

    // RENDER SUPPLIES PAGE
    Route::get('/supplies/create/data', [MedicalSupplyController::class, 'suppliescreate'])
        ->name('supplies.create.page');

    // RENDER SUPPLY ARCHIVED SUPPLIES PAGE
    Route::get('/archive/supplies/data/create', [MedicalSupplyController::class, 'archiveSuppliescreate'])
        ->name('archive.supplies.create');

    // ARCHIVING SUPPLIES STORED DATA
    Route::post('/archive/supplies/{id}/store', [MedicalSupplyController::class, 'archiveSuppliesData'])
        ->name('archive.supplies.data');

    // RENDER SUPPLY REQUEST PAGE
    Route::get('/medical/supply/request', [MedicalSupplyController::class, 'inventorySupplyRequest'])
        ->name('inventory.supply.request');

    // UPDATE SUPPLY REQUEST STATUS
    Route::post('/update/supply/request/status', [MedicalSupplyController::class, 'updateRequestStatus'])
        ->name('update.request.status');

    // SUPPLY INVENTORY ADDITION ENDPOINT
    Route::post('/supply/add', [MedicalSupplyController::class, 'store'])
        ->name('supply.add');

    Route::post('/add/stock/{id}', [MedicalSupplyController::class, 'addStockSupply'])
        ->name('supply.add.stock');

    Route::put('/supply/critical-stock/{id}', [MedicalSupplyController::class, 'updateCriticalStock'])
        ->name('supply.update.critical.stock');

    Route::get('/inventory/print', [MedicalSupplyController::class, 'printPDFReport'])
        ->name('inventory.print');

    Route::get('/supply/requqst/print', [MedicalSupplyController::class, 'printRequestSupplyPDF'])
        ->name('supply.request.print');

    // UPDATE REQUEST ENDPOINT
    Route::put('/update/supply/request', [MedicalSupplyController::class, 'update'])
        ->name('update.supply.request');
});
