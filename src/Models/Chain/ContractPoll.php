<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class ContractPoll { 
    var int $version;
    var string $title;
    var string $question;
    var string $url;
    var int $type;
    var int $weight_type;
    var int $response_type;
    var int $duration_days;
    /** @var ContractPollChoices[] */
    var array $choices;
}

