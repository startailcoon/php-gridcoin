<?php

namespace CoonDesign\phpGridcoin\Models\Wallet;

class PollResults {

    var string $poll_id;
    var string $poll_title;
    var bool $poll_expired;
    var int $starting_block_height;
    var int $ending_block_height;
    var int $votes;
    var int $invalid_votes;
    var float $total_weight;
    var float $active_vote_weight;
    var float $vote_percent_avw;
    var bool $poll_results_validated;
    var int $top_choice_id;
    var string $top_choice;
    var $responses;
}