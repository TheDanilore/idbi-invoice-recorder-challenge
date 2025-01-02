<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Console\Command;

/**
 * Comando para regularizar comprobantes existentes, extrayendo y almacenando
 * datos adicionales (serie, número, tipo de comprobante, moneda) desde el contenido XML.
 */
class RegularizeVouchers extends Command
{
    // Define el nombre del comando
    protected $signature = 'vouchers:regularize';

    // Describe la funcionalidad del comando
    protected $description = 'Extract additional data from existing voucher XML content';

    private $voucherService;

    // Constructor para inyectar dependencias
    public function __construct(VoucherService $voucherService)
    {
        parent::__construct();
        $this->voucherService = $voucherService;
    }

    /**
     * Maneja la ejecución del comando.
     * 
     * @return int Código de estado (0 para éxito)
     */
    public function handle()
    {
        // Obtiene todos los registros de comprobantes existentes
        $vouchers = Voucher::all(); 
        $this->info("Found {$vouchers->count()} vouchers to process...");

        foreach ($vouchers as $voucher) {
            // Verifica si los campos ya están completos
            if ($voucher->serie && $voucher->numero && $voucher->tipo_comprobante && $voucher->moneda) {
                $this->info("Voucher ID {$voucher->id} already has all fields, skipping...");
                continue;
            }

            // Procesa el contenido XML para extraer datos
            $xmlContent = $voucher->xml_content;
            $parsedData = $this->voucherService->parseXML($xmlContent);

            // Actualiza el comprobante con los datos extraídos
            $voucher->update([
                'serie' => $parsedData['serie'] ?? $voucher->serie,
                'numero' => $parsedData['numero'] ?? $voucher->numero,
                'tipo_comprobante' => $parsedData['tipo_comprobante'] ?? $voucher->tipo_comprobante,
                'moneda' => $parsedData['moneda'] ?? $voucher->moneda,
            ]);

            $this->info("Updated Voucher ID {$voucher->id}");
        }

        $this->info("Regularization complete!");
        return 0;
    }

}
