<?php

/**
 * Beacon Contract
 * ---
 * This contract has two versions   
 * Version 2 is verified by chain, which enables the possibility to 
 * verify a removal on chain by the user. This was previously required 
 * to be done by an authorized centralisd party by the team.
 * 
 * This does not change the way we process this locally for now.
 */
class ContractBeacon {
    var int $version;
    var string $cpid;
    var string $public_key;
}

?>