<?php

namespace phpGridcoin;

use phpGridcoin\Models\Block;
use phpGridcoin\Models\Transaction;
use phpGridcoin\Models\ContractBody;
use phpGridcoin\Models\ContractVoteClaim;

use Exception;
use JsonMapper;
use Curl;

require_once __DIR__ . "/Constants.php";
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

    private static array $nodes;
    public static bool $allowPublicNodes = true;
    protected static ?WalletRPC $walletRPC = null;
    private static bool $isPublicNodeRPC = false;
    
    /**
     * Set the RPC to be a public node RPC
     * This will limit the use of some commands that are not allowed on public nodes
     */
    public static function setIsPublicNodeRPC() {
        Wallet::$isPublicNodeRPC = true;
    }

    
    public static function setAllowPublicNodes(bool $allowPublicNodes = true) {
        Wallet::$allowPublicNodes = $allowPublicNodes;
    }
    
    public static function addNode(string $host, int $port, string $user = "", string $pass = "") {
        Wallet::$nodes[] = array(
            "host" => $host,
            "port" => $port,
            "user" => $user,
            "pass" => $pass
        );
    }

    public static function removeNode(string $host, string $port, string $user, string $pass) {
        foreach(Wallet::$nodes as $key => $node) {
            if($node["host"] === $host && $node["port"] === $port && $node["user"] === $user && $node["pass"] === $pass) {
                unset(Wallet::$nodes[$key]);
            }
        }
    }

    private static function execute(string $method, array $parameter = array()) {
        try {
            return Wallet::getRPC()->execute($method, $parameter);
        } catch(Exception $e) {
            throw new Exception("Failed to execute RPC method: " . $method);
        }
    }

    private static function getRPC() {
        // We already have a connection, return it
        if(Wallet::$walletRPC !== null && Wallet::$walletRPC->error === false) {
            return Wallet::$walletRPC;
        }

        // Append default nodes to the list if allowed
        if(!empty(Wallet::$nodes) && Wallet::$allowPublicNodes) {
            Wallet::$nodes = array_merge(Wallet::$nodes, Constants::$nodes);
        }

        // If no nodes are set, use the default nodes
        if(empty(Wallet::$nodes)) {
            Wallet::$nodes = Constants::$nodes;
        } 

        while(Wallet::$walletRPC === null) {

            foreach(Wallet::$nodes as $node) {
                Wallet::$walletRPC = new WalletRPC($node["host"], $node["port"], $node["user"], $node["pass"]);

                try {
                    Wallet::$walletRPC->execute("getblockcount");
                    return Wallet::$walletRPC;
                } catch(Exception $e) {
                    continue;
                }
            }
        }

        throw new Exception("Failed to connect to Gridcoin Wallet RPC");
    }
    
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
    public static function gettransaction(string $txid):Transaction {
        $jm = new JsonMapper();
        $jm->classMap[ContractBody::class] = function($class, $jvalue, $pjson) {
            return ContractBody::determineClass($class, $jvalue, $pjson);
        };

        return $jm->map(
            Wallet::execute(
                "gettransaction", 
                array($txid)
            ),
            new Transaction()
        );
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

    public static function __callStatic($name, $args) {
        printf("Usable to find method '%s' with args: %s\n", $name, json_encode($args));
        exit();
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
    public bool $error = false;
    public int $error_code = 0;
    public string $error_message = "";
    public string $error_maker = "";

    private float $timer = 0;
    public function __construct($host = null, $port = null, $user = null, $pass = null) {
        if ($host === null) { $host = Wallet::$host; }
        if ($port === null) { $port = Wallet::$port; }
        if ($user === null) { $user = Wallet::$user; }
        if ($pass === null) { $pass = Wallet::$pass; }

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
        
        return isset($response->result) ? $response->result : null;
    }
    private function get_result($method, $parameter) {
        $curl = new Curl\Curl;
        $curl->setOpt(CURLOPT_TIMEOUT, 30);
        if($this->user && $this->pass) {
            $curl->setBasicAuthentication($this->user, $this->pass);
        }
        $payload = $this->createPayload($method, $parameter);
        $curl->post($this->host . ":" . $this->port, $payload);

        if($curl->error || $curl->error_code > 0) {
            $this->error = true;
            throw new Exception("cURL Error: " . $curl->error_code . ": " . $curl->error_message);
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
            $this->error = true;
            throw new Exception("JSON Error: " . json_last_error() . ": " . json_last_error_msg());
            // $this->error_message = json_last_error_msg();
            // $this->error_code = json_last_error();
            // // printf("[" . date("Y-m-d H:i:s") . "] JSON threw error %s: %s", json_last_error(), json_last_error_msg());
            // sleep(10);
            // $response = $this->execute($method, $parameter);
        }

        if($response->error) {
            $this->error = true;
            throw new Exception("Wallet Error: " . $response->error . ": " . $response->errmsg);
            // $this->error_message = $response->errmsg;
            // $this->error_code = $response->error;
            // // printf("[" . date("Y-m-d H:i:s") . "] Wallet threw error %s: %s\n", $response->error, $response->errmsg);
            // sleep(10);
            // $this->get_result($method, $parameter);
        }

        return $response;
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

class WalletException extends Exception {
    public function __construct($message='') {
        parent::__construct($message);
    }
}

?>