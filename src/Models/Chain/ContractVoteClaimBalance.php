<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class ContractVoteClaimBalance {
    var string $public_key;
    var string $signature;
    /** @var ContractVoteClaimBalanceOutpoints[] $outpoints */
    var array $outpoints;
}