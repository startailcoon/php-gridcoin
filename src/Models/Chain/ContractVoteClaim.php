<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class ContractVoteClaim {
    var int $version;
    var ContractVoteClaimMagntiude $magnitude_claim;
    /** @var ContractVoteClaimBalance[] $balance_claim */
    var array $balance_claim;
}