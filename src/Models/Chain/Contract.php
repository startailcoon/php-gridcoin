<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

require_once __DIR__ . "/contract_beacon.php";
require_once __DIR__ . "/contract_claim.php";
require_once __DIR__ . "/contract_mrc.php";
require_once __DIR__ . "/contract_project.php";
require_once __DIR__ . "/contract_poll.php";
require_once __DIR__ . "/contract_vote.php";

class Contract { 
    var int $version;
    var string $type;
    var string $action;
    /** @var ContractBody */
    var $body;
}