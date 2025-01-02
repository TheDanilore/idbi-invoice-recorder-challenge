<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Clase que define un correo para notificar la creación de comprobantes.
 */
class VouchersCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Lista de comprobantes procesados.
     * 
     * @var array
     */
    public array $vouchers;

    /**
     * Usuario asociado a los comprobantes.
     * 
     * @var User
     */
    public User $user;

    /**
     * Constructor de la clase.
     *
     * @param array $vouchers Lista de comprobantes procesados.
     * @param User $user Usuario autenticado.
     */
    public function __construct(array $vouchers, User $user)
    {
        $this->vouchers = $vouchers;
        $this->user = $user;
    }

    /**
     * Construye el correo con la vista y los datos necesarios.
     *
     * @return self
     */
    public function build(): self
    {
        return $this->view('emails.vouchers')
            ->subject('Subida de comprobantes') // Asunto del correo.
            ->with(['vouchers' => $this->vouchers, 'user' => $this->user]); // Datos para la vista.
    }
}
