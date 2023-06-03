<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

class GetBlockHash {
    
        /**
        * Get the block hash by number
        * @param int $number The block number
        * @return string The block hash
        */
        public static function execute(int $number):string|null {
            return Wallet::execute("getblockhash", [$number]);
        }
}


?>