<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

use CoonDesign\phpGridcoin\Models\Block;
use CoonDesign\phpGridcoin\Models\Transaction;

use JsonMapper;

class GetBlockByNumber {

    /**
     * Get the Block model by block number
     * @param int $block_number The block number to get
     * @return Block The Block model
     */
    public static function execute(int $block_number):Block|null {
        $jm = new JsonMapper();
        $jm->classMap[Transaction::class] = function($class, $jvalue, $pjson) {
            return Block::determineTxClass($class, $jvalue, $pjson);
        };
        
        $result = Wallet::execute(
            "getblockbynumber", 
            array($block_number)
        );

        return empty($result) ? null : 
            $jm->map(
                $result, 
                new Block()
            );
    }

}


