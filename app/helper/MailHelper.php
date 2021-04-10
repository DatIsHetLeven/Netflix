<?php
use setasign\Fpdi\PdfParser\Type\PdfName;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once APPROOT . '/libraries/vendor/autoload.php';

abstract class MailHelper {
    public static function send($recipient, $subject, $content) {
        $client = new PHPMailer();
        $client->isSMTP();
        $client->Host = "smtp.gmail.com";
        $client->SMTPAuth = true;
        $client->Username = "rster2002app@gmail.com";
        $client->Password = MAILPASSWORD;
        $client->SMTPSecure = "tls";
        $client->Port = 587;
//        $client->SMTPDebug = SMTP::DEBUG_SERVER;

        $client->setFrom("rster2002app@gmail.com", "Noreply");
        $client->addAddress($recipient);

        $client->isHTML(true);

        $client->Subject = $subject;
        $client->Body = $content;

//        if (!$success) echo 'Mailer Error: ' . $client->ErrorInfo;

        return $client->send();
    }
}