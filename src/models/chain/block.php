<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

require_once __DIR__ . "/contract_mrc.php";
require_once __DIR__ . "/transaction.php";

class Block {

    var string $hash;   
    var int $size;
    var int $height;
    var int $version;
    var string $merkleroot;
    var float $mint;
    var int $MoneySupply;
    var int $time;
    var int $nonce;
    var string $bits;
    var float $difficulty;
    var string $blocktrust;
    var string $chaintrust;
    var string $previousblockhash;
    var string $nextblockhash;
    var string $flags;
    var string $proofhash;
    var int $entropybit;
    var string $modifier;
    /** @var Transaction[] */
    var array $tx;
    var BlockClaim $claim;
    var float $fees_collected;
    var Superblock $superblock;
    var bool $IsSuperBlock;
    var bool $IsContract;

    /** 
     * Determines the class of the transaction
     * The wallet can return either an array of TX ids, or the full transaction
     */
    public static function determineTxClass($class, $jvalue, $pjson) {
        return is_string($jvalue) ? 'string' : 'CoonDesign\phpGridcoin\Models\Chain\Transaction';
    }

    /**
     * Returns the CPID of the staker.
     * If there is no CPID, return null
     *
     * @return string|null
     */
    public function getStakerCPID() {
        return strlen($this->claim->mining_id) == 32 ? $this->claim->mining_id : null;
    }

    /**
     * Returns the address of the staker.
     * Requires the transaction model to be loaded
     * 
     * @return string|null
     */
    public function getStakerAddress() {

        foreach($this->tx as $mTx) {

            // This needs to be a Transaction object and not the TXID reference
            if(!is_object($mTx)) {
                new \Exception("Transaction model not loaded");
            }

            foreach($mTx->vout as $vout) {
                if(isset($vout->scriptPubKey->addresses)) {
                    return $vout->scriptPubKey->addresses[0];
                }
            }
        }
        
        return null;
    }

}

class Superblock {
    var int $version;
    var array $magnitudes;
    /** @var SuperblockProject[] */
    var array $projects;
    var array $beacons;

}

class SuperblockProject {
    var int $average_rac;
    var int $rac;
    var int $total_credit;
}

class BlockClaim {
    var int $version;
    var string $mining_id;
    var string $client_version;
    var string $organization;
    var float $block_subsidy;
    var float $research_subsidy;
    var int $magnitude;
    var float $magnitude_unit;
    var float $fees_to_staker;
    var string $m_mrc_tx_map_size;
    /** @var ContractMRC[] */
    var array $mrcs;
    var string $signature;
    var string $quorum_hash;
    var string $quorum_address;

}


?>