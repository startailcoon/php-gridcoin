<?php

use phpGridcoin\Wallet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

$app->get('/getblocksbatch/{startBlock:[0-9]+}/{blocksToFetch:[0-9]+}[/{txinfo}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $startBlock = $args['startBlock'];
    $blocksToFetch = $args['blocksToFetch'];
    $txinfo = isset($args['txinfo']) ? $args['txinfo'] : false;

    $result = Wallet::getblocksbatch($startBlock, $blocksToFetch, $txinfo);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A block with that number was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/getblocksbatch/{blockHash:[a-z0-9]+}/{blocksToFetch:[0-9]+}[/{txinfo}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $blockHash = $args['blockHash'];
    $blocksToFetch = $args['blocksToFetch'];
    $txinfo = isset($args['txinfo']) ? $args['txinfo'] : false;

    $result = Wallet::getblocksbatch($blockHash, $blocksToFetch, $txinfo);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A block with that hash was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

?>