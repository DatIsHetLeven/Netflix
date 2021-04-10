<?php

use setasign\Fpdi\PdfParser\Type\PdfName;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once APPROOT . '/libraries/vendor/autoload.php';
require_once APPROOT . '/libraries/phpqrcode/qrlib.php';
require_once APPROOT . '/controllers/EmailSender.php';



class InvoiceController extends Autoloader {

  private $emailSender;

  public function __construct()
  {
    $this->emailSender = new EmailSender;
    $this->Invoice = $this->model("Invoice");
    $this->Ticket = $this->model("Ticket");
  }

    public function makeInvoice(){
      $data = [
        'Price' => '',
        'Participants' => '',
        'Comment' => '',
        'InvoiceId' => '',
        'TicketId' => '',
        'error' => '',
        'feedback' => ''
      ];


      if (isset($_SESSION["loggedInCustomer"])) {
        $id = $this->Invoice->makeInvoiceId();
        $invoiceId = $id->id + 1;
        $purchaseDate = date('Y-m-d H:i:s');

        $this->Invoice->createInvoice($invoiceId ,$_SESSION['loggedInCustomer']->getId(), $purchaseDate);

        if(!empty($_SESSION["selectedTickets"])){

          foreach ($_SESSION["selectedTickets"] as $ticket) {
            $data['SelectedTicketId'] = $ticket->SelectedTicketId;
            $data['Price'] = $ticket->Price;
            $data['Participants'] = $ticket->Participants;
            $data['Comment'] = $ticket->Comment;
            $data['InvoiceId'] = $invoiceId;
            $data['TicketId'] = $ticket->TicketId;

            $this->Ticket->makeSelectedTicket($data);

          }
          unset($_SESSION["selectedTickets"]);
          $this->sendPdf($invoiceId,$purchaseDate);

        }else{
          header('location: ' . URLROOT . '/pages/index');
        }
      }
      else {
        header('location: ' . URLROOT . '/customer/login');
      }
    }

    private function sendPdf($invoiceId){
      // 1-Make pdf for the created invoice
      $pdf = $this->makePdf($invoiceId);
      // 2-Pass the pdf name to the mailer
      $pdfName = 'Invoice Number ' . $invoiceId. '.pdf';
      $pdfString = $pdf->Output($pdfName , 'S');


      if(!empty($pdf)){
        // 3-Pass the created pdf to the email sender
        $this->emailSender->sendEmailWithPdf($pdfString,$pdfName ,$_SESSION['loggedInCustomer']->getEmailAddress());
      }
      
      $data['pdf'] = $pdf;
    }

    public function makePdf($invoiceId){

        // create a new pdf instance
        $pdf = new \Mpdf\Mpdf();

        // get the pdf layout
        $layout = $this->createPdfLayout($invoiceId);


        $pdf->WriteHTML($layout);

        return $pdf;
    }

    private function createPdfLayout($invoiceId){

      $invoice = $this->Invoice->getInvoice($invoiceId);


        //create the layout
        $data = '';
        $data .= '<h2 style="text-align: center;">Invoice Number: ' . $invoiceId. '</h2>';



        $data .= '<h1>Haarlem Festival</h1><br>';
        $data .= '<strong>Website: </strong>shekho.tech/HaarlemFestival</h4>';
        $data .= '<strong>Telephone Number: </strong>0684588703</h4>';
        $data .= '<strong>Date: </strong>' . date("d/M/y H:i:s") . '<br/><br/><br/><br/>';

        // add data

        $data .= '<strong>Purchase Date: </strong>' . $invoice->PurchaseDate. '<br/>';
        $data .= '<strong>Email: </strong>' . $_SESSION['loggedInCustomer']->getEmailAddress(). '<br/><br/><br/><br/>';


        $tickets = $this->Ticket->getSelecetdTickets($invoiceId);

        $data .= '<h2 style="text-align: center;">Invoice Number: ' . $invoiceId. '</h2>';

        // make the table layout
        $data .= '
                <table style="margin: 0 auto; width: 100%; border-collapse: collapse; font-size:25px; ">
                    <tr>
                        <th style="background-color: #ff523b; text-align: left; ">Name</th>
                        <th style="background-color: #ff523b; text-align: left; ">Participants</th>
                        <th style="background-color: #ff523b; text-align: left; ">Date</th>
                        <th style="background-color: #ff523b; text-align: left; ">Price</th>
                    </tr>

                ';

        $total = 0;


        foreach($tickets as $ticket){


            $data .= '<tr><td style="padding: 10px 5px; font-size: 12px;"><img style="float: left; width: 80px;
            height: 80px; margin-right: 10px;" >' . $ticket->Name. '</td>';
            $data .= '<td style="padding: 10px 5px; font-size: 12px;">' . $ticket->Participants . '</td>';
            $data .= '<td style="padding: 10px 5px; font-size: 12px;">' . $ticket->Date . '</td>';
            $data .= '<td style="padding: 10px 5px; font-size: 12px;"> €' . $ticket->Price . '</td></tr>';

                $total += $ticket->Price;
            }

        $data .= '</table>';

        // add second table consit of three table rows
        $data .= '<table style="display: flex; justify-content: flex-end; border-top: 3px solid #ff523b;width: 100%; max-width: 400px;"><tr style=" text-align: right;"><td style=" text-align: right;">Subtotal</td><td style=" text-align: right;"> €' . number_format($subTotal =  $total  - ($total * 0.21), 2, '.', '') . '</td></tr>';
        $data .= '<tr style=" text-align: right;"><td style=" text-align: right;">Tax</td><td style=" text-align: right;"> €' . number_format($tax =  $total * 0.21, 2, '.', '') . '</td></tr>';
        $data .= '<tr style=" text-align: right;"><td style=" text-align: right;">Total</td><td style=" text-align: right;"> €' . number_format($total, 2, '.', '') . '</td></tr></table><br>';

        $qrLayout = $this->makeQrLayout($invoiceId);

        $QR = $this->generateQR($qrLayout);

        $data .= "<img src='" . $QR. "'>";


        return $data;
    }

    private function generateQR($text){
        $path = ROOT . '/public/QR/';
        $file = $path.uniqid().".png";

        // Add the QR code img to the QR img folder
        QRcode::png($text, $file);

        return $file;
    }

    private function makeQrLayout($id){
        $layout = 'Haarlem Festival';
        $layout .= 'Website: http://localhost/haarlemfestival/ ';
        $layout .= 'Telephone Number: 0684588703';
        $layout .= 'Invoice Number: ' . $id;

        return $layout;
    }
}
