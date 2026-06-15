<?php

require_once __DIR__ . '/../vendor/autoload.php';

class EmailSender {

    // Resend API Key
    private const RESEND_API_KEY = 're_GLYWxyUA_HqDCD7GiARbp8WkRgj8CQPUu';

    public static function sendMissionAcceptedEmail($toEmail, $userName, $pdfPath) {
        if (!file_exists($pdfPath)) {
            throw new Exception("PDF file not found at: " . $pdfPath);
        }

        $resend = Resend::client(self::RESEND_API_KEY);

        $pdfContent = file_get_contents($pdfPath);
        $base64Pdf = base64_encode($pdfContent);

        $htmlContent = self::getEmailHtml($userName);

        try {
            $result = $resend->emails->send([
                'from' => 'Parc Auto <onboarding@resend.dev>',
                'to' => [$toEmail],
                'subject' => 'Votre demande de mission a été acceptée',
                'html' => $htmlContent,
                'attachments' => [
                    [
                        'filename' => 'Ordre_Mission.pdf',
                        'content' => $base64Pdf,
                    ]
                ]
            ]);

            return $result;
        } catch (\Exception $e) {
            error_log("Failed to send email: " . $e->getMessage());
            throw $e;
        }
    }

    private static function getEmailHtml($userName) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e4ebf4; }
                .header { background-color: #0066cc; padding: 30px; text-align: center; }
                .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 0.5px; }
                .content { padding: 40px 30px; color: #334155; line-height: 1.6; }
                .content p { margin-bottom: 20px; font-size: 16px; }
                .highlight { font-weight: 600; color: #001737; }
                .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #8799ae; font-size: 13px; border-top: 1px solid #e4ebf4; }
                .btn-container { text-align: center; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Mission Acceptée</h1>
                </div>
                <div class='content'>
                    <p>Bonjour <span class='highlight'>{$userName}</span>,</p>
                    <p>Nous avons le plaisir de vous informer que votre demande de mission a été <span class='highlight'>acceptée</span> par l'administration.</p>
                    <p>Vous trouverez ci-joint votre <strong>Ordre de Mission</strong> en format PDF. Veuillez l'imprimer et le conserver avec vous lors de votre déplacement.</p>
                    <p>Nous vous souhaitons un excellent voyage professionnel.</p>
                    <br>
                    <p>Cordialement,<br><strong>Le service Parc Automobile</strong></p>
                </div>
                <div class='footer'>
                    Ce message a été envoyé automatiquement, merci de ne pas y répondre.
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
