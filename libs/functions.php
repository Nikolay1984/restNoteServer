<?php
/**
 * This file contains frequently used functions
 */

/**
 * Function for debugging and stopping the script
 *
 * @param mixed $var
 * @param bool $die
 * @return void
 */
function debug($var, $die = false){
    echo "<pre>" . print_r($var,true) . "</pre>";
    if($die){
        die("Die!");
    }
}

/**
 * Set headers for CORS policy
 *
 * @return void
 */
function setCORSHeader(){
    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Headers:*, Authorization");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Credentials:true");
    header("Access-Control-Expose-Headers: location");
}

/**
 * Registering our global error handler on the server
 *
 * @return void
 */
function registrationErrorApi(){
    if(!DEBUG){
        ob_start();
        register_shutdown_function(function () {
            if(error_get_last()){
                ob_clean();
                debug( error_get_last());

                generationErrorApi('error_global');
            }
        });
    }

}

/**
 * Override the default error output and sends a response string with code and error message to the browser.
 *
 * @param string $statusError set from init.php constant CODE_MESSAGE_ERROR
 * @param string $codeError custom status code
 * @param string $messageError custom status message
 * @return void
 */
function generationErrorApi($statusError, $codeError = "", $messageError = ""){
    if(!DEBUG){
        error_clear_last();

        if($statusError){
            $codeError = CODE_MESSAGE_ERROR[$statusError][0];
            $messageError = CODE_MESSAGE_ERROR[$statusError][1];
        }
        header( "HTTP/1.0 " .  $codeError . " " . $messageError);

        echo '';
        die();
    }

}