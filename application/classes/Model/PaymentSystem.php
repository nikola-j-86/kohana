<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_PaymentSystem extends ORM {
    // If your table name doesn't match the model name, define it explicitly
    protected $_table_name = 'payment_systems';

    // Define the primary key if it's not 'id'
    protected $_primary_key = 'id';
}