<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

require_once __DIR__ . "/Contract.php";

use CoonDesign\phpGridcoin\Coin;

class Transaction {

    var string $txid;
    var int $version;
    var int $size;
    var int $time;
    var int $locktime;
    var string $hashboinc;
    /** @var Contract[] */
    var array $contracts;
    /** @var Vin[] */
    var array $vin;
    /** @var Vout[] */
    var array $vout;    
    var string $blockhash;
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

        if(isset($this->contracts[0]->body->mining_id)) {

            if($this->contracts[0]->body->mining_id == "INVESTOR") {
                return TransactionType::MINT_POS;
            }

            return TransactionType::MINT_POR;
        }

        if(isset($this->vout[0]->scriptPubKey->type) && $this->vout[0]->scriptPubKey->type == "nonstandard") {
            return TransactionType::MINT_GEN;
        }

        if(isset($this->contracts[0]->type) && !empty($this->contracts[0]->type)) {
            return TransactionType::checkConstat(TransactionType::CONTRACT . "_" . strtoupper($this->contracts[0]->type));
        }

        if(isset($this->vout[0]->scriptPubKey->asm)) {

            if($this->vout[0]->scriptPubKey->asm == "OP_RETURN") {
                return TransactionType::BURN;
            }

            if(stristr($this->vout[0]->scriptPubKey->asm, "OP_HASH160") 
                && stristr($this->vout[0]->scriptPubKey->asm, "OP_EQUAL")) 
            {
                return TransactionType::PAY2SCRIPT;
            }

        }

        return TransactionType::TRANSFER;
    }

    /**
     * Get the addresses of outputs for the transaction
     * 
     * @return array<string>
     */
    function getOutputAddresses() {
        $addresses = array();

        foreach($this->vout as $vout) {
            if(isset($vout->scriptPubKey->addresses)) {
                $addresses[$vout->scriptPubKey->addresses[0]] = $vout->scriptPubKey->addresses[0];
            }
        }

        return $addresses;
    }

    /**
     * Get the value of outputs for the transaction
     * 
     * @return Coin
     */
    function getOutputValue():Coin {
        $coin = new Coin();

        foreach($this->vout as $vout) {
            $coin->add($vout->value);
        }

        return $coin;
    }

    /**
     * Get the value of inputs for the transaction
     * This function requires the inputs to be passed as an array of Transaction objects
     * 
     * @var array<Transaction> $inputs
     * @return Coin
     */
    function getInputValue(array $inputs):Coin {
        $coin = new Coin();

        foreach($this->vin as $vin) {
            $array_key = array_search($vin->txid, array_column($inputs, 'txid'));

            if($array_key === false) {
                throw new \Exception("Input transaction '{$vin->txid}' not found!");
            }

            $coin->add($inputs[$array_key]->vout[$vin->vout]->value);
        }

        return $coin;
    }

    /**
     * Get the addresses of inputs for the transaction
     * This function requires the inputs to be passed as an array of Transaction objects
     * 
     * @var array<Transaction> $inputs
     * @return array<string>
     */
    function getInputAddresses(array $inputs):array {
        $addresses = array();

        foreach($this->vin as $vin) {

            $array_key = array_search($vin->txid, array_column($inputs, 'txid'));

            if($array_key === false) {
                throw new \Exception("Input transaction '{$vin->txid}' not found!");
            }

            $addresses[$inputs[$array_key]->vout[$vin->vout]->scriptPubKey->addresses[0]] = $inputs[$array_key]->vout[$vin->vout]->scriptPubKey->addresses[0];
        }

        return $addresses;
    }
}