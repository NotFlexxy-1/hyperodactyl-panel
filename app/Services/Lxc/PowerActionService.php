<?php

namespace Hyperodactyl\Services\Lxc;

use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class PowerActionService
{
    public const ACTIONS = ['start', 'stop', 'restart', 'freeze'];

    public function __construct(private LxcDriverFactory $factory)
    {
    }

    /**
     * @throws LxcApiException
     */
    public function handle(LxcContainer $container, string $action): void
    {
        if (!in_array($action, self::ACTIONS, true)) {
            throw new LxcApiException("Unknown power action [{$action}].", 422);
        }

        $driver = $this->factory->forNode($container->node);
        $driver->$action($container);

        $container->update(['status' => $driver->state($container)]);
    }
}
