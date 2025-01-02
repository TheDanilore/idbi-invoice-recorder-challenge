<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Controlador para manejar el cierre de sesión de los usuarios.
 */
class LogoutHandler
{
    /**
     * Invalida el token JWT y cierra la sesión del usuario.
     *
     * @param Request $request Solicitud HTTP que incluye el token del usuario.
     * @return Response Respuesta HTTP indicando el resultado del cierre de sesión.
     */
    public function __invoke(Request $request): Response
    {
        try {
            // Obtiene el token de la cabecera Authorization.
            $token = $request->bearerToken();

            // Configura el token en el administrador de JWT y lo invalida.
            $token = JWTAuth::setToken($token)->getToken();
            JWTAuth::manager()->invalidate($token, true);

            // Respuesta de éxito en el cierre de sesión.
            return response([
                'message' => 'Cierre de sesión exitoso'
            ], 200);
        } catch (JWTException $exception) {
            // Maneja errores al invalidar el token.
            return response([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
