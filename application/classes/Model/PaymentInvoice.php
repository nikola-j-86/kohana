<?php defined('SYSPATH') or die('No direct access to script');

class Model_PaymentInvoice extends ORM {

    protected $_table_name = 'payment_invoices';

    protected $_belongs_to = array(
        'user' => array(
            'model'       => 'User',
            'foreign_key' => 'user_id',
        ),
        'payment_system' => array(
            'model'       => 'PaymentSystem',
            'foreign_key' => 'payment_system_id',
        ),
    );

    protected $_primary_key = 'id';
}