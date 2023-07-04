<?php

use CoonDesign\phpGridcoin\Routes\GetDifficulty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getdifficulty', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $result = GetDifficulty::execute();
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');;
});

?>