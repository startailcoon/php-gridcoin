<?php

use CoonDesign\phpGridcoin\Routes\GetBlock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

$app->get('/getblock/{hash:[a-z0-9]+}[/{txinfo}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $hash = $args['hash'];
    $txinfo = isset($args['txinfo']) && $args['txinfo'] == "true" ? true : false;

    $result = GetBlock::execute($hash, $txinfo);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A block with that hash was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

?>