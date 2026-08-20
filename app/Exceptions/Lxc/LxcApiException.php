<?php

namespace Hyperodactyl\Exceptions\Lxc;

use Throwable;
use Hyperodactyl\Exceptions\DisplayException;

/**
 * Thrown whenever a call to a remote LXD/Proxmox API fails or returns an
 * error response. This is always the result of a real HTTP call — it must
 * never be thrown to fake a state, only to surface genuine failures.
 */
class LxcApiException extends DisplayException
{
    public function __construct(string $message, private int $statusCode = 502, ?Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
