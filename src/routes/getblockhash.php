<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

class GetBlockHash {
    
        /**
        * Get the block hash by number
        * @param int $number The block number
        * @return null|string The block hash
        */
        public static function execute(int $number) {
            return Wallet::execute("getblockhash", [$number]);
        }
}


?>