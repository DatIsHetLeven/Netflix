<?php
require_once APPROOT . "/libraries/mollie_api/vendor/autoload.php";
require_once APPROOT . "/libraries/mollie_api/src/CompatibilityChecker.php";
require_once APPROOT . "/libraries/mollie_api/src/MollieApiClient.php";
require_once APPROOT . '/controllers/invoiceController.php';

        
class PaymentController extends Autoloader {

    public function __construct() {
        $this->tickets = $this->model("Ticket");
        $this->events = $this->model("Event");
        $this->invoiceModel = $this->model("Invoice");
        $this->invoiceController = new InvoiceController();
    }

    public function proceedCheckOut() {
        if(!isset($_SESSION['loggedInCustomer'])) {
            header("Location: " . URLROOT . "/customer/login", 301);
            exit();
        } else {
            $data['errorMess'] = '';
            $this->view('payment/paymentMethod', $data);
        }
    }
    
    public function paymentMethodPage() {
        $data['errorMess'] = '';
        $this->view('payment/paymentMethod', $data);
    }

    public function checkPaymentMethod() {
        // issuer = choice
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $selectedPay = $_POST['payment'];
            $_SESSION['paymentMethod'] = $selectedPay;
            // $this->Payment($selectedPay);
            if(isset($_SESSION['loggedInCustomer'])) { 
                if(isset($_SESSION['paymentMethod'])) {
                    if(isset($_SESSION['selectedTickets'])) {
                        $data = [
                            "tickets" => "",
                            "events" => ""
                        ];
                        
                        $data["tickets"] = $this->tickets->getTicketsCartInfo();
                        $data["events"] = $this->events->getAllEvents();                
                        $data['errorMess'] = '';
                        $this->view('payment/paymentOverview', $data);
                    }
                } else {
                    $data['errorMess'] = 'Something went wrong, please try again.';
                    $this->view('payment/paymentMethod', $data);
                }
            } else {
                header("Location: " . URLROOT . "/customer/login");
            }
        }
    }

    public function finishCheckOut() {
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            if(isset($_GET['orderId'])) {
                $data['orderId'] = $_GET['orderId'];
                $this->view('payment/paymentPayed', $data);
            }
        }
    }

    public function ini() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey(PAYMENTKEY);
        return $mollie;
    }   
    
    public function Payment() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $selectPay = $_SESSION['paymentMethod'];
            $selectPayment = '';
            if($selectPay === 'ideal') {
                $selectPayment = \Mollie\Api\Types\PaymentMethod::IDEAL;
            } elseif ($selectPay === 'paypal') {
                $selectPayment = \Mollie\Api\Types\PaymentMethod::PAYPAL;
            } else {
                $selectPayment = \Mollie\Api\Types\PaymentMethod::CREDITCARD;
            }
            $price = number_format((float) $_POST['totalPrice'], 2);;
            try {
                $mollie = $this->ini();
                // First, let the customer pick the bank in a simple HTML form. This step is actually optional.
                
                // Generate a unique order id for this example. It is important to include this unique attribute
                // in the redirectUrl (below) so a proper return page can be shown to the customer.
                
                $tempId = $this->invoiceModel->makeInvoiceId();
                if(empty($tempId->id)) {
                    $tempId = 1;
                } else {
                    $tempId = $tempId->id;
                }
                $orderId = $tempId; 
                $totalPrice = '';
                // Determine the url parts to these example files.
                $payment = $mollie->payments->create([
                    "amount" => [
                        "currency" => "EUR",
                        "value" => (string)$price, // You must send the correct number of decimals, thus we enforce the use of strings
                    ],
                    "method" => $selectPayment,
                    "description" => "Haarlem Festival Ticket order #{$orderId}",
                    
                    // // test
                    // "redirectUrl" => REMOTEURLROOT . "/Payment/finishCheckOut?orderId={$orderId}",
                    // "webhookUrl" => REMOTEURLROOT . "/Webhook/checkWebhook",
                    
                    "redirectUrl" => URLROOT . "/Payment/finishCheckOut?orderId={$orderId}",
                    "webhookUrl" => URLROOT . "/Webhook/checkWebhook",
                    
                    
                    
                    "metadata" => [
                        "order_id" => $orderId,
                    ],
                    "issuer" => ! empty($_POST["issuer"]) ? $_POST["issuer"] : null,
                ]);
                // In this example we store the order with its payment status in a database.
                $this->invoiceController->makeInvoice();
                /*
                * Send the customer off to complete the payment.
                * This request should always be a GET, thus we enforce 303 http response code
                */
                header("Location: " . $payment->getCheckoutUrl(), true, 303);
            } catch (\Mollie\Api\Exceptions\ApiException $e) {
                echo "API call failed: " . \htmlspecialchars($e->getMessage());
            }
        }
        
    }
}
