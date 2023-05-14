<?php

namespace phpGridcoin\Models;

require_once __DIR__ . "/contract.php";

class Transaction {

    /** @var string */
    var $txid;

    /** @var int */
    var $version;

    /** @var int */
    var $size;

    /** @var int */
    var $time;
    
    /** @var int */
    var $locktime;
    
    /** @var string */
    var $hashboinc;
    /** @var Contracts[] */
    var array $contracts;
    
    /** @var Vin[] */
    var array $vin;
    
    /** @var Vout[] */
    var array $vout;
    
    /** @var string */
    var string $blockhash;

    /** @var int */
    var int $confirmations;

    public function getStakingAddress() {
        foreach($this->vout as $vout) {
            if(isset($vout->scriptPubKey->addresses)) {
                return $vout->scriptPubKey->addresses[0];
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getType() {

        if(isset($this->vin[0]->coinbase) && $this->vout[0]->value > 0) {
            return TransactionType::MINT_POW;
        }

        if(isset($this->vout[0]->scriptPubKey->type) && $this->vout[0]->scriptPubKey->type == "nonstandard") {
            return TransactionType::MINT_GEN;
        }

        if(isset($this->contracts[0]->type) && !empty($this->contracts[0]->type)) {
            return TransactionType::checkConstat(TransactionType::CONTRACT . "_" . strtoupper($this->contracts[0]->type));
        }

        return TransactionType::TRANSFER;
    }

    function getOutputAddresses() {
        $addresses = array();

        foreach($this->vout as $vout) {
            if(isset($vout->scriptPubKey->addresses)) {
                $addresses[$vout->scriptPubKey->addresses[0]] = $vout->scriptPubKey->addresses[0];
            }
        }

        return $addresses;
    }

    function getOutputValue() {
        $value = 0;

        foreach($this->vout as $vout) {
            $value += $vout->value;
        }

        return $value;
    }
}

class Vin {

    /** @var int */
    var $coinbase;
    
    /** @var int */
    var $sequence;

    /** @var string */
    var $txid;

    /** @var int */
    var $vout;
    
    /** @var ScriptSig */
    var ScriptSig $scriptSig;
}

class ScriptSig {

    /** @var string */
    var $asm;
    
    /** @var string */
    var string $hex;
}

class Vout {

    /** @var float */
    var $value;

    /** @var int */
    var $n;
    
    /** @var ScriptPubKey */
    var ScriptPubKey $scriptPubKey;
}

class ScriptPubKey {

    /** @var string */
    var $asm;

    /** @var string */
    var $hex;

    /** @var int */
    var $reqSigs;

    /** @var string */
    var $type;

    /** @var array */
    var $addresses;
}


class TransactionType {
    const UNKNOWN = 'UNKNOWN';                  //!< An invalid, non-standard, or empty transaction type
    const COINBASE = 'COINBASE';                //!< 
    const MINT_ACS = 'MINT_ACS';                //!< Minted as a Anyone-can-spend UTXO
    const MINT_GEN = 'MINT_GEN';                //!< 
    const MINT_POS = 'MINT_POS';                //!< Minting was performed by a Proof-of-Stake transaction 
    const MINT_POW = 'MINT_POW';            
    const MINT_POR = 'MINT_POR';                //!< Minting was performed by a Proof-of-Distributed-Research transaction
    const MINT_SIDESTAKE = 'MINT_SIDESTAKE';    //!< Minting was performed and sent as a side-staking to reciever
    const TRANSFER = 'TRANSFER';                //!< Transaction was performed as a transfer
    const BURN = 'BURN';                        //!< Transaction was performed as a burn
    const BURN_MINT = 'BURN_MINT';
    const PAY2SCRIPT = 'PAY2SCRIPT';            //!< Transaction was performed as a pay2script
    const CONTRACT = 'CONTRACT';                //!< Transaction was performed as a contract
    const CONTRACT_MRC = 'CONTRACT_MRC';
    const CONTRACT_POLL = 'CONTRACT_POLL';
    const CONTRACT_VOTE = 'CONTRACT_VOTE';
    const CONTRACT_BEACON = 'CONTRACT_BEACON';
    const CONTRACT_MESSAGE = 'CONTRACT_MESSAGE';
    const CONTRACT_PROJECT = 'CONTRACT_PROJECT';
    const CONTRACT_SCRAPER = 'CONTRACT_SCRAPER';
    const CONTRACT_PROTOCOL = 'CONTRACT_PROTOCOL';

    static function checkConstat($type) {
        if(self::lls($type) == $type) {
            throw new \Exception("'$type' is not defined!");
        }

        return $type;
    }

    static function lls($type) {
        switch($type) {
            case TransactionType::UNKNOWN: return "UNKNOWN";
            case TransactionType::COINBASE: return "Coinbase";
            case TransactionType::MINT_ACS: return "ACS Transaction";
            case TransactionType::MINT_GEN: return 'Coin Generating';
            case TransactionType::MINT_POW: return 'Proof-of-Work';
            case TransactionType::MINT_POS: return "Proof-of-Stake";
            case TransactionType::MINT_POR: return "Research Reward";
            case TransactionType::MINT_SIDESTAKE: return "Side-stake";
            case TransactionType::TRANSFER: return "Transfer";
            case TransactionType::BURN: return "Proof-of-Burn";
            case TransactionType::BURN_MINT: return "Burnt Minting";
            case TransactionType::PAY2SCRIPT: return "Pay2Script";
            case TransactionType::CONTRACT: return "Contract";
            case TransactionType::CONTRACT_MRC: return "MRC Contract";
            case TransactionType::CONTRACT_POLL: return "Poll Contract";
            case TransactionType::CONTRACT_VOTE: return "Vote Contract";
            case TransactionType::CONTRACT_BEACON: return "Beacon Contract";
            case TransactionType::CONTRACT_MESSAGE: return "Message Contract";
            case TransactionType::CONTRACT_PROJECT: return "Project Contract";
            case TransactionType::CONTRACT_SCRAPER: return "Scraper Contract";
            case TransactionType::CONTRACT_PROTOCOL: return "Protocol Contract";
            default: return "$type";
        }
    }    
}

?>