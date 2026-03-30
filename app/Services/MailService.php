<?php

namespace App\Services;

/**
 * Placeholder para o serviço de envio de email. 
 * Substitua este conteúdo pela sua implementação real do PHP Mailer.
 */
class MailService
{
    /**
     * Simula o envio de um email.
     * @param string $toEmail O email do destinatário.
     * @param string $subject O assunto do email.
     * @param string $body O corpo (conteúdo) do email.
     * @return bool
     */
    public static function send(string $toEmail, string $subject, string $body): bool
    {
        // === PONTO DE INTEGRAÇÃO COM PHPMailer ===
        // Você deve configurar e usar o PHP Mailer aqui.
        
        // Log ou simulação do envio:
        error_log("EMAIL SIMULADO ENVIADO para $toEmail: $subject. Conteúdo: $body");
        
        return true; // Assume sucesso na simulação
    }
}