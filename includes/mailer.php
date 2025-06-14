<?php
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCodigoRedefinicao(string $emailDestino, int $codigo): bool {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seuemail@gmail.com';         // Seu e-mail
        $mail->Password   = 'sua_senha_de_aplicativo';    // Senha de App do Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('seuemail@gmail.com', 'Sistema GRI');
        $mail->addAddress($emailDestino);

        $mail->isHTML(true);
        $mail->Subject = 'Código de redefinição de senha';
        $mail->Body    = "<p>Olá,</p><p>Seu código é: <strong>$codigo</strong></p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar: " . $mail->ErrorInfo);
        return false;
    }
}
