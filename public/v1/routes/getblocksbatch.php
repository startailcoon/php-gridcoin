<?php

use CoonDesign\phpGridcoin\Routes\GetBlocksBatch;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

$app->get('/getblocksbatch/{startBlock:[0-9]+}/{blocksToFetch:[0-9]+}[/{txinfo}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    // Limit the number of blocks to fetch to 100
    if($args['blocksToFetch'] > 100) {
        throw new HttpNotFoundException($request, 'The number of blocks to fetch cannot exceed 100');
    }
    
    $startBlock = $args['startBlock'];
    $blocksToFetch = $args['blocksToFetch'];
    $txinfo = isset($args['txinfo']) && $args['txinfo'] == "true" ? true : false;

    $result = GetBlocksBatch::execute($startBlock, $blocksToFetch, $txinfo);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A block with that number was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/getblocksbatch/{blockHash:[a-z0-9]+}/{blocksToFetch:[0-9]+}[/{txinfo:[0-1]}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $blockHash = $args['blockHash'];
    $blocksToFetch = $args['blocksToFetch'];
    $txinfo = isset($args['txinfo']) ? $args['txinfo'] : false;

    $result = GetBlocksBatch::execute($blockHash, $blocksToFetch, $txinfo);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A block with that hash was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

?>