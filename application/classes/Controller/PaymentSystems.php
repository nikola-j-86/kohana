<?php defined('SYSPATH') or die('No direct script access.');

class Controller_PaymentSystems extends Controller {

    public function before() {
        parent::before();

        // Check if the user is logged in
        $userRole = Session::instance()->get('user_role');
        $isLoggedIn = Session::instance()->get('user_logged_in');

        if (!$isLoggedIn || $userRole !== Controller_Admin::REQUIRED_ROLE) {
            // User is not logged in
            $this->redirect('/user/dashboard');
        }
    }

    public function action_create() {
        if ($this->request->method() == 'POST') {
            // Extract data from POST request
            $name = $this->request->post('systemName');
            $active = $this->request->post('isActive') === '1';

            // Create a new PaymentSystem object
            $paymentSystem = ORM::factory('PaymentSystem');
            $paymentSystem->name = $name;
            $paymentSystem->is_active = $active;
            $paymentSystem->created_by = Session::instance()->get('user_id');
            $paymentSystem->save();

            // Return a JSON response or redirect
            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode(['success' => true]));
        }
    }

    public function action_get() {
        $paymentSystems = ORM::factory('PaymentSystem')
            ->find_all()
            ->as_array(); // Convert the result to an array

        $paymentSystemsArray = [];
        foreach ($paymentSystems as $paymentSystem) {
            $paymentSystemsArray[] = [
                'id' => $paymentSystem->id,
                'created_by' => $paymentSystem->created_by,
                'is_active' => $paymentSystem->is_active == '1' ? true : 0,
                'name' => $paymentSystem->name
            ];
        }

        // Return the data as JSON
        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(json_encode($paymentSystemsArray));
    }

    public function action_edit() {
        $systemId = $this->request->param('id');

        $paymentSystem = ORM::factory('PaymentSystem', $systemId);
        if (!$paymentSystem->loaded()) {
            throw HTTP_Exception::factory(404, 'Payment system not found.');
        }

        // Assuming the payment system data is returned as JSON
        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(json_encode(array(
            'id' => $paymentSystem->id,
            'name' => $paymentSystem->name,
            'is_active' => $paymentSystem->is_active,
        )));
    }

    public function action_update() {
        $systemId = $this->request->param('id');
        $isActive = $this->request->post('isActive'); // Adjust based on what you send in the request

        $paymentSystem = ORM::factory('PaymentSystem', $systemId);
        if ($paymentSystem->loaded()) {
            $paymentSystem->is_active = $isActive;
            $paymentSystem->save();
            $this->response->body(json_encode(['success' => true]));
        } else {
            $this->response->body(json_encode(['success' => false, 'error' => 'Payment system not found']));
        }
    }
}