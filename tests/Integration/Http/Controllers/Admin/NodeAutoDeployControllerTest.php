<?php

namespace Hyperodactyl\Tests\Integration\Http\Controllers\Admin;

use Hyperodactyl\Models\Node;
use Hyperodactyl\Models\User;
use Hyperodactyl\Models\ApiKey;
use Hyperodactyl\Models\Location;
use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Tests\Integration\Http\HttpTestCase;

class NodeAutoDeployControllerTest extends HttpTestCase
{
    public function testGeneratedTokenHasNodeWritePermission(): void
    {
        $node = Node::factory()->for(Location::factory())->create();

        $response = $this->actingAs(User::factory()->admin()->create())
            ->postJson(route('admin.nodes.view.configuration.token', ['node' => $node]));

        $response->assertOk();
        $response->assertJsonPath('node', $node->id);

        $key = ApiKey::query()
            ->where('identifier', substr($response->json('token'), 0, ApiKey::IDENTIFIER_LENGTH))
            ->firstOrFail();

        $this->assertSame(AdminAcl::READ | AdminAcl::WRITE, $key->r_nodes);
    }
}
