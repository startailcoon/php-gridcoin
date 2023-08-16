<?php

use CoonDesign\phpGridcoin\Routes\GetBurnReport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

$app->get('/getburnreport', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $result = GetBurnReport::execute();

    if($result === null) {
        throw new HttpInternalServerErrorException($request, 'An error occured while fetching the burn report, please try again later. Error: ' . GetBurnReport::$error_code . ' - ' . GetBurnReport::$error_message);
    }

    $payload = json_encode($result, JSON_PRETTY_PRINT);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


?>