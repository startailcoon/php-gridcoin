<?php

use CoonDesign\phpGridcoin\Routes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

$app->get('/getpollresults/{txid:[a-z0-9]+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $txid = $args['txid'];

    $result = Routes\GetPollResults::execute($txid);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A poll with that txid was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

?>