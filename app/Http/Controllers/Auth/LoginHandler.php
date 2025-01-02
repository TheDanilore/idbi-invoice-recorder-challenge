<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Controlador para manejar el inicio de sesión de usuarios.
 */
class LoginHandler
{
    /**
     * Maneja el inicio de sesión de un usuario autenticado.
     *
     * @param LoginRequest $request Solicitud HTTP que contiene las credenciales del usuario.
     * @return Response Respuesta HTTP con el token JWT y los datos del usuario.
     */
    public function __invoke(LoginRequest $request): Response
    {
        $credentials = $request->only('email', 'password'); // Extrae las credenciales del usuario.

        try {
            // Intenta autenticar al usuario con las credenciales proporcionadas.
            if (!$token = JWTAuth::attempt($credentials)) {
                // Respuesta en caso de credenciales inválidas.
                return response([
                    'message' => 'Credenciales inválidas'
                ], 401);
            }
        } catch (JWTException $exception) {
            // Maneja errores relacionados con la creación del token.
            return response([
                'message' => 'No se pudo crear el token'
            ], 500);
        }

        // Obtiene al usuario autenticado.
        $user = JWTAuth::user();

        // Retorna una respuesta con el token JWT y los datos del usuario.
        return response([
            'data' => [
                'token' => $token,
                'user' => UserResource::make($user), // Serializa el usuario como recurso.
            ],
        ], 200);
    }
}
