<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Console\Command;

class RegularizeVouchers extends Command
{
    protected $signature = 'vouchers:regularize';
    protected $description = 'Extract additional data from existing voucher XML content';

    private $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        parent::__construct();
        $this->voucherService = $voucherService;
    }

    public function handle()
    {
        $vouchers = Voucher::all(); // Obtener todos los registros existentes
        $this->info("Found {$vouchers->count()} vouchers to process...");

        foreach ($vouchers as $voucher) {
            if ($voucher->serie && $voucher->numero && $voucher->tipo_comprobante && $voucher->moneda) {
                $this->info("Voucher ID {$voucher->id} already has all fields, skipping...");
                continue;
            }

            // Procesar el contenido XML
            $xmlContent = $voucher->xml_content;
            $parsedData = $this->voucherService->parseXML($xmlContent);

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
