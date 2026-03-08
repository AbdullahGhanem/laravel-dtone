<?php

namespace Ghanem\Dtone\Tests\Unit;

use Ghanem\Dtone\DtoneController;
use Ghanem\Dtone\Facades\Dtone;
use Ghanem\Dtone\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_is_bound(): void
    {
        $this->assertInstanceOf(DtoneController::class, app('ghanem-dtone'));
    }

    public function test_facade_resolves(): void
    {
        $this->assertInstanceOf(DtoneController::class, Dtone::getFacadeRoot());
    }
}
