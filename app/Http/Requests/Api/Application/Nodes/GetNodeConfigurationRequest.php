<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Nodes;

use Hyperodactyl\Services\Acl\Api\AdminAcl;

class GetNodeConfigurationRequest extends GetNodesRequest
{
    protected int $permission = AdminAcl::WRITE;
}
