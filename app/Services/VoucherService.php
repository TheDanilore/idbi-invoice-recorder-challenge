<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Jobs\ProcessVoucherJob;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class VoucherService
{
    /**
     * Obtiene una lista paginada de vouchers.
     *
     * @param int $page
     * @param int $paginate
     * @return LengthAwarePaginator
     */
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * Almacena un comprobante a partir del contenido XML.
     *
     * @param string $xmlContent
     * @param User $user
     * @return Voucher
     * @throws Exception Si el XML no contiene información válida.
     */
    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        Log::info("Procesando contenido XML para almacenar", ['user_id' => $user->id]);
        return DB::transaction(function () use ($xmlContent, $user) {
            Log::info("Iniciando transacción para almacenar comprobante", ['user_id' => $user->id]);

            $xml = new SimpleXMLElement($xmlContent);

            // Extraer datos del XML
            $issuerName = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0] ?? '');
            $issuerDocumentType = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0] ?? '');
            $issuerDocumentNumber = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0] ?? '');

            $receiverName = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0] ?? '');
            $receiverDocumentType = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0] ?? '');
            $receiverDocumentNumber = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0] ?? '');

            $totalAmount = (float) ($xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0] ?? 0.0);
            $serie = (string) ($xml->xpath('//cbc:ID')[0] ?? '');
            $numero = (string) ($xml->xpath('//cbc:ID')[1] ?? '');
            $tipoComprobante = (string) ($xml->xpath('//cbc:InvoiceTypeCode')[0] ?? '');
            $moneda = (string) ($xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? '');

            // Validaciones
            if (!$issuerName || !$issuerDocumentType || !$issuerDocumentNumber) {
                throw new Exception("El XML no contiene información válida del emisor.");
            }

            if (!$receiverName || !$receiverDocumentType || !$receiverDocumentNumber) {
                throw new Exception("El XML no contiene información válida del receptor.");
            }

            if (!$totalAmount) {
                throw new Exception("El XML no contiene información válida sobre el monto total.");
            }

            if (!$numero) {
                throw new Exception("El XML no contiene información válida del número de comprobante.");
            }

            // Generar y validar hash único
            $hash = hash('sha256', $xmlContent);

            Log::info("Hash generado para el XML", ['hash' => $hash]);

            if (Voucher::where('hash', $hash)->exists()) {
                Log::warning("Hash ya existe, ignorando comprobante", ['hash' => $hash]);
                throw new Exception("El comprobante ya existe con hash: {$hash}");
            }

            // Crear el registro del voucher
            $voucher = Voucher::create([
                'hash' => $hash,
                'issuer_name' => $issuerName,
                'issuer_document_type' => $issuerDocumentType,
                'issuer_document_number' => $issuerDocumentNumber,
                'receiver_name' => $receiverName,
                'receiver_document_type' => $receiverDocumentType,
                'receiver_document_number' => $receiverDocumentNumber,
                'total_amount' => $totalAmount,
                'xml_content' => $xmlContent,
                'serie' => $serie,
                'numero' => $numero,
                'tipo_comprobante' => $tipoComprobante,
                'moneda' => $moneda,
                'user_id' => $user->id,
            ]);

            // Crear las líneas de la factura
            foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
                $name = (string) ($invoiceLine->xpath('cac:Item/cbc:Description')[0] ?? 'Sin descripción');
                $quantity = (float) ($invoiceLine->xpath('cbc:InvoicedQuantity')[0] ?? 0);
                $unitPrice = (float) ($invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0] ?? 0.0);

                VoucherLine::create([
                    'name' => $name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'voucher_id' => $voucher->id,
                ]);
            }
            Log::info("Comprobante almacenado exitosamente", ['voucher_id' => $voucher->id]);
            return $voucher;
        });
    }

    /**
     * Procesa y almacena múltiples comprobantes XML.
     *
     * @param string[] $xmlContents
     * @param User $user
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): void
    {
        $vouchers = []; // Array para almacenar los comprobantes procesados.

        foreach ($xmlContents as $index => $xmlContent) {
            Log::info("Enviando trabajo a la cola para XML", [
                'index' => $index,
                'xml_preview' => substr($xmlContent, 0, 100),
            ]);

            // Procesa cada comprobante y almacénalo en el array.
            $voucher = $this->storeVoucherFromXmlContent($xmlContent, $user);
            $vouchers[] = $voucher;
        }

        // Despacha el evento si hay comprobantes procesados.
        if (!empty($vouchers)) {
            VouchersCreated::dispatch($vouchers, $user);
        }
    }

    /**
     * Extrae datos clave del contenido XML.
     *
     * @param string $xmlContent
     * @return array
     */
    public function parseXML($xmlContent): array
    {
        $xml = new SimpleXMLElement($xmlContent);
        return [
            'serie' => $xml->xpath('//cbc:ID')[0] ?? null,
            'numero' => $xml->xpath('//cbc:ID')[1] ?? null,
            'tipo_comprobante' => $xml->xpath('//cbc:InvoiceTypeCode')[0] ?? null,
            'moneda' => $xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? null,
        ];
    }

    /**
     * Obtiene montos acumulados agrupados por moneda.
     *
     * @return array
     */
    public function getMontosAcumuladosPorMoneda(): array
    {
        // Consulta a la base de datos agrupando por moneda y sumando los montos
        return Voucher::select('moneda', DB::raw('SUM(total_amount) as total'))
            ->groupBy('moneda')
            ->get()
            ->toArray();
    }

    /**
     * Filtra y obtiene comprobantes según criterios específicos.
     *
     * @param array $filters
     * @param int $page
     * @param int $paginate
     * @return LengthAwarePaginator
     */
    public function getFilteredVouchers(array $filters, int $page, int $paginate): LengthAwarePaginator
    {
        $query = Voucher::query();

        // Aplicar filtros condicionalmente
        if (isset($filters['issuer_name'])) {
            $query->where('issuer_name', 'LIKE', '%' . $filters['issuer_name'] . '%');
        }

        if (isset($filters['receiver_name'])) {
            $query->where('receiver_name', 'LIKE', '%' . $filters['receiver_name'] . '%');
        }

        if (isset($filters['serie'])) {
            $query->where('serie', $filters['serie']);
        }

        if (isset($filters['moneda'])) {
            $query->where('moneda', $filters['moneda']);
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        }

        // Agregar paginación
        return $query->paginate(perPage: $paginate, page: $page);
    }
}
