<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\VoucherService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessVoucherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $xmlContent;
    public $user;

    public function __construct(string $xmlContent, User $user)
    {
        $this->xmlContent = $xmlContent;
        $this->user = $user;
    }

    public function handle(VoucherService $voucherService)
    {
        try {
            Log::info("Iniciando procesamiento del comprobante", [
                'user_id' => $this->user->id,
            ]);
    
            $voucherService->storeVoucherFromXmlContent($this->xmlContent, $this->user);
    
            Log::info("Procesamiento del comprobante finalizado", [
                'user_id' => $this->user->id,
            ]);
        } catch (Exception $e) {
            Log::error("Error durante el procesamiento del comprobante", [
                'user_id' => $this->user->id,
                'exception' => $e->getMessage(),
            ]);
            throw $e; // Re-lanza la excepción para manejarla en `failed()`.
        }
    }

    public function failed(Exception $exception)
    {
        Log::error("Error al procesar comprobante", [
            'user_id' => $this->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
