<?php

// Rutas de la API, con prefijo 'v1'
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(
    function () {
        include_once 'vouchers/auth.php'; // Incluye rutas relacionadas con los vouchers
    }
);
