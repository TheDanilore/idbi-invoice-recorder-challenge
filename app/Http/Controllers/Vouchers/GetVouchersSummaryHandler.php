<?php

namespace App\Http\Controllers\Vouchers;

use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;

class GetVouchersSummaryHandler
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke(): JsonResponse
    {
        try {
            $summary = $this->voucherService->getMontosAcumuladosPorMoneda();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen de montos acumulados por moneda.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
