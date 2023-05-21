<?php

namespace phpGridcoin;

use phpGridcoin\Models\Block;
use phpGridcoin\Models\Transaction;
use phpGridcoin\Models\ContractBody;
use phpGridcoin\Models\ContractVoteClaim;

use Exception;
use JsonMapper;
use Curl;

require_once __DIR__ . "/models/block.php";
require_once __DIR__ . "/models/transaction.php";
require_once __DIR__ . "/models/contract.php";

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
    
    /**
     * Get the current block count
     * @return int The current block count
     */
    public static function getblockcount():int {
        return Wallet::execute("getblockcount");
    }

    /**
     * Get the Block model by block number
     * @param int $block_number The block number to get
     * @return Block The Block model
     */
    public static function getblockbynumber(int $block_number):Block {
        return (new JsonMapper())->map(
            Wallet::execute(
                "getblockbynumber", 
                array($block_number)
            ), 
            new Block()
        );
    }

    /**
     * Get a batch of blocks
     * @param int $startBlockNoOrHash The block number or hash to start from
     * @param int $blocksToFetch The amount of blocks to fetch
     * @param bool $txInfo Whether to include transaction info
     * @return Block[] An array of Block models
     */
    public static function getblocksbatch($startBlockNoOrHash, int $blocksToFetch, bool $txInfo = false) {
        $jm = new JsonMapper();
        $jm->classMap[Models\Transaction::class] = function($class, $jvalue, $pjson) {
            return Models\Block::determineTxClass($class, $jvalue, $pjson);
        };

        return $jm->mapArray(
            Wallet::execute(
                "getblocksbatch", 
                array($startBlockNoOrHash, $blocksToFetch, $txInfo)
            )->blocks, 
            array(), 
            'phpGridcoin\Models\Block'
        );
    }

    /**
     * Get a transaction by txid
     * @param string $txid The transaction id
     * @return Transaction The Transaction model
     */
    public static function gettransaction(string $txid):Transaction|null {
        $jm = new JsonMapper();
        $jm->classMap[ContractBody::class] = function($class, $jvalue, $pjson) {
            return ContractBody::determineClass($class, $jvalue, $pjson);
        };

        $result = Wallet::execute(
            "gettransaction", 
            array($txid)
        );

        if($result === null) {
            Wallet::$error_code = 404;
            Wallet::$error_message = "No Transaction with the txid of '{$txid}' was found";

            return null;
        }

        return $jm->map($result, new Transaction());
    }

    /**
     * Get the blockchain mempool
     * @return Transaction[] An array of Transaction models
     */
    public static function getrawmempool() {
        $map_tx = array();
        $mempool = (array)Wallet::execute("getrawmempool");
        foreach($mempool as $txid) {
            $map_tx[] = self::gettransaction($txid); 
        }

        return $map_tx;
    }

    /**
     * Get voting claim by Transaction hash of the poll or vote.
     * @param string $poll_or_vote_id Transaction hash of the poll or vote.
     * @return ContractVoteClaim The ContractVoteClaim model
     */
    public static function getvotingclaim(string $poll_or_vote_id) {
        return (new JsonMapper)->map(
            Wallet::execute("getvotingclaim", array($poll_or_vote_id)),
            new ContractVoteClaim()
        );
    }

    // Wallet RPC Functions below
    // --------------------------------------------------------------------------------------------
    // These functions are not part of the Gridcoin Wallet RPC
    // They are helper functions to handle the data from the RPC

    /**
     * Catch all for unknown methods
     */
    public static function __callStatic($name, $args) {
        Wallet::$error_code = 404;
        Wallet::$error_message = "Invalid method '" . $name . "' with args: " . json_encode($args);
    }

    private static function execute(string $method, array $parameter = array()) {
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