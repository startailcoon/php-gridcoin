<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Models\Wallet\WalletInfo;
use CoonDesign\phpGridcoin\Wallet;
use JsonMapper;


class GetWalletInfo {
    
        /**
        * Get the wallet info
        * @return object The wallet info
        */
        public static function execute() {
            $result = Wallet::execute("getwalletinfo");

            return empty($result) ? null :
            (new JsonMapper)->map(
                $result, 
                new WalletInfo
            );
        }
}



?>