<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Models\Chain\Transaction;
use CoonDesign\phpGridcoin\Models\Chain\ContractBody;

use JsonMapper;

class GetTransation {
        /**
     * Get a transaction by txid
     * @param string $txid The transaction id
     * @return null|Transaction The Transaction model
     */
    public static function execute(string $txid) {
        $jm = new JsonMapper();
        $jm->classMap[ContractBody::class] = function($class, $jvalue, $pjson) {
            return ContractBody::determineClass($class, $jvalue, $pjson);
        };

        $result = Wallet::execute(
            "gettransaction", 
            array($txid)
        );

        return empty($result) ? null : $jm->map($result, new Transaction());
    }
}