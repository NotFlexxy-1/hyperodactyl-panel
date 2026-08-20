<?php

namespace Hyperodactyl\Jobs\Lxc;

use Hyperodactyl\Models\LxcContainer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class ProvisionContainerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(private LxcContainer $container)
    {
    }

    public function handle(LxcDriverFactory $factory): void
    {
        try {
            $driver = $factory->forNode($this->container->node);
            $driver->create($this->container);
            $driver->start($this->container);

            $this->container->update([
                'status' => LxcContainer::STATUS_RUNNING,
                'installed_at' => now(),
            ]);
        } catch (LxcApiException $exception) {
            $this->container->update([
                'status' => LxcContainer::STATUS_INSTALL_FAILED,
                'description' => trim(($this->container->description ?? '') . "\nProvisioning failed: " . $exception->getMessage()),
            ]);

            throw $exception;
        }
    }
}
