<?php

require_once __DIR__ . "/models/block.php";
require_once __DIR__ . "/models/transaction.php";
require_once __DIR__ . "/models/contract.php";

/**
 * Gridcoin Wallet Class
 * 
 * This is a static class that uses the class GridcoinWalletRPC
 * and formats the data in Objects that can be handled later
 */
class GridcoinWallet {

    public static string $host;
    public static string $port;
    public static string $user;
    public static string $pass;
    protected static ?GridcoinWalletRPC $walletRPC = null;
    protected static JsonMapper $mapper;

    public static function getRPC() {
        $walletRPC = GridcoinWallet::$walletRPC;
        
        if ($walletRPC === null) {
            $walletRPC = GridcoinWallet::$walletRPC = new GridcoinWalletRPC();
        }
    
        return $walletRPC;
    }
    
    /**
     * Get the current block count
     * @return int The current block count
     */
    public static function getblockcount():int {
        return GridcoinWallet::getRPC()->execute("getblockcount");
    }

    /**
     * Get the Block model by block number
     * @param int $block_number The block number to get
     * @return Block The Block model
     */
    public static function getblockbynumber(int $block_number):Block {
        return (new JsonMapper())->map(
            GridcoinWallet::getRPC()->execute(
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
     * @return array An array of Block models
     */
    public static function getblocksbatch($startBlockNoOrHash, int $blocksToFetch, bool $txInfo = false) {
        $jm = new JsonMapper();
        $jm->classMap[Transaction::class] = function($class, $jvalue, $pjson) {
            return Block::determineTxClass($class, $jvalue, $pjson);
        };

        return $jm->mapArray(
            GridcoinWallet::getRPC()->execute(
                "getblocksbatch", 
                array($startBlockNoOrHash, $blocksToFetch, $txInfo)
            )->blocks, 
            array(), 
            'Block'
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
            GridcoinWallet::getRPC()->execute(
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
        $mempool = (array)GridcoinWallet::getRPC()->execute("getrawmempool");
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
            GridcoinWallet::getRPC()->execute("getvotingclaim", array($poll_or_vote_id)),
            new ContractVoteClaim()
        );
    }

    // public static function __callStatic($name, $args) {
    //     $fn = array(GridcoinWallet::getRPC(), $name);
    //     if (! is_callable($fn)) {
    //         throw new GridcoinWalletException("GridcoiNWallet does not have a method called $name");
    //     }

    //     return call_user_func_array($fn, $args);
    // }
}

/**
 * Gridcoin Wallet RPC Class
 * 
 * This class handles the communication with the Gridcoin Wallet
 * and returns the data as JSON
 */
class GridcoinWalletRPC {

    public string $host;
    public string $port;
    public string $user;
    public string $pass;
    private float $timer = 0;
    public function __construct($host = null, $port = null, $user = null, $pass = null) {
        if ($host === null) { $host = GridcoinWallet::$host; }
        if ($port === null) { $port = GridcoinWallet::$port; }
        if ($user === null) { $user = GridcoinWallet::$user; }
        if ($pass === null) { $pass = GridcoinWallet::$pass; }

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
    private function get_result($method, $parameter, $runs = 0) {
        $curl = new Curl\Curl;
        $curl->setOpt(CURLOPT_TIMEOUT, 30);
        if($this->user && $this->pass) {
            $curl->setBasicAuthentication($this->user, $this->pass);
        }
        $payload = $this->createPayload($method, $parameter);
        $curl->post($this->host . ":" . $this->port, $payload);

        if($curl->error) {
            
            printf("Curl run %s: Error %s - %s", $runs, $curl->error_code, $curl->error_message);
            throw new Exception("To many runs ($runs)");
            if($runs > 1) {
                throw new Exception("To many runs ($runs)");
            }
            sleep(5);
            $runs++;
            $this->get_result(...);
        }

        $curl->close();

        if($curl->error_code > 0) {
            printf("[" . date("Y-m-d H:i:s") . "] Curl threw error %s: %s\n", $curl->error_code, $curl->error_message);
            sleep(10);
            $this->get_result($method, $parameter);
        }

        // In Mainnet Tx 22322ad894648edacd3870793dd0522abbbc1af7abd60fd95bf10c7ca3b60e03
        // we have a character in the hashboinc content <BINARY> which will cause an
        // issue when trying to decode it to an array. 
        // We will here sanitize all <BINARY>*</BINARY> data from content
        // We also make sure it's all UTF-8 characters compliant by doing a mb_convert_encoding

        $response = mb_convert_encoding($curl->response, 'UTF-8', 'UTF-8');
        $response = preg_replace("/<BINARY>(.*?)<\/BINARY>/m","",$response);

        $response = json_decode($response);

        if(json_last_error()) {
            printf("[" . date("Y-m-d H:i:s") . "] JSON threw error %s: %s", json_last_error(), json_last_error_msg());
            sleep(10);
            $response = $this->execute($method, $parameter);
        }

        if($response->error) {
            printf("[" . date("Y-m-d H:i:s") . "] Wallet threw error %s: %s\n", $response->error, $response->errmsg);
            sleep(10);
            $this->get_result($method, $parameter);
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

class GridcoinWalletException extends Exception {
    public function __construct($message='') {
        parent::__construct($message);
    }
}

?>