<?php

use CoonDesign\phpGridcoin\Routes\GetWalletInfo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/getwalletinfo', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $result = GetWalletInfo::execute();

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

?>