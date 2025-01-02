<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Controlador para manejar el registro de nuevos usuarios.
 */
class SignUpHandler
{
    /**
     * Registra un nuevo usuario en el sistema.
     *
     * @param SignUpRequest $request Solicitud HTTP que incluye los datos del usuario.
     * @return Response Respuesta HTTP con los datos del usuario creado.
     */
    public function __invoke(SignUpRequest $request): Response
    {
        // Crea un nuevo usuario con los datos proporcionados.
        $user = User::create([
            'name' => $request->input('name'),         // Nombre del usuario.
            'last_name' => $request->input('last_name'), // Apellido del usuario.
            'email' => $request->input('email'),       // Correo electrónico.
            'password' => bcrypt($request->input('password')), // Contraseña encriptada.
        ]);

        // Retorna una respuesta con los datos del usuario como recurso.
        return response([
            'data' => UserResource::make($user)
        ], 201);
    }
}
