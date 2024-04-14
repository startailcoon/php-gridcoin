<?php

namespace CoonDesign\phpGridcoin;

use Exception;

## TODO: Make use of a proper exception class
## This is not used yet, but will be in the future

class WalletException extends Exception {
    public function __construct($message='') {
        parent::__construct($message);
    }
}