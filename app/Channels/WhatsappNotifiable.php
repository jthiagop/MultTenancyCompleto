<?php

namespace App\Channels;

/**
 * Contrato para notificações que suportam envio via WhatsApp.
 * Implementar em qualquer Notification junto com WhatsappChannel::class no via().
 */
interface WhatsappNotifiable
{
    /**
     * Retorna o texto a ser enviado via WhatsApp para o usuário notificável.
     */
    public function toWhatsapp(object $notifiable): string;
}
