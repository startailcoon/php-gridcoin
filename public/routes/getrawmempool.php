<?php

use phpGridcoin\Wallet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getrawmempool', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $wallet = new Wallet();
    $rawmempool = $wallet->getRawMemPool();
    $response->getBody()->write(json_encode($rawmempool));
    return $response->withHeader('Content-Type', 'application/json');
});

?>