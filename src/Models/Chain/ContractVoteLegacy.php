<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

#TODO: Rename this to ContractVoteV1

class ContractVoteLegacy {
    var string $key;
    var string $mining_id;
    var int $amount;
    var int $magnitude;
    var string $responses;
    var string $poll_txid;
}