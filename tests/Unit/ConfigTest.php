<?php

namespace Ghanem\Dtone\Tests\Unit;

use Ghanem\Dtone\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function test_config_has_required_keys(): void
    {
        $this->assertNotNull(config('dtone.key'));
        $this->assertNotNull(config('dtone.secret'));
        $this->assertNotNull(config('dtone.test_key'));
        $this->assertNotNull(config('dtone.test_secret'));
        $this->assertIsBool(config('dtone.is_production'));
    }

    public function test_sandbox_mode_by_default(): void
    {
        $this->assertFalse(config('dtone.is_production'));
    }

    public function test_config_values_match_environment(): void
    {
        $this->assertEquals('test-key', config('dtone.key'));
        $this->assertEquals('test-secret', config('dtone.secret'));
        $this->assertEquals('test-sandbox-key', config('dtone.test_key'));
        $this->assertEquals('test-sandbox-secret', config('dtone.test_secret'));
    }

    public function test_retry_config_defaults(): void
    {
        $this->assertEquals(0, config('dtone.retries'));
        $this->assertEquals(100, config('dtone.retry_delay'));
    }
}
