<?php

namespace Hyperodactyl\Services\Lxc;

use Carbon\Carbon;
use Hyperodactyl\Models\LxcNode;
use Illuminate\Cache\Repository;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class NodeHealthService
{
    public function __construct(private LxcDriverFactory $factory, private Repository $cache)
    {
    }

    /**
     * Fetch real, current node resource usage from the remote API, cached briefly to
     * avoid hammering the node on every dashboard refresh.
     *
     * @throws LxcApiException
     */
    public function handle(LxcNode $node, bool $fresh = false): array
    {
        $key = "lxc-node-health:{$node->uuid}";

        if ($fresh) {
            $this->cache->forget($key);
        }

        return $this->cache->remember($key, Carbon::now()->addSeconds(15), function () use ($node) {
            return $this->factory->forNode($node)->nodeUsage();
        });
    }
}
