<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class ScriptPubKey {

    var string $asm;

    var string $hex;

    var null|int $reqSigs;

    var string $type;

    var null|array $addresses;
}