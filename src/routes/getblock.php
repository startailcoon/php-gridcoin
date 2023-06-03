<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Models\Block;
use CoonDesign\phpGridcoin\Models\Transaction;

use JsonMapper;

class GetBlock {

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