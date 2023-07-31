<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\WalletInterface;
use CoonDesign\phpGridcoin\Wallet;

use CoonDesign\phpGridcoin\Models\Chain\Block;
use CoonDesign\phpGridcoin\Models\Chain\Transaction;
use CoonDesign\phpGridcoin\Models\Chain\ContractBody;

use JsonMapper;

class GetBlocksBatch {

     /**
     * Get a batch of blocks
     * @param int $startBlockNoOrHash The block number or hash to start from
     * @param int $blocksToFetch The amount of blocks to fetch
     * @param bool $txInfo Whether to include transaction info
     * @return null|Block[] An array of Block models
     */
    public static function execute($startBlockNoOrHash, int $blocksToFetch, bool $txInfo = false) {
        $jm = new JsonMapper();

        // Map the transaction class to the correct type
        // The transaction can be a txid reference or a transaction object
        $jm->classMap[Transaction::class] = function($class, $jvalue, $pjson) {
            return Block::determineTxClass($class, $jvalue, $pjson);
        };

        // Map the contract body class to the correct type
        // There are many different types of contracts, this will map the correct body class
        $jm->classMap[ContractBody::class] = function($class, $jvalue, $pjson) {
            return ContractBody::determineClass($class, $jvalue, $pjson);
        };

        $result = Wallet::execute(
            "getblocksbatch", 
            [$startBlockNoOrHash, $blocksToFetch, $txInfo]
        );
        
        return empty($result) ? null : 
            $jm->mapArray(
                $result->blocks, 
                array(), 
                'CoonDesign\phpGridcoin\Models\Chain\Block'
            );
    }

}


?>