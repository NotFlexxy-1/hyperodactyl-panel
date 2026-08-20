<?php

namespace Hyperodactyl\Services\Lxc;

use Hyperodactyl\Models\LxcContainer;
use Illuminate\Support\Facades\DB;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class ResourceUpdateService
{
    public function __construct(private LxcDriverFactory $factory)
    {
    }

    /**
     * Update the container's limits both in the database and, critically, on the
     * remote node itself via a real API call.
     *
     * @throws LxcApiException
     */
    public function handle(LxcContainer $container, array $data): LxcContainer
    {
        return DB::transaction(function () use ($container, $data) {
            $container->fill($data);
            $container->save();

            $this->factory->forNode($container->node)->updateLimits($container);

            return $container;
        });
    }
}
