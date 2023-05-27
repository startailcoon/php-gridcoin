<?php

declare(strict_types=1);

namespace CoonDesign\phpGridcoin\Exception;

class HttpRateLimitException extends \Slim\Exception\HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = 429;

    /**
     * @var string
     */
    protected $message = 'Rate limit exceeded.';

    protected string $title = '429 Too Many Requests';
    protected string $description = 'The server will not process the request due to a rate limit.';
}
