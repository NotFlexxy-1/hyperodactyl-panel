<?php

namespace Hyperodactyl\Services\Lxc;

use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class ContainerDeletionService
{
    public function __construct(private LxcDriverFactory $factory)
    {
    }

    /**
     * @throws LxcApiException
     */
    public function handle(LxcContainer $container, bool $force = false): void
    {
        try {
            $this->factory->forNode($container->node)->delete($container);
        } catch (LxcApiException $exception) {
            if (!$force) {
                throw $exception;
            }
        }

        $container->delete();
    }
}
