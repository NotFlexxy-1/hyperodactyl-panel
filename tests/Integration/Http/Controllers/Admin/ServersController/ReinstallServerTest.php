<?php

namespace Hyperodactyl\Tests\Integration\Http\Controllers\Admin\ServersController;

use Hyperodactyl\Models\User;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Tests\Integration\Http\HttpTestCase;
use Hyperodactyl\Repositories\Wings\DaemonServerRepository;

class ReinstallServerTest extends HttpTestCase
{
    /**
     * Test that a server can be reinstalled from the admin area.
     */
    public function testServerCanBeReinstalled(): void
    {
        $server = $this->createServerModel();

        $service = \Mockery::mock(DaemonServerRepository::class);
        $this->app->instance(DaemonServerRepository::class, $service);

        $service->expects('setServer')
            ->with(\Mockery::on(fn ($value) => $value->uuid === $server->uuid))
            ->andReturnSelf()
            ->getMock()
            ->expects('reinstall')
            ->andReturnUndefined();

        $this->actingAs(User::factory()->admin()->create())
            ->withHeaders(['Accept' => 'text/html'])
            ->post(route('admin.servers.view.manage.reinstall', ['server' => $server]))
            ->assertRedirect();

        $this->assertSame(Server::STATUS_INSTALLING, $server->refresh()->status);
    }

    /**
     * Test that a server configured to skip its egg's install script cannot be reinstalled from
     * the admin area.
     */
    public function testServerConfiguredToSkipScriptsCannotBeReinstalled(): void
    {
        $server = $this->createServerModel(['skip_scripts' => true]);

        $service = \Mockery::mock(DaemonServerRepository::class);
        $this->app->instance(DaemonServerRepository::class, $service);

        $service->expects('setServer')->never();

        $this->actingAs(User::factory()->admin()->create())
            ->withHeaders(['Accept' => 'text/html'])
            ->post(route('admin.servers.view.manage.reinstall', ['server' => $server]))
            ->assertRedirect();

        $this->assertNull($server->refresh()->status);
    }
}
