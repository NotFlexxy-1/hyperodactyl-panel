<?php

namespace Hyperodactyl\Tests\Integration\Api\Application\Nodes;

use Hyperodactyl\Models\Node;
use Hyperodactyl\Models\Location;
use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Tests\Integration\Api\Application\ApplicationApiIntegrationTestCase;

class NodeConfigurationControllerTest extends ApplicationApiIntegrationTestCase
{
    public function testWriteNodeKeyCanGetNodeConfiguration(): void
    {
        $this->createNewDefaultApiKey($this->getApiUser(), ['r_nodes' => AdminAcl::READ | AdminAcl::WRITE]);

        $node = Node::factory()->for(Location::factory())->create();

        $response = $this->getJson('/api/application/nodes/' . $node->id . '/configuration');

        $response->assertOk();
        $response->assertJsonPath('uuid', $node->uuid);
        $response->assertJsonPath('token_id', $node->daemon_token_id);
        $this->assertSame(decrypt($node->daemon_token), $response->json('token'));
    }

    public function testReadOnlyNodeKeyCannotGetNodeConfiguration(): void
    {
        $this->createNewDefaultApiKey($this->getApiUser(), ['r_nodes' => AdminAcl::READ]);

        $node = Node::factory()->for(Location::factory())->create();

        $this->assertAccessDeniedJson($this->getJson('/api/application/nodes/' . $node->id . '/configuration'));
    }
}
