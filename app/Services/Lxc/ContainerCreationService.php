<?php

namespace Hyperodactyl\Services\Lxc;

use Ramsey\Uuid\Uuid;
use Hyperodactyl\Models\LxcNode;
use Hyperodactyl\Models\LxcContainer;
use Illuminate\Support\Facades\DB;
use Hyperodactyl\Jobs\Lxc\ProvisionContainerJob;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class ContainerCreationService
{
    /**
     * Validate the requested resources against the node's real configured capacity
     * (accounting for overallocation) and persist the container record. Actual
     * provisioning against the remote API is queued so that the request does not
     * block on a slow remote operation.
     *
     * @throws LxcApiException
     */
    public function handle(array $data): LxcContainer
    {
        /** @var LxcNode $node */
        $node = LxcNode::query()->findOrFail($data['lxc_node_id']);

        if ($node->maintenance_mode) {
            throw new LxcApiException('This node is currently in maintenance mode and cannot accept new containers.', 409);
        }

        $this->assertCapacity($node, 'memory', $data['memory']);
        $this->assertCapacity($node, 'disk', $data['disk']);

        return DB::transaction(function () use ($data) {
            $container = new LxcContainer($data);
            $container->uuid = Uuid::uuid4()->toString();
            $container->uuid_short = substr(str_replace('-', '', $container->uuid), 0, 8);
            $container->status = LxcContainer::STATUS_INSTALLING;
            $container->save();

            ProvisionContainerJob::dispatch($container);

            return $container;
        });
    }

    protected function assertCapacity(LxcNode $node, string $field, int $requested): void
    {
        $used = (int) $node->containers()->sum($field);
        $capacity = $node->$field + ($node->$field * ($node->{"{$field}_overallocate"} / 100));

        if ($used + $requested > $capacity) {
            throw new LxcApiException("The selected node does not have enough available {$field} to satisfy this request.", 422);
        }
    }
}
