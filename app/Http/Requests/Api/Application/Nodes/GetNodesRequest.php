<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Nodes;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class GetNodesRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_NODES;

    protected int $permission = AdminAcl::READ;
}
