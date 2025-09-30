<?php

namespace Source\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Emailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'equipeensaiei@gmail.com';
        $this->mailer->Password = 'oufw tmih rjbw qlra';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';

        $this->mailer->setFrom('equipeensaiei@gmail.com', 'Equipe Ensaiei 🎭');
    }

    public function sendVerificationEmail(string $toEmail, string $toName, string $code): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Confirme seu email - Ensaiei';

            $this->mailer->Body = $this->getVerificationEmailTemplate($toName, $code);
            $this->mailer->AltBody = "Olá {$toName},\n\nSeu código de verificação é: {$code}\n\nEste código expira em 30 minutos.\n\nSe você não criou esta conta, ignore este email.";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    private function getVerificationEmailTemplate(string $name, string $code): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { 
                    font-family: 'Arial', sans-serif; 
                    background-color: #fff3f5; 
                    margin: 0; 
                    padding: 20px; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 12px; 
                    overflow: hidden; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #e5005a 0%, #ad1330 100%); 
                    color: white; 
                    padding: 30px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 32px; 
                    font-weight: 600; 
                }
                .content { 
                    padding: 40px 30px; 
                }
                .content h2 { 
                    color: #333; 
                    margin-bottom: 20px; 
                }
                .code-box { 
                    background: #fff3f5; 
                    border: 2px dashed #e5005a; 
                    border-radius: 8px; 
                    padding: 30px; 
                    text-align: center; 
                    margin: 30px 0; 
                }
                .code { 
                    font-size: 36px; 
                    font-weight: bold; 
                    color: #e5005a; 
                    letter-spacing: 8px; 
                    font-family: 'Courier New', monospace; 
                }
                .info { 
                    color: #666; 
                    font-size: 14px; 
                    line-height: 1.6; 
                    margin-top: 20px; 
                }
                .footer { 
                    background: #f8f8f8; 
                    padding: 20px 30px; 
                    text-align: center; 
                    color: #999; 
                    font-size: 12px; 
                }
                .warning {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Ensaiei</h1>
                </div>
                <div class='content'>
                    <h2>Olá, {$name}!</h2>
                    <p>Bem-vindo ao Ensaiei! Para completar seu cadastro, confirme seu endereço de email usando o código abaixo:</p>
                    
                    <div class='code-box'>
                        <div class='code'>{$code}</div>
                    </div>
                    
                    <p class='info'>
                        ⏱️ <strong>Este código expira em 30 minutos.</strong><br>
                        📧 Insira o código na página de verificação para ativar sua conta.
                    </p>
                    
                    <div class='warning'>
                        <strong>⚠️ Não criou esta conta?</strong><br>
                        Se você não se cadastrou no Ensaiei, pode ignorar este email com segurança.
                    </div>
                </div>
                <div class='footer'>
                    <p>© 2025 Ensaiei. Todos os direitos reservados.</p>
                    <p>Esta é uma mensagem automática, por favor não responda.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
