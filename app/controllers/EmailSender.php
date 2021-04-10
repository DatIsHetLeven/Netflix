<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once APPROOT . '/libraries/vendor/autoload.php';


class EmailSender{

    public function sendEmailWithPdf($pdf,$pdfName,$email){
        $sended = $this->sendEmail($pdf, $pdfName, $email);
        return $sended;
    }

    private function sendEmail($pdf, $pdfName, $email){

        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {

            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.strato.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'shekho@shekho.tech';                     //SMTP username
            $mail->Password   = 'Yr7aeU6sSpbMvR5';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('Shekho@shekho.tech', 'Haarlem Festival');

            $mail->addAddress($email);     //Add a recipient
            $mail->addAddress('shekho@shekho.tech');     //Send to the admin

            // Make the email body
            $emailBody = '';
            $subject = '';

            // Add attachment
            if(!empty($pdf)){
                $mail->addStringAttachment($pdf, $pdfName);
                $emailBody = $this->makeEmailLayoutForPdf($email);
                $subject = $pdfName;
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $emailBody;
            $mail->AltBody = strip_tags($emailBody);

            $mail->send();

            return true;

        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    private function makeEmailLayoutForPdf($email){
        $data = '';

        // Add the sender information
        $data .= '<h1>Haarlem Festival</h1><br>';
        $data .= '<strong>Website: </strong>shekho.tech/HaarlemFestival<br/>';
        $data .= '<strong>Telephone Number: </strong>0684588703<br/>';
        $data .= '<strong>Date: </strong>' . date("d/M/y H:i:s") . '<br/><br/><br/>';


        // add data
        $data .= '<strong>Email: </strong>' . $email. '<br/><br/><br/><br/>';


        $data .= '<p>Thank you for your order, your order has been sent as an attachment</p>.';

        return $data;
    }
}
