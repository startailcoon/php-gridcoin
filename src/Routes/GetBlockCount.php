<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

class GetBlockCount {

    /**
     * Get the current block count
     * @return int The current block count
     */
    public static function execute() {
        return Wallet::execute("getblockcount");
    }
}