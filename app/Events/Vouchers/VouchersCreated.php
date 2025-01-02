<?php

namespace App\Events\Vouchers;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento que se dispara cuando se crean nuevos comprobantes.
 */
class VouchersCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Constructor del evento.
     *
     * @param Voucher[] $vouchers Lista de comprobantes creados.
     * @param User $user Usuario que generó los comprobantes.
     */
    public function __construct(
        public readonly array $vouchers, // Lista de comprobantes creados.
        public readonly User $user       // Usuario autenticado.
    ) {
    }
}
