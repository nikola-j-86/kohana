<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL']))
{
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   => '/kohana/',
    'index_file' => '',
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	 'auth'       => MODPATH.'auth',       // Basic authentication
	// 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	 'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
    // 'minion'     => MODPATH.'minion',     // CLI Tasks
	 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    // Other modules...

	));

/**
 * Cookie Salt
 * @see  http://kohanaframework.org/3.3/guide/kohana/cookies
 * 
 * If you have not defined a cookie salt in your Cookie class then
 * uncomment the line below and define a preferrably long salt.
 */
// Cookie::$salt = NULL;

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

// Dashboards
Route::set('admin_dashboard', 'admin/dashboard')
    ->defaults(array(
        'controller' => 'Admin',
        'action'     => 'index',
    ));

Route::set('user_dashboard', 'user/dashboard')
    ->defaults(array(
        'controller' => 'User',
        'action'     => 'index',
    ));

// Authentication
Route::set('login', 'login')
    ->defaults(array(
        'controller' => 'Login',
        'action'     => 'index',
    ));

Route::set('logout', 'logout')
    ->defaults(array(
        'controller' => 'Login',
        'action'     => 'logout',
    ));

Route::set('authenticate', 'login/authenticate', array('method' => 'POST'))
    ->defaults(array(
        'controller' => 'Login',
        'action'     => 'authenticate',
    ));

// Payment Systems
Route::set('create_payment_system', 'payment-systems/create', array('method' => 'POST'))
    ->defaults(array(
        'controller' => 'PaymentSystems',
        'action'     => 'create',
    ));

Route::set('get_payment_systems', 'payment-systems/get')
    ->defaults(array(
        'controller' => 'PaymentSystems',
        'action'     => 'get',
    ));

Route::set('edit_payment_system', 'payment-systems/<id>/edit', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'PaymentSystems',
        'action'     => 'edit',
    ));

Route::set('update_payment_system', 'payment-systems/<id>/update', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'PaymentSystems',
        'action'     => 'update',
    ));

// Invoices
Route::set('create_invoice', 'invoices/create', array('method' => 'POST'))
    ->defaults(array(
        'controller' => 'Invoices',
        'action'     => 'create',
    ));

Route::set('get_invoices', 'invoices/get')
    ->defaults(array(
        'controller' => 'Invoices',
        'action'     => 'get',
    ));

Route::set('all_invoices', 'invoices/all')
    ->defaults(array(
        'controller' => 'Invoices',
        'action'     => 'all',
    ));

Route::set('approve_invoice', 'invoices/<id>/approve', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Invoices',
        'action'     => 'approve',
    ));

Route::set('cancel_invoice', 'invoices/<id>/cancel', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Invoices',
        'action'     => 'cancel',
    ));

Route::set('download_invoice', 'invoices/<id>/download', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Invoices',
        'action'     => 'download',
    ));

// Default route
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));

Cookie::$salt = 'dvjskjttkjyvr98uy6e59byum8496yb3485b8[b59ubyu64v5o308[0';

