<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersCreated;
use App\Mail\VouchersCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Listener que maneja el envío de correos cuando se crean comprobantes.
 */
class SendVoucherAddedNotification implements ShouldQueue
{
    /**
     * Maneja el evento de creación de comprobantes.
     *
     * @param VouchersCreated $event Evento que contiene los comprobantes creados y el usuario asociado.
     * @return void
     */
    public function handle(VouchersCreated $event): void
    {
        try {
            // Crea el correo utilizando la clase `VouchersCreatedMail`
            $mail = new VouchersCreatedMail($event->vouchers, $event->user);
            
            // Envía el correo de manera asíncrona
            Mail::to($event->user->email)->queue($mail);// Usamos queue() para enviar asíncronamente.
        } catch (\Exception $e) {
            // Registra cualquier error ocurrido durante el envío del correo
            Log::error('Error enviando correo de comprobantes', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
