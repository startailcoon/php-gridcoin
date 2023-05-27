<?php

namespace CoonDesign\phpGridcoin\Models;

/**
 * Claim Contracts
 * ------
 * This contract has 3 versions
 * https://github.com/gridcoin-community/Gridcoin-Research/blob/cc44a681b7629879f1f0252b7ca5e4a65ab902a9/src/gridcoin/claim.cpp#L44
 * 
 * None affects the way we currently handle this contract locally
 */

class ContractClaim {
    var int $version;
    var string $mining_id;
    var string $client_version;
    var string $organization;
    var float $block_subsidy;
    var float $research_subsidy;
    var int $magnitude;
    var float $magnitude_unit;
    var string $signature;
    var string $quorum_hash;
    var string $quorum_address;
    var float $fees_to_staker;
    var string $m_mrc_tx_map_size;
    var ?array $mrcs;
}
?>