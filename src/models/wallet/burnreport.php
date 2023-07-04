<?php

namespace CoonDesign\phpGridcoin\Models\Wallet;

class BurnReport {
    var float $total;
    var float $voluntary;
    var BurnReportContracts $contracts;
}

class BurnReportContracts {
    var float $beacon;
    var float $message;
    var float $poll;
    var float $project;
    var float $protocol;
    var float $scraper;
    var float $vote;
    var float $mrc;
}


?>