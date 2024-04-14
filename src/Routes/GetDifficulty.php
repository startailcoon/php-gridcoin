<?php

namespace CoonDesign\phpGridcoin\Routes;

use CoonDesign\phpGridcoin\Models\Wallet\Difficulty;
use CoonDesign\phpGridcoin\Wallet;

use JsonMapper;

class GetDifficulty {
    
        /**
        * Get the difficulty
        * @return null|float The difficulty
        */
        public static function execute() {
            $result = Wallet::execute("getdifficulty");

            return empty($result) ? null : 
            (new JsonMapper)->map(
                $result, 
                new Difficulty
            );
        }
}