<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Models\Wallet\Vote;

use JsonMapper;

class GetVoteDetails {

    public static $error_code = 0;
    public static $error_message = "";
    public static $ttl = 3600;  // 1 hour
    public static $timeout = 180; // 3 minutes

    /**
     * Get the details of a vote
     * NOTE: This function has a longer runtime the default
     * @var string $txid The transaction ID of the vote
     * @var int $ttl Cache time to live in seconds
     * @return array<Vote>|null
     */
    public static function execute($txid, $ttl = null) {
        $result = Wallet::execute("votedetails", [$txid]);

        return empty($result) ? null :
            (new JsonMapper)->mapArray(
                $result,
                array(),
                'CoonDesign\phpGridcoin\Models\Wallet\Vote'
            );
    }
}