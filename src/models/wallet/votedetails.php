<?php

namespace CoonDesign\phpGridcoin\Models\Wallet;

class VoteDetails {
    /** @var Vote[] */
    var Vote $votes;
}

class Vote {
    var float $amount;
    var string $cpid;
    var int $magntiude;
    var float $total_weight;
    /** @var VoteAnswers[] */
    var $answers;
}

class VoteAnswers {
    var int $id;
    var float $weight;
}
?>