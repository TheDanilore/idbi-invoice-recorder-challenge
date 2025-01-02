<?php

// Rutas específicas relacionadas con la gestión de comprobantes (vouchers)
use App\Http\Controllers\Vouchers\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\GetVouchersSummaryHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class); // Obtiene el listado de vouchers
        Route::post('/', StoreVouchersHandler::class); // Almacena vouchers
        Route::delete('/{id}', DeleteVoucherHandler::class); // Elimina un voucher por ID
        Route::get('/montos-acumulados', GetVouchersSummaryHandler::class); // Obtiene montos acumulados por moneda
        Route::get('/filtrar', [GetVouchersHandler::class, 'getFilteredVouchers']); // Filtra vouchers
    }
);
