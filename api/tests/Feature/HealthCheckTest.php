<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_settings_endpoint_returns_success(): void
    {
        $response = $this->get('/api/settings');

        $response->assertStatus(200);
        $response->assertJson([
            'site_name' => 'CMS Website',
            'version' => '1.0.0',
        ]);
    }
}
