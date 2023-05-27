<?php

namespace CoonDesign\phpGridcoin\Models;

class BlockchainInfo {
    var int $blocks;
    var bool $in_sync;
    var float $moneysupply;
    var BlockChainInfoDifficulty $difficulty;
    var bool $testnet;
    var string $errors;
}

class BlockChainInfoDifficulty {
    var float $current;
    var float $target;
}

?>