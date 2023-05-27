<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

use CoonDesign\phpGridcoin\Models\BlockchainInfo;

use JsonMapper;


class GetBlockchainInfo {

    public static function execute() {
        
        $result = Wallet::execute("getblockchaininfo");
        return empty($result) ? null : 
            (new JsonMapper)->map(
                $result,
                new BlockchainInfo()
            );
    }

}