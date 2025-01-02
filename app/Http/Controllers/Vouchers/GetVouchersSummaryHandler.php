<?php

namespace App\Http\Controllers\Vouchers;

use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para obtener un resumen de los montos acumulados por moneda.
 */
class GetVouchersSummaryHandler
{
     /**
     * Constructor que inyecta el servicio de comprobantes.
     *
     * @param VoucherService $voucherService Servicio encargado de la lógica de comprobantes.
     */
    public function __construct(private readonly VoucherService $voucherService) {}

    /**
     * Maneja la solicitud para obtener el resumen de montos acumulados por moneda.
     *
     * @return JsonResponse Respuesta JSON con los montos acumulados.
     */
    public function __invoke(): JsonResponse
    {
        try {
            // Obtiene el resumen de montos acumulados por moneda
            $summary = $this->voucherService->getMontosAcumuladosPorMoneda();

            // Retorna la respuesta con el resumen
            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            // Maneja errores y retorna una respuesta con el mensaje de error
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen de montos acumulados por moneda.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
