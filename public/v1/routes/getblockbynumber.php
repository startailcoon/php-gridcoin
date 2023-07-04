<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

use CoonDesign\phpGridcoin\Routes\GetBlockByNumber;

$app->get('/getblockbynumber/{height:[0-9]+}[/{txinfo:[0-1]}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $height = $args['height'];
    $txinfo = isset($args['txinfo']) ? true : false;

    $result = GetBlockByNumber::execute($height, $txinfo);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A block with that number was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

?>