<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetFilteredVouchersRequest;
use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador para manejar solicitudes relacionadas con la obtención de comprobantes.
 */
class GetVouchersHandler
{
    /**
     * Constructor que inyecta el servicio de comprobantes.
     *
     * @param VoucherService $voucherService Servicio encargado de la lógica de comprobantes.
     */
    public function __construct(private readonly VoucherService $voucherService) {}

    /**
     * Obtiene una lista de comprobantes con paginación.
     *
     * @param GetVouchersRequest $request Solicitud HTTP que incluye los parámetros de paginación.
     * @return AnonymousResourceCollection Colección de recursos de comprobantes.
     */
    public function __invoke(GetVouchersRequest $request): AnonymousResourceCollection
    {
        $vouchers = $this->voucherService->getVouchers(
            $request->query('page'),      // Página solicitada.
            $request->query('paginate'), // Cantidad de elementos por página.
        );

        // Retorna los comprobantes como una colección de recursos.
        return VoucherResource::collection($vouchers);
    }

    /**
     * Obtiene una lista de comprobantes filtrados según los parámetros proporcionados.
     *
     * @param GetFilteredVouchersRequest $request Solicitud HTTP que incluye los filtros.
     * @return AnonymousResourceCollection Colección de recursos de comprobantes filtrados.
     */
    public function getFilteredVouchers(GetFilteredVouchersRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated(); // Valida y obtiene los filtros proporcionados.

        $vouchers = $this->voucherService->getFilteredVouchers(
            $filters,
            $request->query('page', 1),     // Página predeterminada si no se proporciona.
            $request->query('paginate', 15), // Cantidad predeterminada de elementos por página.
        );

        // Retorna los comprobantes filtrados como una colección de recursos.
        return VoucherResource::collection($vouchers);
    }
}
