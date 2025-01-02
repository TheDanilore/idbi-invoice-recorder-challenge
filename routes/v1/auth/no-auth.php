<?php

// Define rutas específicas relacionadas con autenticación de usuarios
use App\Http\Controllers\Auth\LoginHandler;
use App\Http\Controllers\Auth\LogoutHandler;
use App\Http\Controllers\Auth\SignUpHandler;
use Illuminate\Support\Facades\Route;

Route::post('/users', SignUpHandler::class); // Registro de nuevos usuarios
Route::post('/login', LoginHandler::class); // Inicio de sesión
Route::post('/logout', LogoutHandler::class)
    ->middleware('jwt.verify'); // Cierre de sesión (requiere autenticación JWT)
