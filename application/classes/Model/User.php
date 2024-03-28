<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends ORM {

    protected $_table_name = 'users'; // Assuming your table name is 'users'

    public function get_user($id) {
        // Assuming there's a DB setup and configuration in place
        $query = DB::select()->from($this->_table_name)
            ->where('id', '=', $id)
            ->execute()
            ->as_array();

        if (count($query) > 0) {
            return $query[0]; // Return the first user found
        }

        return null; // No user found

//        return ORM::factory('User', $id);
    }
}