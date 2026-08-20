<?php

namespace Hyperodactyl\Exceptions\Service;

use Illuminate\Http\Response;
use Hyperodactyl\Exceptions\DisplayException;

class HasActiveServersException extends DisplayException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
