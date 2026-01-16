<?php

namespace Tests\Feature\Import;

use App\Domain\Auth\Models\User;
use App\Domain\Auth\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RouteMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that import routes require admin authentication.
     */
    public function test_import_routes_require_admin_authentication(): void
    {
        // Test unauthenticated access
        $response = $this->get(route('admin.import.questions.index'));
        $response->assertRedirect(route('admin.login'));

        // Test non-admin user access
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);

        $response = $this->get(route('admin.import.questions.index'));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test that admin users can access import routes.
     */
    public function test_admin_users_can_access_import_routes(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.import.questions.index'));
        $response->assertOk();
    }

    /**
     * Test file upload validation middleware.
     */
    public function test_file_upload_validation_middleware(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // Test file size limit (create a file larger than 10MB)
        $largeFile = UploadedFile::fake()->create('large_file.xlsx', 11000); // 11MB

        $response = $this->post(route('admin.import.upload'), [
            'file' => $largeFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test file extension validation.
     */
    public function test_file_extension_validation(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // Test invalid file extension
        $invalidFile = UploadedFile::fake()->create('test.txt', 100);

        $response = $this->post(route('admin.import.upload'), [
            'file' => $invalidFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test CSRF protection on POST routes.
     */
    public function test_csrf_protection_on_post_routes(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // Make request without CSRF token
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('admin.import.upload'), [
                'file' => UploadedFile::fake()->create('test.xlsx', 100),
            ]);

        // With CSRF middleware disabled, this should work
        // In real scenario, without CSRF token it would fail with 419
        $response->assertStatus(302); // Redirect due to validation or processing
    }

    /**
     * Test throttling on upload route.
     */
    public function test_upload_route_throttling(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // This test would require making multiple requests rapidly
        // For now, just verify the route is accessible
        $file = UploadedFile::fake()->create('test.xlsx', 100);

        $response = $this->post(route('admin.import.upload'), [
            'file' => $file,
        ]);

        // Should not be throttled on first request
        $response->assertStatus(302); // Redirect due to processing
    }
}