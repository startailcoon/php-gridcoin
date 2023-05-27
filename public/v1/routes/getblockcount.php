<?php

use CoonDesign\phpGridcoin\Routes\GetBlockCount;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getblockcount', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $result = GetBlockCount::execute();
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');;
}); 

?>