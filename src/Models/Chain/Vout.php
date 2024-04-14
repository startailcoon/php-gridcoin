<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class Vout {

    var float $value;

    var int $n;
    
    var ScriptPubKey $scriptPubKey;
}