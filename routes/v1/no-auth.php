<?php

// Archivo que define rutas generales para la API versión 'v1'
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(
    function () {
        include_once 'auth/no-auth.php'; // Rutas relacionadas con la autenticación que no requieren autenticación previa
    }
);
