<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Modelo que representa los comprobantes (vouchers) almacenados en la base de datos.
 * 
 * @property string $id
 * @property string $issuer_name Nombre del emisor del comprobante
 * @property string $issuer_document_type Tipo de documento del emisor
 * @property string $issuer_document_number Número del documento del emisor
 * @property string $receiver_name Nombre del receptor del comprobante
 * @property string $receiver_document_type Tipo de documento del receptor
 * @property string $receiver_document_number Número del documento del receptor
 * @property float $total_amount Monto total del comprobante
 * @property string $xml_content Contenido XML del comprobante
 * @property string $user_id ID del usuario que subió el comprobante
 * @property Carbon|null $created_at Fecha de creación del comprobante
 * @property Carbon|null $updated_at Fecha de última actualización del comprobante
 * @property Carbon|null $deleted_at Fecha de eliminación suave del comprobante
 * @property-read User $user Relación con el usuario que registró el comprobante
 * @property-read Collection|User[] $lines Relación con las líneas del comprobante
 * @mixin Builder
 */
class Voucher extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    // Habilita las marcas de tiempo automáticas (created_at, updated_at)
    public $timestamps = true;

    // Define los campos que pueden ser asignados en masa
    protected $fillable = [
        'hash',
        'xml_content',
        'serie',
        'numero',
        'tipo_comprobante',
        'moneda',
        'issuer_name',
        'issuer_document_type',
        'issuer_document_number',
        'receiver_name',
        'receiver_document_type',
        'receiver_document_number',
        'total_amount',
        'xml_content',
        'user_id',
    ];

    // Define los atributos que serán casteados automáticamente
    protected $casts = [
        'total_amount' => 'float', // Asegura que el monto total sea tratado como float
    ];

    /**
     * Define la relación "belongsTo" con el modelo User.
     * 
     * @return BelongsTo Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación "hasMany" con el modelo VoucherLine.
     * 
     * @return HasMany Relación con las líneas del comprobante
     */
    public function lines(): HasMany
    {
        return $this->hasMany(VoucherLine::class);
    }
}
