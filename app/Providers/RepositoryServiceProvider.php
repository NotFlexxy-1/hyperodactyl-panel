<?php

namespace Hyperodactyl\Providers;

use Illuminate\Support\ServiceProvider;
use Hyperodactyl\Repositories\Eloquent\EggRepository;
use Hyperodactyl\Repositories\Eloquent\NestRepository;
use Hyperodactyl\Repositories\Eloquent\NodeRepository;
use Hyperodactyl\Repositories\Eloquent\TaskRepository;
use Hyperodactyl\Repositories\Eloquent\UserRepository;
use Hyperodactyl\Repositories\Eloquent\ApiKeyRepository;
use Hyperodactyl\Repositories\Eloquent\ServerRepository;
use Hyperodactyl\Repositories\Eloquent\SessionRepository;
use Hyperodactyl\Repositories\Eloquent\SubuserRepository;
use Hyperodactyl\Repositories\Eloquent\DatabaseRepository;
use Hyperodactyl\Repositories\Eloquent\LocationRepository;
use Hyperodactyl\Repositories\Eloquent\ScheduleRepository;
use Hyperodactyl\Repositories\Eloquent\SettingsRepository;
use Hyperodactyl\Repositories\Eloquent\AllocationRepository;
use Hyperodactyl\Contracts\Repository\EggRepositoryInterface;
use Hyperodactyl\Repositories\Eloquent\EggVariableRepository;
use Hyperodactyl\Contracts\Repository\NestRepositoryInterface;
use Hyperodactyl\Contracts\Repository\NodeRepositoryInterface;
use Hyperodactyl\Contracts\Repository\TaskRepositoryInterface;
use Hyperodactyl\Contracts\Repository\UserRepositoryInterface;
use Hyperodactyl\Repositories\Eloquent\DatabaseHostRepository;
use Hyperodactyl\Contracts\Repository\ApiKeyRepositoryInterface;
use Hyperodactyl\Contracts\Repository\ServerRepositoryInterface;
use Hyperodactyl\Repositories\Eloquent\ServerVariableRepository;
use Hyperodactyl\Contracts\Repository\SessionRepositoryInterface;
use Hyperodactyl\Contracts\Repository\SubuserRepositoryInterface;
use Hyperodactyl\Contracts\Repository\DatabaseRepositoryInterface;
use Hyperodactyl\Contracts\Repository\LocationRepositoryInterface;
use Hyperodactyl\Contracts\Repository\ScheduleRepositoryInterface;
use Hyperodactyl\Contracts\Repository\SettingsRepositoryInterface;
use Hyperodactyl\Contracts\Repository\AllocationRepositoryInterface;
use Hyperodactyl\Contracts\Repository\EggVariableRepositoryInterface;
use Hyperodactyl\Contracts\Repository\DatabaseHostRepositoryInterface;
use Hyperodactyl\Contracts\Repository\ServerVariableRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register all the repository bindings.
     */
    public function register(): void
    {
        // Eloquent Repositories
        $this->app->bind(AllocationRepositoryInterface::class, AllocationRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(DatabaseRepositoryInterface::class, DatabaseRepository::class);
        $this->app->bind(DatabaseHostRepositoryInterface::class, DatabaseHostRepository::class);
        $this->app->bind(EggRepositoryInterface::class, EggRepository::class);
        $this->app->bind(EggVariableRepositoryInterface::class, EggVariableRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(NestRepositoryInterface::class, NestRepository::class);
        $this->app->bind(NodeRepositoryInterface::class, NodeRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(ServerRepositoryInterface::class, ServerRepository::class);
        $this->app->bind(ServerVariableRepositoryInterface::class, ServerVariableRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(SubuserRepositoryInterface::class, SubuserRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
