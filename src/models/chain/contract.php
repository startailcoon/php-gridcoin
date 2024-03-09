<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

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
        if($pjson->version <= 3) {
                
            if($pjson->type == "")              return;
            if(is_string($json))                return 'string';

            switch(strtoupper($pjson->type)) {
                case "SCRAPER":
                    if(!isset($json->version))  return 'CoonDesign\phpGridcoin\Models\Chain\ContractScraper';
                    break;

                case "PROTOCOL":
                    if(!isset($json->version))  return 'CoonDesign\phpGridcoin\Models\Chain\ContractProtocol';
                    break;

                case "CLAIM":
                    if($json->version <= 4)     return 'CoonDesign\phpGridcoin\Models\Chain\ContractClaim';
                    break;

                case "MRC":
                    if($json->version <= 1)       return 'CoonDesign\phpGridcoin\Models\Chain\ContractMRC';
                    break;

                case "PROJECT":
                    if($json->version <= 2)       return 'CoonDesign\phpGridcoin\Models\Chain\ContractProject';
                    break;

                case "BEACON":
                    if($json->version <= 2)       return 'CoonDesign\phpGridcoin\Models\Chain\ContractBeacon';
                    break;

                case "POLL":
                    if($json->version <= 3)       return 'CoonDesign\phpGridcoin\Models\Chain\ContractPoll';
                    break;

                case "VOTE":
                    if(!isset($json->version))    return 'CoonDesign\phpGridcoin\Models\Chain\ContractVoteLegacy';
                    if($json->version == 1)       return 'CoonDesign\phpGridcoin\Models\Chain\ContractVote';
                    break;
            }

        }
        
        // Catch all for unknown contracts
        print_r($pjson);
        print_r($json);
        throw new \Exception("Missing Contracts Class '{$pjson->type}' v{$json->version}");
    }

}

?>