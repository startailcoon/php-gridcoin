<?php

namespace CoonDesign\phpGridcoin;

use Curl;

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
        $curl->setOpt(CURLOPT_TIMEOUT, Wallet::$timeout);
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