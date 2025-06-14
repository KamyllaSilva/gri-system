<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Ajuste o caminho para o seu arquivo autoload.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCodigoRedefinicao(string $emailDestino, int $codigo): bool {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP (exemplo Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seuemail@gmail.com';
        $mail->Password   = 'sua_senha_de_aplicativo';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('seuemail@gmail.com', 'Sistema GRI');
        $mail->addAddress($emailDestino);

        $mail->isHTML(true);
        $mail->Subject = 'Redefinição de senha - Código de verificação';
        $mail->Body    = "<p>Olá,</p><p>Seu código de redefinição de senha é: <strong>$codigo</strong></p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}
