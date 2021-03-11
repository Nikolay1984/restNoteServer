<?php
/**
 * This file sets the initial properties of the application
 */


//> This block defines application constants
define("DEBUG",0 );
define("ROOT", dirname(__DIR__) );
define("WWW", ROOT.'/public_html' );
define("LIBS", ROOT.'/libs' );
define("CONF", ROOT.'/config' );
define("DB_CONNECTION", require_once CONF."/config_db.php");
define("CODE_MESSAGE_SUCCESS", [
    'success_POST' => [201,"POST request successful. Created"],
    'success_GET' => [200,"GET request successful"],
    'success_GET_ID' => [200,"GET_ID request successful. OK"],
    'success_PUT' => [204,"PUT request successful. No Data"],
    'success_DELETE' => [204,"DELETE request successful. No Data"],
    'success_OPTIONS' => [200,"OPTIONS request successful. OK"],
    'resource_NOT_FOUND' => [404,"Not Found"]
]);// Set the text and  status code for a database connection successful for various request methods.
define("CODE_MESSAGE_ERROR", [
    'error_db_connection' => [503,"error_db_connection"],
    'error_global' => [500 ,"Server error"],
    'error_POST' => [502,"POST method error"],
    'error_GET' => [502,"GET method error"],
    'error_PUT' => [502,"PUT method error"],
    'error_DELETE' => [502,"DELETE method error"],
    'error_GET_ID' => [502,"GET_ID  method error"]

]); // Set the text and error status code for a database connection error for various request methods.

//<

//> Connecting the necessary classes and useful functions
require_once ROOT . '/vendor/autoload.php';
require_once LIBS . '/functions.php';
//<


setCORSHeader();
registrationErrorApi();
