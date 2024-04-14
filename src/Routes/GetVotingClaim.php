<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Wallet;
use CoonDesign\phpGridcoin\Models\Chain\ContractVoteClaim;

use JsonMapper;

class GetVotingClaim {

    /**
     * Get voting claim by Transaction hash of the poll or vote.
     * @param string $poll_or_vote_id Transaction hash of the poll or vote.
     * @return ContractVoteClaim The ContractVoteClaim model
     */
    
    public static function execute(string $poll_or_vote_id) {
        $result = Wallet::execute("getvotingclaim", array($poll_or_vote_id));

        return empty($result) ? null :
            (new JsonMapper)->map(
                $result,
                new ContractVoteClaim
            );
    }
}