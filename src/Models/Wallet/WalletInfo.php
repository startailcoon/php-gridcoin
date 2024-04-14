<?php

namespace CoonDesign\phpGridcoin\Models\Wallet;

class WalletInfo {

    var int $walletversion;
    var float $balance;
    var float $newmint;
    var float $stake;
    var int $keypoololdest;
    var int $keypoolsize;
    var string $masterkeyid;
    var bool $staking;
    var string $miningError;
}