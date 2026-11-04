
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfidScanController;

Route::post('/rfid/scan', [RfidScanController::class, 'scan']);
