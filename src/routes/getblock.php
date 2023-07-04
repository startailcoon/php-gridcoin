<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Models\Chain\Block;
use CoonDesign\phpGridcoin\Models\Chain\Transaction;

use JsonMapper;

class GetBlock {

    /**
     * Get a block by hash
     * @param string $hash The block hash
     * @param bool $txinfo Include transaction information
     * @return null|Block The block model
     */
    static function execute(string $hash, bool $txinfo = false) {

        $result = Wallet::execute("getblock", [$hash, $txinfo]);

        if($result == null) {
            return null;
        }

        $jm = new JsonMapper();
        $jm->classMap[Transaction::class] = function($class, $jvalue, $pjson) {
            return Block::determineTxClass($class, $jvalue, $pjson);
        };

        return $jm->map($result, new Block());
    }
}


?>