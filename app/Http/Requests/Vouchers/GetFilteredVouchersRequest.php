<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Clase de solicitud para manejar los filtros en la consulta de comprobantes.
 */
class GetFilteredVouchersRequest extends FormRequest
{
    /**
     * Define las reglas de validación para los parámetros de filtro.
     *
     * @return array Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'issuer_name' => 'nullable|string|max:255',          // Nombre del emisor (opcional).
            'receiver_name' => 'nullable|string|max:255',        // Nombre del receptor (opcional).
            'serie' => 'nullable|string|max:20',                 // Serie del comprobante (opcional).
            'moneda' => 'nullable|string|max:3',                 // Moneda (opcional).
            'date_from' => 'nullable|date',                      // Fecha inicial del rango (opcional).
            'date_to' => 'nullable|date|after_or_equal:date_from', // Fecha final del rango, debe ser igual o posterior a la inicial.
            'page' => 'nullable|integer|min:1',                  // Página de resultados (opcional).
            'paginate' => 'nullable|integer|min:1|max:100',      // Cantidad de resultados por página (opcional).
        ];
    }

    /**
     * Autoriza la ejecución de esta solicitud.
     *
     * @return bool Retorna true para permitir siempre la solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }
}
