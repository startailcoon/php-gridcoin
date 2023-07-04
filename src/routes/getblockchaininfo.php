<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

use CoonDesign\phpGridcoin\Models\Wallet\BlockchainInfo;
use JsonMapper;


class GetBlockchainInfo {

    /**
     * Get the blockchain info
     * @return null|BlockchainInfo The BlockchainInfo model
     */
    public static function execute() {
        
        $result = Wallet::execute("getblockchaininfo");
        return empty($result) ? null : 
            (new JsonMapper)->map(
                $result,
                new BlockchainInfo()
            );
    }

}