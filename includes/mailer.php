<?php
require_once 'includes/phpmailer/PHPMailer.php';
require_once 'includes/phpmailer/SMTP.php';
require_once 'includes/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require __DIR__ . '/../vendor/autoload.php'; // Certifique-se que o caminho está correto

function enviarCodigoRedefinicao(string $emailDestino, int $codigo): bool {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP (exemplo usando Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seuemail@gmail.com';       // Seu e-mail Gmail
        $mail->Password   = 'sua_senha_de_aplicativo';  // Use uma senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('seuemail@gmail.com', 'Sistema GRI');
        $mail->addAddress($emailDestino);

        // Conteúdo
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
