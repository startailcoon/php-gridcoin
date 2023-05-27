<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

class GetBestBlockHash {

    /**
     * Get the best block hash
     * @return string The best block hash
     */
    public static function execute() {
        return Wallet::execute("getbestblockhash");
    }

}

?>