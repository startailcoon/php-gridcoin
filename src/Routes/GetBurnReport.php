<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Models\Wallet\BurnReport;
use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\WalletCache;
use JsonMapper;

class GetBurnReport {

    public static $error_code = 0;
    public static $error_message = "";
    public static $ttl = 3600;  // 1 hour
    public static $timeout = 180; // 3 minutes

    /**
     * Get the burn report.
     * NOTE: This function has a longer runtime the default
     * @var int $ttl Chace time to live in seconds
     * @return null|BurnReport
     */
    public static function execute($ttl = null) {
        $ttl = $ttl ?? self::$ttl;
        
        $cache = new WalletCache;

        if($cache->exists("burnreport")) {
            return json_decode($cache->get("burnreport"));
        } 

        Wallet::setTimeOut(self::$timeout); // This needs to have a higher timeout than the default

        $result = Wallet::execute("getburnreport");

        if($result == null) {
            self::$error_code = Wallet::getErrorCode();
            self::$error_message = Wallet::getErrorMessage();
            return null;
        }

        $return = (new JsonMapper)->map($result, new BurnReport);

        // Store the result in cache
        $cache->set("burnreport", json_encode($return), $ttl);

        return $return;
    }
}