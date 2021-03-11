<?php
/**
 * This file is the front controller.
 */

use http\Request;
use http\Core;

/**
 * Application service part initialization
 */
require_once "../config/init.php";



$core = new Core(new Request());

$core->send();





