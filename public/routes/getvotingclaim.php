<?php

use phpGridcoin\Wallet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

$app->get('/getvotingclaim/{poll_or_vote_id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $wallet = new Wallet();
    $votingclaim = $wallet->getVotingClaim($args['poll_or_vote_id']);

    if($votingclaim == null) {
        throw new HttpNotFoundException($request, 'Voting claim not found');
    }

    $response->getBody()->write(json_encode($votingclaim));
    return $response->withHeader('Content-Type', 'application/json');
});

?>