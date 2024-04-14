<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\WalletCache;
use CoonDesign\phpGridcoin\Models\Wallet\PollResults;
use JsonMapper;

class GetPollResults {

    public static $error_code = 0;
    public static $error_message = "";
    public static $ttl = 3600;  // 1 hour
    public static $timeout = 180; // 3 minutes

    /**
     * Get the results of a poll
     * NOTE: This function has a longer runtime the default
     * @var string $txid The transaction ID of the poll
     * @var int $ttl Cache time to live in seconds
     * @return PollResults|null
     */
    public static function execute($txid, $ttl = null) {
        $ttl = $ttl ?? self::$ttl;
        
        $cache = new WalletCache;

        if($cache->exists("getpollresults_{$txid}")) {
            return json_decode($cache->get("getpollresults_{$txid}"));
        } 

        Wallet::setTimeOut(self::$timeout); // This needs to have a higher timeout than the default

        $result = Wallet::execute("getpollresults", [$txid]);

        if($result == null) {
            self::$error_code = Wallet::getErrorCode();
            self::$error_message = Wallet::getErrorMessage();
            return null;
        }

        $return = (new JsonMapper)->map($result, new PollResults());

        // Store the result in cache
        $cache->set("getpollresults_{$txid}", json_encode($return), $ttl);

        return $return;

    }
}