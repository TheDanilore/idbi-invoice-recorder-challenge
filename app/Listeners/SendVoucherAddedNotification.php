<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersCreated;
use App\Mail\VouchersCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVoucherAddedNotification implements ShouldQueue
{
    public function handle(VouchersCreated $event): void
    {
        try {
            $mail = new VouchersCreatedMail($event->vouchers, $event->user);
            Mail::to($event->user->email)->queue($mail);// Usamos queue() para enviar asíncronamente.
        } catch (\Exception $e) {
            Log::error('Error enviando correo de comprobantes', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
