<?php

namespace App\Http\Controllers\Vouchers;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteVoucherHandler
{

    public function __invoke($id, Request $request): JsonResponse
    {
        $user = auth()->user();

        // Busca el comprobante por ID y asegura de que pertenece al usuario autenticado
        $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first();

        if (!$voucher) {
            return response()->json(['message' => 'Comprobante no encontrado o no autorizado'], 404);
        }

        // Eliminar el comprobante
        $voucher->delete();

        return response()->json(['message' => 'Comprobante eliminado exitosamente'], 200);
    }
}
