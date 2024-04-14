<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class Vin {

    var null|int $coinbase;
    
    var int $sequence;

    var null|string $txid;

    var null|int $vout;
    
    var ScriptSig $scriptSig;
}