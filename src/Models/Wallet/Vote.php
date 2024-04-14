<?php

namespace CoonDesign\phpGridcoin\Models\Wallet;

class Vote {
    var float $amount;
    var string $cpid;
    var int $magntiude;
    var float $total_weight;
    /** @var VoteAnswers[] */
    var $answers;
}