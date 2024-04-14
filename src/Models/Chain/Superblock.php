<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

class Superblock {
    var int $version;
    var array $magnitudes;
    /** @var SuperblockProject[] */
    var array $projects;
    var array $beacons;

}