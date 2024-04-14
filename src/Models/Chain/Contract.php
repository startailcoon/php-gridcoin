<?php

namespace CoonDesign\phpGridcoin\Models\Chain;

foreach(glob(__DIR__ . "/Contract*.php") as $file) {
    require_once $file;
}

class Contract { 
    var int $version;
    var string $type;
    var string $action;
    /** @var ContractBody */
    var $body;
}