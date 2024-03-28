<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller {

    public function action_index() {
        // Check if the user is already logged in
        if (Session::instance()->get('user')) {
            // User is logged in
            $view = View::factory('already_logged_in');
        } else {
            // User is not logged in
            $view = View::factory('login');
        }
        $this->response->body($view);
    }

    public function action_logout() {
        // Clear the user session
        Session::instance()->delete('user');
        Session::instance()->destroy(); // Optional: completely destroy the session

        // Redirect to the home page or login page
        $this->redirect('/login');
    }

    public function action_authenticate() {
        // From what I learned about Kohana, while retrieving data through $this->request->post()...
        // ...framework automatically applies a basic level of sanitization to prevent the most common issues
        // ...Kohana's ORM or Query Builder automatically handles parameter escaping, significantly reducing the risk of SQL injection.
        $username = $this->request->post('username');
        $password = $this->request->post('password');

        // Find the user with the provided username
        $user = ORM::factory('User')
            ->where('username', '=', $username)
            ->find();


        // Check if the user exists and the password matches
        if ($user->loaded() && $user->password === $password) {
            // Passwords should be hashed and checked with password_verify in production...
            // ...in production passwords would be compared using password_verify($password, $user->password)
            Session::instance()->set('user_logged_in', true);
            Session::instance()->set('user', $username);
            Session::instance()->set('user_id', $user->id);
            Session::instance()->set('user_role', $user->role);

            // Respond with success status and redirection URL, don't redirect here
            $this->response->headers('Content-Type', 'application/json');
            echo json_encode([
                'success' => true,
                'redirect' => $user->role === 'admin' ? '/kohana/admin/dashboard' : '/kohana/user/dashboard',
            ]);
        } else {
            // Authentication failed, redirect back to login form with an error message
            Session::instance()->set('error', 'Invalid username or password');
            $this->redirect('/login');
        }
    }
}