<?php

namespace CoonDesign\phpGridcoin;

use Exception;

require_once __DIR__ . "/Coin.php";
require_once __DIR__ . "/WalletRPC.php";
require_once __DIR__ . "/WalletException.php";
require_once __DIR__ . "/WalletCache.php";

// Load all models
foreach(glob(__DIR__ . "/models/*") as $dir) {
    foreach(glob($dir . "/*.php") as $file) {
        require_once $file;
    }
}

// Load all routes
foreach(glob(__DIR__ . "/routes/*.php") as $file) {
    require_once $file;
}

/**
 * Gridcoin Wallet Class
 * 
 * This is a static class that uses the class GridcoinWalletRPC
 * and formats the data in Objects that can be handled later
 */
class Wallet {

    protected static string $error_message = "";
    protected static int $error_code = 0;
    private static ?WalletRPC $walletRPC = null;
    private static bool $isPrivateNode = false;
    private static string $host;
    private static int $port;
    private static string $user;
    private static string $pass;
    public static int $timeout = 10;
    public static float $timer = 0;
    
    /**
     * Set the RPC to be a public node RPC
     * This will limit the use of some commands that are not allowed on public nodes
     */
    public static function setIsPrivateNode() {
        Wallet::$isPrivateNode = true;
    }

    public static function setTimeout(int $timeout) {
        Wallet::$timeout = $timeout;
    }

    public static function getErrorCode() {
        return Wallet::$error_code;
    }

    public static function getErrorMessage() {
        return Wallet::$error_message;
    }
    
    public static function getTimer() {
        return Wallet::$timer;
    }

    public static function resetTimer() {
        Wallet::$timer = 0;
    }

    public static function setNode(string $host, int $port, string $user, string $pass) {
        Wallet::$host = $host;
        Wallet::$port = $port;
        Wallet::$user = $user;
        Wallet::$pass = $pass;
    }

    // Wallet RPC Methods below
    // --------------------------------------------------------------------------------------------
    // These methods are the RPC methods that are used to communicate with the wallet
    // They all coresponds to a method in the wallet RPC
    

    // Wallet RPC Functions below
    // --------------------------------------------------------------------------------------------
    // These functions are not part of the Gridcoin Wallet RPC
    // They are helper functions to handle the data from the RPC

    /**
     * Catch all for unknown methods
     */
    // public static function __callStatic($name, $args) {
    //     printf("Unknown method %s with args: %s\n", $name, json_encode($args));
    //     Wallet::$error_code = 404;
    //     Wallet::$error_message = "Invalid method '" . $name . "' with args: " . json_encode($args);
    // }

    public static function execute(string $method, array $parameter = array()) {
        $timer = microtime(true);

        // printf("Executing method %s with args: %s\n", $method, json_encode($parameter));
        $result = Wallet::getRPC()->execute($method, $parameter);

        $timer = microtime(true) - $timer;
        Wallet::$timer += $timer;

        if($result) { return $result; }

        Wallet::$error_code = Wallet::getRPC()->error_code;
        Wallet::$error_message = Wallet::getRPC()->error_message;
        return;
    }

    private static function getRPC() {
        // We already have a connection, return it
        if(Wallet::$walletRPC !== null && Wallet::$walletRPC->error_code === 0) {
            return Wallet::$walletRPC;
        }

        Wallet::$walletRPC = new WalletRPC(Wallet::$host, Wallet::$port, Wallet::$user, Wallet::$pass);

        try {
            Wallet::$walletRPC->execute("getblockcount");
            return Wallet::$walletRPC;
        } catch(Exception $e) {
            Wallet::$error_code = 503;
            Wallet::$error_message = "Failed to connect to Gridcoin Wallet RPC";
            return;
        }
    }
}