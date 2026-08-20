<?php

namespace Hyperodactyl\Exceptions\Service\Database;

use Hyperodactyl\Exceptions\HyperodactylException;

class DatabaseClientFeatureNotEnabledException extends HyperodactylException
{
    public function __construct()
    {
        parent::__construct('Client database creation is not enabled in this Panel.');
    }
}
