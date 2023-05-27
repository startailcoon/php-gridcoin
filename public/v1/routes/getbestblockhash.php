<?php

use CoonDesign\phpGridcoin\Routes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getbestblockhash', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $result = Routes\GetBestBlockHash::execute();
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');;
});


?>