<?php

if (is_dir(__DIR__ . '/../src/')) {
    set_include_path(
        __DIR__ . '/../src/'
        . PATH_SEPARATOR . get_include_path()
    );
}
require_once 'GridcoinWallet/GridcoinWallet.php';
require_once 'GridcoinWallet/Exception.php';

?>