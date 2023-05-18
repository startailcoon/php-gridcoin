<?php

/**
 * Wallet RPC API
 * 
 * This file is used to make it possible to host a wallet RPC API as a webservice.
 * Wallet RPC commands can be called by this API.
 *
 * This script can be used as a stand-alone by running the following command:
 * 'php -S <host>:<port> api.php'
 *
 * Running the PHP Server is NOT recommended outside development environment.
 * Best practice is to use a webserver like Apache or Nginx to host this API.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Wallet.php';

// Set the RPC connection details.
phpGridcoin\Wallet::setNode('localhost', '25717', 'gridcoinrpc', 'bkw75QgtWAAQpnU0MHR4qIQIfAqXR7OxdvHPHI6xI4VMQKXXEkpfPo2dT');

// Get the command and parameters from the URL.
$props = explode("/", substr($_SERVER['REQUEST_URI'],1));
$call = $props[0];
array_shift($props);

$result = null;

## TODO: This can be done better, but it works for now.
try {
    $result = phpGridcoin\Wallet::{$call}(...$props);
} catch (\ArgumentCountError $e) {
    phpGridcoin\Wallet::$error = true;
    phpGridcoin\Wallet::$errorMsg = "Failed to execute RPC method: '" . $call . "' - Wrong number of arguments.";
} catch (\TypeError $e) {
    phpGridcoin\Wallet::$error = true;
    phpGridcoin\Wallet::$errorMsg = "Failed to execute RPC method: '" . $call . "' - Wrong argument type.";
} catch(Exception $e) {
    phpGridcoin\Wallet::$error = true;
    phpGridcoin\Wallet::$errorMsg = "Failed to execute RPC method: " . $call;
}

$return = array(
    "result" => $result,
    "error" => phpGridcoin\Wallet::$error,
    "error_msg" => phpGridcoin\Wallet::$errorMsg
);

echo json_encode($return);

?>