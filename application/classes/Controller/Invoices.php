<?php defined('SYSPATH') or die('No direct script access.');

require DOCROOT . 'vendor/autoload.php';

class Controller_Invoices extends Controller {

    public function before() {
        parent::before();

        // Check if the user is logged in and has the required role
        $isLoggedIn = Session::instance()->get('user_logged_in');

        if (!$isLoggedIn) {
            // User is not logged in
            $this->redirect('/login');
        }
    }

    public function action_create() {
        if ($this->request->method() == 'POST') {
            // Extract data from POST request
            $paymentSystemId = $this->request->post('paymentSystemId');
            $details = $this->request->post('details');
            $amount = $this->request->post('amount');

            // Here, you would typically save this data to the database
            // For example, using ORM:
             $invoice = ORM::factory('PaymentInvoice');
             $invoice->payment_system_id = $paymentSystemId;
             $invoice->details = $details;
             $invoice->amount = $amount;
             $invoice->status = 'creating';
             $invoice->user_id = Session::instance()->get('user_id');
             $invoice->created_by = Session::instance()->get('user_id');
             $invoice->save();

            // Return a JSON response or redirect
            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode(['success' => true]));
        }
    }

    public function action_get() {
        $invoices = ORM::factory('PaymentInvoice')
            ->where('user_id', '=', Session::instance()->get('user_id'))
            ->find_all()
            ->as_array(); // Convert the result to an array

        $invoicesArray = [];
        foreach ($invoices as $invoice) {
            $invoicesArray[] = [
                'id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'payment_system_id' => $invoice->payment_system_id,
                'details' => $invoice->details,
                'amount' => $invoice->amount,
                'status' => $invoice->status,
            ];
        }

        // Return the data as JSON
        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(json_encode($invoicesArray));
    }

    public function action_all() {
        $invoices = ORM::factory('PaymentInvoice')
            ->where('status', '=', 'creating')
            ->find_all()
            ->as_array(); // Convert the result to an array

        $invoicesArray = [];
        foreach ($invoices as $invoice) {
            $invoicesArray[] = [
                'id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'payment_system_id' => $invoice->payment_system_id,
                'details' => $invoice->details,
                'amount' => $invoice->amount,
                'status' => $invoice->status,
            ];
        }

        // Return the data as JSON
        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(json_encode($invoicesArray));
    }

    public function action_approve() {
        $invoiceId = $this->request->param('id');

        if ($this->request->method() === Request::POST) {
            // Logic to approve the invoice
            $invoice = ORM::factory('PaymentInvoice', $invoiceId);
            if ($invoice->loaded()) {
                $invoice->status = 'approved';
                $invoice->save();

                $this->response->body(json_encode(array('success' => true)));
            } else {
                // Handle the error case
                $this->response->body(json_encode(array('success' => false, 'error' => 'Invoice not found')));
            }
        }
    }

    public function action_cancel() {
        $invoiceId = $this->request->param('id');

        if ($this->request->method() === Request::POST) {
            // Logic to cancel the invoice
            $invoice = ORM::factory('PaymentInvoice', $invoiceId);
            if ($invoice->loaded()) {
                $invoice->status = 'cancelled';
                $invoice->save();

                $this->response->body(json_encode(array('success' => true)));
            } else {
                // Handle the error case
                $this->response->body(json_encode(array('success' => false, 'error' => 'Invoice not found')));
            }
        }
    }

    public function action_download() {
        $invoiceId = $this->request->param('id');

        // Load the invoice from the database
        $invoice = ORM::factory('PaymentInvoice', $invoiceId);
        if (!$invoice->loaded()) {
            throw new HTTP_Exception_404('Invoice not found.');
        }

        // Generate PDF content (you might use a library like TCPDF or mPDF)
        $pdfContent = $this->generateInvoicePdf($invoice);

        // Set the appropriate headers to force download
        $this->response->headers('Content-Type', 'application/pdf');
        $this->response->headers('Content-Disposition', 'attachment; filename="invoice_' . $invoiceId . '.pdf"');
        $this->response->headers('Content-Length', strlen($pdfContent));
        $this->response->send_headers();

        // Output the PDF content
        echo $pdfContent;
        exit;
    }

    private function generateInvoicePdf($invoice) {
        // Initialize mPDF
        $dompdf = new \Dompdf\Dompdf();

        // Construct the HTML content for the PDF
        $htmlContent = '
        <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .invoice-header { text-align: center; }
                    .invoice-details { margin-top: 20px; }
                    .invoice-details th, .invoice-details td { text-align: left; padding: 8px; border: 1px solid #ddd; }
                    .invoice-details th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <div class="invoice-header">
                    <h1>Invoice #' . $invoice->id . '</h1>
                </div>
                <div class="invoice-details">
                    <table width="100%">
                        <tr>
                            <th>Details</th>
                            <td>' . htmlspecialchars($invoice->details) . '</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>' . htmlspecialchars($invoice->amount) . '</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>' . htmlspecialchars($invoice->status) . '</td>
                        </tr>
                        <!-- Add more invoice details as needed -->
                    </table>
                </div>
            </body>
        </html>
    ';

        $dompdf->loadHtml($htmlContent);

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF as a string
        return $dompdf->output();
    }

}