<?php

// Archivo principal que agrupa rutas con y sin autenticación
use Illuminate\Support\Facades\Route;

// Incluye las rutas públicas (sin autenticación JWT)
include_once 'v1/no-auth.php';

// Agrupa rutas protegidas bajo el middleware 'jwt.verify' para requerir autenticación
Route::group(['middleware' => ['jwt.verify']], function () {
    include_once 'v1/auth.php';// Rutas que requieren autenticación
});
