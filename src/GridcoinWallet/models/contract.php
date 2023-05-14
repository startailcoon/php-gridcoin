<?php

require_once __DIR__ . "/contract_beacon.php";
require_once __DIR__ . "/contract_claim.php";
require_once __DIR__ . "/contract_mrc.php";
require_once __DIR__ . "/contract_project.php";
require_once __DIR__ . "/contract_poll.php";
require_once __DIR__ . "/contract_vote.php";

class Contracts { 
    var int $version;
    var string $type;
    var string $action;
    /** @var ContractBody */
    var $body;

}

/** Placeholder to be replaced with a specific Contract Body Class */
class ContractBody { 

    public static function determineClass($class, $json, $pjson) {
        if($pjson->version <= 2) {
                
            if($pjson->type == "")           return;
            if(is_string($json))                return 'string';

            switch(strtoupper($pjson->type)) {
                case "SCRAPER":
                    if(!isset($json->version))  return 'ContractScraper';
                    break;

                case "PROTOCOL":
                    if(!isset($json->version))  return 'ContractProtocol';
                    break;

                case "CLAIM":
                    if($json->version <= 4)     return 'ContractClaim';
                    break;

                case "MRC":
                    if($json->version <= 1)       return 'ContractMRC';
                    break;

                case "PROJECT":
                    if($json->version <= 2)       return 'ContractProject';
                    break;

                case "BEACON":
                    if($json->version <= 2)       return 'ContractBeacon';
                    break;

                case "POLL":
                    if($json->version <= 3)       return 'ContractPoll';
                    break;

                case "VOTE":
                    if(!isset($json->version))    return 'ContractVoteLegacy';
                    if($json->version == 1)       return 'ContractVote';
                    break;
            }

        }
        
        print_r($pjson);
        print_r($json);
        throw new \Exception("Missing Contracts Class '{$pjson->type}' v{$json->version}");
    }

}

?>