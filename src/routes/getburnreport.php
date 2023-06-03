<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Models\BurnReport;
use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\WalletCache;
use JsonMapper;
use Slim\Exception\HttpGoneException;
use Slim\Exception\HttpInternalServerErrorException;

class GetBurnReport {

    public static $error_code = 0;
    public static $error_message = "";
    public static $ttl = 3600;  // 1 hour

    /**
     * Get the burn report.
     * This is an expensive operation and should be used with caution.
     * Data is cached and will be updated every hour.
     * Pass a custom TTL value to override the default.
     * @var int $ttl Time to live in seconds
     * @return BurnReport|null
     */
    public static function execute($ttl = null) {
        $ttl = $ttl ?? self::$ttl;
        
        $cache = new WalletCache;

        if($cache->exists("burnreport")) {
            return json_decode($cache->get("burnreport"));
        } 

        Wallet::setTimeOut(180); // This needs to have a higher timeout than the default

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

?>