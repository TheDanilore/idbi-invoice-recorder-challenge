<?php

namespace App\Http\Controllers\Vouchers;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteVoucherHandler
{
    /**
     * Maneja la eliminación de un comprobante por su ID.
     *
     * @param string $id El identificador único del comprobante.
     * @param Request $request La solicitud HTTP actual.
     * @return JsonResponse Respuesta JSON indicando el estado de la operación.
     */
    public function __invoke($id, Request $request): JsonResponse
    {
        // Obtiene el usuario autenticado
        $user = auth()->user();

        // Busca el comprobante por ID y asegura de que pertenece al usuario autenticado
        $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first();

        // Retorna un error si no encuentra el comprobante o no pertenece al usuario
        if (!$voucher) {
            return response()->json(['message' => 'Comprobante no encontrado o no autorizado'], 404);
        }

        // Eliminar el comprobante (Soft Delete)
        $voucher->delete();

        // Retorna un mensaje indicando éxito en la operación
        return response()->json(['message' => 'Comprobante eliminado exitosamente'], 200);
    }
}
