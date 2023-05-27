<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\WalletInterface;
use CoonDesign\phpGridcoin\Wallet;

use CoonDesign\phpGridcoin\Models\Block;
use CoonDesign\phpGridcoin\Models\Transaction;

use JsonMapper;

class GetBlocksBatch {

     /**
     * Get a batch of blocks
     * @param int $startBlockNoOrHash The block number or hash to start from
     * @param int $blocksToFetch The amount of blocks to fetch
     * @param bool $txInfo Whether to include transaction info
     * @return Block[] An array of Block models
     */
    public static function execute($startBlockNoOrHash, int $blocksToFetch, bool $txInfo = false) {
        $jm = new JsonMapper();
        $jm->classMap[Transaction::class] = function($class, $jvalue, $pjson) {
            return Block::determineTxClass($class, $jvalue, $pjson);
        };

        $result = Wallet::execute(
            "getblocksbatch", 
            array($startBlockNoOrHash, $blocksToFetch, $txInfo)
        );
        
        return empty($result) ? null : 
            $jm->mapArray(
                $result->blocks, 
                array(), 
                'CoonDesign\phpGridcoin\Models\Block'
            );
    }

}


?>