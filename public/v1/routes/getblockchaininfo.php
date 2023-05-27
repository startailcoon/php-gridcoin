<?php

use CoonDesign\phpGridcoin\Routes\GetBlockchainInfo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getblockchaininfo', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $result = GetBlockchainInfo::execute();
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');;
});

?>