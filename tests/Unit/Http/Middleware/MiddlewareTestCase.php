<?php

namespace Hyperodactyl\Tests\Unit\Http\Middleware;

use Hyperodactyl\Tests\TestCase;
use Hyperodactyl\Tests\Traits\Http\RequestMockHelpers;
use Hyperodactyl\Tests\Traits\Http\MocksMiddlewareClosure;
use Hyperodactyl\Tests\Assertions\MiddlewareAttributeAssertionsTrait;

abstract class MiddlewareTestCase extends TestCase
{
    use MiddlewareAttributeAssertionsTrait;
    use MocksMiddlewareClosure;
    use RequestMockHelpers;

    /**
     * Setup tests with a mocked request object and normal attributes.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->buildRequestMock();
    }
}
