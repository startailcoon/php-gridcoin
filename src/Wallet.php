<?php

namespace CoonDesign\phpGridcoin;

use CoonDesign\phpGridcoin\Models\Block;
use CoonDesign\phpGridcoin\Models\Transaction;
use CoonDesign\phpGridcoin\Models\ContractBody;
use CoonDesign\phpGridcoin\Models\ContractVoteClaim;

use Exception;
use JsonMapper;
use Curl;

// Load all models
foreach(glob(__DIR__ . "/models/*.php") as $file) {
    require_once $file;
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
    
    /**
     * Set the RPC to be a public node RPC
     * This will limit the use of some commands that are not allowed on public nodes
     */
    public static function setIsPrivateNode() {
        Wallet::$isPrivateNode = true;
    }

    public static function getErrorCode() {
        return Wallet::$error_code;
    }

    public static function getErrorMessage() {
        return Wallet::$error_message;
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
        $result = Wallet::getRPC()->execute($method, $parameter);

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

/**
 * Gridcoin Wallet RPC Class
 * 
 * This class handles the communication with the Gridcoin Wallet
 * and returns the data as JSON
 */
class WalletRPC {

    public string $host;
    public string $port;
    public string $user;
    public string $pass;
    public array $response;
    public int $error_code = 0;
    public string $error_message = "";

    private float $timer = 0;
    public function __construct($host, $port, $user, $pass) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function reset() {
        $this->timer = 0;
    }

    public function get_timer() { return $this->timer; }

    public function execute(string $method, array $parameter = array()) {
        $timer = microtime(true);
        $response = $this->get_result($method, $parameter);
        $this->timer += microtime(true) - $timer;

        return $response;
    }

    private function get_result($method, $parameter) {
        $curl = new Curl\Curl();
        $curl->setOpt(CURLOPT_TIMEOUT, 30);
        if($this->user && $this->pass) {
            $curl->setBasicAuthentication($this->user, $this->pass);
        }
        $payload = $this->createPayload($method, $parameter);
        $curl->post($this->host . ":" . $this->port, $payload);

        if($curl->error || $curl->error_code > 0) {
            $this->error_code = 501;
            $this->error_message = "cURL Error: " . $curl->error_code . ": " . $curl->error_message;
            return;
        }

        $curl->close();

        /**
         * Sanitize the binary response from the wallet
         * Mainnet TX 22322ad894648edacd3870793dd0522abbbc1af7abd60fd95bf10c7ca3b60e03 will otherwise break here
         */
        $response = mb_convert_encoding($curl->response, 'UTF-8', 'UTF-8');
        $response = preg_replace("/<BINARY>(.*?)<\/BINARY>/m","",$response);

        $response = json_decode($response);

        if(json_last_error()) {
            $this->error_code = 502;
            $this->error_message = "JSON Error: " . json_last_error() . ": " . json_last_error_msg();
            return;
        }

        if($response->error) {
            $this->error_code = 503;
            $this->error_message = "Wallet Error: " . $response->error . ": " . $response->errmsg;
            return;
        }

        return $response->result;
    }

    private function createPayload($method, $params_array) {
        $params_string = null;

        if(is_array($params_array)) {
            foreach($params_array as $param) {
                $param = $this->cleanInputParam($param);
                $params_string = empty($params_string) ? $param : "{$params_string}, {$param}";
            }
        }

        if(!empty($params_array) && !is_array($params_array)) {
            $param = $this->cleanInputParam($params_array);
            $params_string = empty($params_string) ? $param : "{$params_string}, {$param}";
        }

        $params_string = !empty($params_string) ? 
            '"params":[' . $params_string . '],' : "";

        return '{"method":"' . $method . '",' . $params_string . '"jsonrpc":"2.0","id":0}';
    }

    private function cleanInputParam($thisParam) {
        if (empty($thisParam) && !is_bool($thisParam)) return;
        if ($thisParam === "NULL") return "\"\""; 

        if(is_string($thisParam)) {
            $thisParam = str_replace("\",","\",\"",$thisParam);
            if(!stristr($thisParam,"}")) {
                $thisParam = "\"". $thisParam . "\""; 
            } 
        }

        if(is_bool($thisParam)) {
            $thisParam = $thisParam ? "true" : "false";
        }

        return $thisParam;
    }
}

## TODO: Make use of a proper exception class
## This is not used yet, but will be in the future

class WalletException extends Exception {
    public function __construct($message='') {
        parent::__construct($message);
    }
}

?>