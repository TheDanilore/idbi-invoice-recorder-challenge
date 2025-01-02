<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class StoreVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $xmlFiles = $request->file('files');

            // Convertir en array si no lo es
            if ($xmlFiles instanceof \Illuminate\Http\UploadedFile) {
                $xmlFiles = [$xmlFiles];
            }

            // Log para confirmar que se han recibido archivos correctamente
            Log::info("Archivos recibidos", ['count' => is_array($xmlFiles) ? count($xmlFiles) : 0]);

            if (empty($xmlFiles)) {
                return response()->json([
                    'message' => 'No se proporcionaron archivos.',
                ], 400);
            }

            // Verificar si es un solo archivo y convertirlo en un array
            if (!is_array($xmlFiles)) {
                $xmlFiles = [$xmlFiles];
            }

            // Ahora puedes usar count() con seguridad
            Log::info("Archivos recibidos", ['count' => count($xmlFiles)]);

            $xmlContents = [];
            foreach ($xmlFiles as $index => $xmlFile) {
                if (!$xmlFile->isValid()) {
                    return response()->json([
                        'message' => 'Uno o más archivos no son válidos.',
                    ], 400);
                }
                $content = file_get_contents($xmlFile->getRealPath());
                Log::info("Archivo leído correctamente", ['index' => $index, 'content_preview' => substr($content, 0, 100)]);
                $xmlContents[] = $content;
            }
            Log::info("Contenido de XMLs procesados", ['count' => count($xmlContents)]);

            $user = auth()->user();

            $this->voucherService->storeVouchersFromXmlContents($xmlContents, $user);

            return response()->json([
                'message' => 'El procesamiento de los comprobantes ha comenzado.',
            ], 202);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Ocurrieron errores al procesar los comprobantes.',
                'error' => $exception->getMessage(),
            ], 400);
        }
    }
}
