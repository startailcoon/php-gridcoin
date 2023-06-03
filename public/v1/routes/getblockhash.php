<?php

use CoonDesign\phpGridcoin\Routes\GetBlockHash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

$app->get('/getblockhash/{index:[0-9]+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $index = $args['index'];
    $result = GetBlockHash::execute($index);

    if ($result === null) {
        throw new HttpNotFoundException($request, "Block not found");
    }

    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');;
});


?>