<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

#TODO: Rename this to ContractVoteV2

class ContractVote {
    var int $version;
    var string $poll_txid;
    /** Array of vote IDs */
    var array $responses;
}