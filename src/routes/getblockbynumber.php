<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;

use CoonDesign\phpGridcoin\Models\Chain\Block;
use CoonDesign\phpGridcoin\Models\Chain\Transaction;

use JsonMapper;

class GetBlockByNumber {

    /**
     * Get a Block by block number
     * @param int $block_number The block number to get
     * @return null|Block The Block model
     */
    public static function execute(int $block_number, bool $txinfo) {
        $jm = new JsonMapper();
        $jm->classMap[Transaction::class] = function($class, $jvalue, $pjson) {
            return Block::determineTxClass($class, $jvalue, $pjson);
        };
        
        $result = Wallet::execute("getblockbynumber", [$block_number, $txinfo]);

        return empty($result) ? null : 
            $jm->map(
                $result, 
                new Block()
            );
    }

}


