<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiMinVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_api_response_includes_x_api_min_version_header(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/me');

        $response->assertHeader('X-API-Min-Version', '1.0.0');
    }

    public function test_unauthenticated_api_response_does_not_include_x_api_min_version_header(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertHeaderMissing('X-API-Min-Version');
    }

    public function test_header_value_matches_config(): void
    {
        config(['mobile.min_version' => '2.3.1']);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/me');

        $response->assertHeader('X-API-Min-Version', '2.3.1');
    }

    public function test_header_defaults_to_1_0_0_when_env_not_set(): void
    {
        config(['mobile.min_version' => null]);

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/me');

        // Middleware defaults to '1.0.0' when config returns null
        $response->assertHeader('X-API-Min-Version', '1.0.0');
    }
}
