<?php

use CoonDesign\phpGridcoin\Routes\GetRawMempool;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getrawmempool', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $rawmempool = GetRawMempool::execute();
    $response->getBody()->write(json_encode($rawmempool));
    return $response->withHeader('Content-Type', 'application/json');
});

?>