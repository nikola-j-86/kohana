<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller {

    const REQUIRED_ROLE = 'admin';

    public function before() {
        parent::before();

// Check if the user is logged in and has the required role
        $userRole = Session::instance()->get('user_role');
        $isLoggedIn = Session::instance()->get('user_logged_in');

        if (!$isLoggedIn || $userRole !== self::REQUIRED_ROLE) {
            // User is not logged in
            $this->redirect('/user/dashboard');
        }
    }

    public function action_index() {
        // Load the admin dashboard view
        $this->response->body(View::factory('admin/dashboard'));
    }

    public function action_getInvoices() {
        $invoices = ORM::factory('Paymentinvoice')
            ->find_all()
            ->as_array();

        $invoicesArray = [];
        foreach ($invoices as $invoice) {
            $invoicesArray[] = [
                'id' => $invoice->id,
                'details' => $invoice->details,
                'amount' => $invoice->amount,
                'status' => $invoice->status,
            ];
        }

        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(json_encode($invoicesArray));
    }

    public function action_updateInvoiceStatus() {
        $invoiceId = $this->request->post('invoice_id');
        $newStatus = $this->request->post('status');

        $invoice = ORM::factory('Paymentinvoice', $invoiceId);

        if ($invoice->loaded()) {
            $invoice->status = $newStatus;
            $invoice->save();

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode(['success' => true]));
        } else {
            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode(['success' => false, 'message' => 'Invoice not found']));
        }
    }

    public function action_addPaymentSystem() {
        $name = $this->request->post('name');
        $isActive = $this->request->post('is_active');

        $paymentSystem = ORM::factory('Paymentsystem');
        $paymentSystem->name = $name;
        $paymentSystem->is_active = $isActive;
        $paymentSystem->save();

        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(json_encode(['success' => true]));
    }

    public function action_editPaymentSystem() {
        $systemId = $this->request->post('system_id');
        $name = $this->request->post('name');
        $isActive = $this->request->post('is_active');

        $paymentSystem = ORM::factory('Paymentsystem', $systemId);

        if ($paymentSystem->loaded()) {
            $paymentSystem->name = $name;
            $paymentSystem->is_active = $isActive;
            $paymentSystem->save();

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode(['success' => true]));
        } else {
            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode(['success' => false, 'message' => 'Payment system not found']));
        }
    }

    // Add more actions to approve, cancel invoices, and manage payment systems
}