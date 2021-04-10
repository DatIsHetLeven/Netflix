<?php
require_once APPROOT . '/controllers/PaymentController.php';

class WebhookController extends PaymentController {
    public function __construct()
    {
        $this->invoiceModel = $this->model("Invoice");
    }

    public function checkWebhook() {
        try {
            // Initialize the Mollie API library with your API key.
            $mollie = $this->ini();
            
            // Retrieve the payment's current state.
            $payment = $mollie->payments->get($_POST["id"]);
            $orderId = $payment->metadata->order_id;
            // Update the order in the database.
            $tempUpdate = $this->invoiceModel->updateStatusInvoice($orderId, $payment->status);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            echo "API call failed: " . \htmlspecialchars($e->getMessage());
        }
    }
}
