<?php

/**
 * Wallet RPC API
 * 
 * This file is used to make it possible to host a wallet RPC API as a webservice.
 * Wallet RPC commands can be called by this API, exept a few commands that are not allowed.
 *
 * This script can be used as a stand-alone by running the following command:
 * 'php -S <host>:<port> api.php'
 *
 * Running the PHP Server is NOT recommended outside development environment.
 * Best practice is to use a webserver like Apache or Nginx to host this API.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Wallet.php';

// We do not wish some commands to be available through this API.
phpGridcoin\Wallet::setIsPublicNodeRPC();

// Set the RPC connection details.
phpGridcoin\Wallet::addNode('localhost', '25717', 'gridcoinrpc', 'bkw75QgtWAAQpnU0MHR4qIQIfAqXR7OxdvHPHI6xI4VMQKXXEkpfPo2dT');

// Get the command and parameters from the URL.
$props = explode("/", substr($_SERVER['REQUEST_URI'],1));
$call = $props[0];
array_shift($props);

// Call the command and return the result.
echo json_encode(phpGridcoin\Wallet::{$call}(...$props));

?>