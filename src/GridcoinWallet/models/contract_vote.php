<?php

namespace phpGridcoin\Models;

class ContractVoteLegacy {
    var string $key;
    var string $mining_id;
    var int $amount;
    var int $magnitude;
    var string $responses;
    var string $poll_txid;
}

class ContractVote {
    var int $version;
    var string $poll_txid;
    /** Array of vote IDs */
    var array $responses;
}

class ContractVoteClaim {
    var int $version;
    var ContractVoteClaimMagntiude $magnitude_claim;
    /** @var ContractVoteClaimBalance[] $balance_claim */
    var array $balance_claim;
}

class ContractVoteClaimMagntiude {
    var string $mining_id;
    var string $beacon_txid;
    var string $signature;
}

class ContractVoteClaimBalance {
    var string $public_key;
    var string $signature;
    /** @var ContractVoteClaimBalanceOutpoints[] $outpoints */
    var array $outpoints;
}

class ContractVoteClaimBalanceOutpoints {
    var string $txid;
    var int $offset;
}

?>