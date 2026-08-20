<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Lxc;

use Hyperodactyl\Models\LxcNode;
use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class StoreLxcNodeRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_LXC_NODES;

    protected int $permission = AdminAcl::WRITE;

    public function rules(?array $rules = null): array
    {
        return $rules ?? LxcNode::$validationRules;
    }
}
