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

// If no command is given, show the help page.
if(empty($call)) {
    echo <<<EOF

    <style>
        body {
            font-family: sans-serif;
        }
        h1 {
            font-size: 1.5em;
        }
        h2 {
            font-size: 1.2em;
        }
        .box {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
        }
        .box h2 {
            margin: 0 0 10px 0;
        }
        .box p {
            margin: 0;
            padding: 0 0 10px 0;
        }
        .box code {
            background-color: #eee;
            padding: 2px 5px;
        }
    </style>

    <h1>phpGridcoin Wallet RPC API</h1>
    <p>Usage: <code>http://<i>host</i>:<i>port</i>/<i>command</i>/<i>param1</i>/<i>param2</i>/...</code></p>
    <p>Available commands:</p>
    <ul>
        <li><a href="/getblockcount">getblockcount</a></li>
        <li><a href="/getblockbynumber/100">getblockbynumber</a></li>
        <li><a href="/getblocksbatch/100/10/true">getblocksbatch</a></li>
        <li><a href="/gettransaction/a9e5994111d2477c6cd63be7b877eb8ff2f3e21fd600661ee078635c6f38f7d1">gettransaction</a></li>
        <li><a href="/getrawmempool">getrawmempool</a></li>
        <li><a href="/getvotingclaims/1234567890">getvotingclaims</a></li>
    </ul>


    <div class='box'>
        <h2>getblockcount</h2>
        <p>Get the current block count</p>
        <p>Example: <a href='/getblockcount'>/getblockcount</a></p>
    </div>
        
    <div class='box'>
        <h2>getblockbynumber</h2>
        <p>Get a block by block number</p>
        <p>Example: <a href='/getblockbynumber/100'>/getblockbynumber/100</a></p>
        <p>Parameters:</p>
        <ol>
            <li><code>blocknumber</code> - The block number</li>
        </ol>
    </div>

    <div class='box'>
        <h2>getblocksbatch</h2>
        <p>Get multiple blocks by block number</p>
        <p>Example: <a href='/getblocksbatch/100/10/true'>/getblocksbatch/100/10/true</a></p>
        <p>Parameters:</p>
        <ol>
            <li><code>int:startBlockNoOrHash</code> - The block number or hash to start from</li>
            <li><code>int:blocksToFetch</code> - The number of blocks to get</li>
            <li><code>bool:txInfo</code> - Optional: Whether to include transaction info</li>
        </ol>
    </div>

    <div class='box'>
        <h2>gettransaction</h2>
        <p>Get a transaction by transaction id</p>
        <p>Example: <a href='/gettransaction/a9e5994111d2477c6cd63be7b877eb8ff2f3e21fd600661ee078635c6f38f7d1'>/gettransaction/a9e5994111d2477c6cd63be7b877eb8ff2f3e21fd600661ee078635c6f38f7d1</a></p>
        <p>Parameters:</p>
        <ol>
            <li><code>txid</code> - The transaction id</li>
        </ol>
    </div>

    <div class='box'>
        <h2>getrawmempool</h2>
        <p>Get the raw mempool</p>
        <p>Example: <a href='/getrawmempool'>/getrawmempool</a></p>
    </div>

    <div class='box'>
        <h2>getvotingclaims</h2>
        <p>Get the voting claims</p>
        <p>Example: <a href='/getvotingclaims/1234567890'>/getvotingclaims/1234567890</a></p>
        <p>Parameters:</p>
        <ol>
            <li><code>txid</code> - The transaction id</li>
        </ol>
    </div>

EOF;
    exit();
}

$result = null;

## TODO: This can be done better, but it works for now.

try {
    $result = phpGridcoin\Wallet::{$call}(...$props);
} catch (\ArgumentCountError $e) {
    phpGridcoin\Wallet::setErrorMsg("Failed to execute RPC method: '" . $call . "' - Wrong number of arguments.");
} catch (\TypeError $e) {
    phpGridcoin\Wallet::setErrorMsg("Failed to execute RPC method: '" . $call . "' - Wrong argument type.");
} catch(Exception $e) {
    phpGridcoin\Wallet::setErrorMsg("Failed to execute RPC method: " . $call);
}

$return = array(
    "result" => $result,
    "error" => phpGridcoin\Wallet::getIsError(),
    "error_msg" => phpGridcoin\Wallet::getErrorMsg()
);

echo json_encode($return);

?>