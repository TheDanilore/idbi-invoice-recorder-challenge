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

    /**
     * Contenido XML del comprobante.
     * 
     * @var string
     */
    public $xmlContent;

    /**
     * Usuario asociado al comprobante.
     * 
     * @var User
     */
    public $user;

     /**
     * Constructor de la clase.
     *
     * @param string $xmlContent Contenido del comprobante en formato XML.
     * @param User $user Usuario autenticado que subió el comprobante.
     */
    public function __construct(string $xmlContent, User $user)
    {
        $this->xmlContent = $xmlContent;
        $this->user = $user;
    }

    /**
     * Maneja el trabajo asincrónico para almacenar un comprobante.
     *
     * @param VoucherService $voucherService Servicio encargado de procesar y almacenar el comprobante.
     * @return void
     * @throws Exception Si ocurre un error durante el procesamiento.
     */
    public function handle(VoucherService $voucherService)
    {
        try {
            Log::info("Iniciando procesamiento del comprobante", [
                'user_id' => $this->user->id,
            ]);
    
            // Procesa y almacena el comprobante utilizando el servicio
            $voucherService->storeVoucherFromXmlContent($this->xmlContent, $this->user);

            Log::info("Procesamiento del comprobante finalizado", [
                'user_id' => $this->user->id,
            ]);
        } catch (Exception $e) {
            // Registra el error en el log y re-lanza la excepción
            Log::error("Error durante el procesamiento del comprobante", [
                'user_id' => $this->user->id,
                'exception' => $e->getMessage(),
            ]);
            throw $e; // Re-lanza la excepción para manejarla en `failed()`.
        }
    }

    /**
     * Maneja errores cuando el trabajo falla.
     *
     * @param Exception $exception La excepción que causó el fallo.
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::error("Error al procesar comprobante", [
            'user_id' => $this->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
