<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller {

    const REQUIRED_ROLE = 'user';

    public function before() {
        parent::before();

        // Check if the user is logged in and has the required role
        $userRole = Session::instance()->get('user_role');
        $isLoggedIn = Session::instance()->get('user_logged_in');

        if (!$isLoggedIn || $userRole !== self::REQUIRED_ROLE) {
            // User is not logged in
            $this->redirect('/admin/dashboard');
        }
    }

    public function action_index() {
        $payment_systems = ORM::factory('PaymentSystem')
            ->where('is_active', '=', 1)
            ->find_all();
        $view = View::factory('user/dashboard')
            ->set('payment_systems', $payment_systems);
        $this->response->body($view);
    }
}