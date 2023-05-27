<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Models\Transaction;

use CoonDesign\phpGridcoin\Routes\GetTransation;

class GetRawMempool {
    /**
     * Get the blockchain mempool
     * @return Transaction[] An array of Transaction models
     */
    public static function execute() {
        $map_tx = array();
        $mempool = (array)Wallet::execute("getrawmempool");
        foreach($mempool as $txid) {
            $map_tx[] = GetTransation::execute($txid);
        }

        return $map_tx;
    }
}