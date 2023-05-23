<?php

use phpGridcoin\Wallet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException; 
use Slim\Exception\HttpNotFoundException;

$app->get('/gettransaction/{txid}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $txid = $args['txid'];

    // Verify that the TxID is valid
    if(!preg_match('/^[0-9a-f]{64}$/i', $txid)) {
        throw new HttpBadRequestException($request, 'Bad request. Transaction id is invalid');
    }

    $result = Wallet::getTransaction($txid);

    // Verify that the transaction exists
    if(is_null($result)) {
        throw new HttpNotFoundException($request, 'A transaction with that id was not found');
    } 
    
    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');;
});

?>