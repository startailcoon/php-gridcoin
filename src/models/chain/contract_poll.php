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

class ContractPollChoices {
    var int $id;
    var string $label;
}


class PollWeightType {
    const UNKNOWN = 'UNKNOWN';
    const OUT_OF_BOUND = 'OUT_OF_BOUND';
    const MAGNITUDE = 'MAGNITUDE';
    const BALANCE = 'BALANCE';
    const BALANCE_AND_MAGNITUDE = 'BALANCE_AND_MAGNITUDE';
    const CPID_COUNT = 'CPID_COUNT';
    const PARTICIPANT_COUNT = 'PARTICIPANT_COUNT';

    function lls($type) {
        switch($type) {
            case PollWeightType::UNKNOWN:               return _("Unknown");
            case PollWeightType::OUT_OF_BOUND:          return _("Out of bound");
            case PollWeightType::MAGNITUDE:             return _("Magnitude");
            case PollWeightType::BALANCE:               return _("Balance");
            case PollWeightType::BALANCE_AND_MAGNITUDE: return _("Magnitude+Balance");
            case PollWeightType::CPID_COUNT:            return _("CPID Count");
            case PollWeightType::PARTICIPANT_COUNT:     return _("Participant Count");
        }
    }
}

?>