<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class BlockClaim {
    var int $version;
    var string $mining_id;
    var string $client_version;
    var string $organization;
    var float $block_subsidy;
    var float $research_subsidy;
    var int $magnitude;
    var float $magnitude_unit;
    var float $fees_to_staker;
    var string $m_mrc_tx_map_size;
    /** @var ContractMRC[] */
    var array $mrcs;
    var string $signature;
    var string $quorum_hash;
    var string $quorum_address;
}