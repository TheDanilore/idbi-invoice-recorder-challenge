<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Clase de solicitud para validar los parámetros de la consulta de comprobantes.
 */
class GetVouchersRequest extends FormRequest
{
    /**
     * Define las reglas de validación para los parámetros de la solicitud.
     *
     * @return array Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],        // Parámetro obligatorio, debe ser un entero mayor a 0.
            'paginate' => ['required', 'int', 'gt:0'],   // Parámetro obligatorio, número de resultados por página.
        ];
    }
}
