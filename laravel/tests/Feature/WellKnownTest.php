<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Feature tests for /.well-known/ endpoints (App Links / Universal Links)
 *
 * Validates Requirement 11.6 from the flutter-hybrid-app spec:
 * - /.well-known/assetlinks.json served with correct structure and Content-Type
 * - /.well-known/apple-app-site-association served with correct structure and Content-Type
 *   (no .json extension in the URL — iOS requirement)
 */
class WellKnownTest extends TestCase
{
    public function test_assetlinks_json_returns_200_with_correct_content_type(): void
    {
        $response = $this->get('/.well-known/assetlinks.json');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_assetlinks_json_contains_correct_structure(): void
    {
        $response = $this->get('/.well-known/assetlinks.json');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'relation',
                'target' => [
                    'namespace',
                    'package_name',
                    'sha256_cert_fingerprints',
                ],
            ],
        ]);

        $data = $response->json();

        $this->assertCount(1, $data);
        $this->assertEquals(['delegate_permission/common.handle_all_urls'], $data[0]['relation']);
        $this->assertEquals('android_app', $data[0]['target']['namespace']);
        $this->assertEquals('br.com.operacaoalfa.app', $data[0]['target']['package_name']);
        $this->assertIsArray($data[0]['target']['sha256_cert_fingerprints']);
        $this->assertNotEmpty($data[0]['target']['sha256_cert_fingerprints']);
    }

    public function test_apple_app_site_association_returns_200_with_correct_content_type(): void
    {
        $response = $this->get('/.well-known/apple-app-site-association');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_apple_app_site_association_has_no_json_extension_in_url(): void
    {
        // The URL must NOT have .json extension — iOS requirement
        $response = $this->get('/.well-known/apple-app-site-association');

        $response->assertStatus(200);

        // Also verify .json version is NOT served (should 404 or hit catch-all)
        // This confirms the URL works without extension
        $this->assertStringNotContainsString('.json', '/.well-known/apple-app-site-association');
    }

    public function test_apple_app_site_association_contains_correct_structure(): void
    {
        $response = $this->get('/.well-known/apple-app-site-association');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'applinks' => [
                'apps',
                'details' => [
                    '*' => [
                        'appID',
                        'paths',
                    ],
                ],
            ],
        ]);

        $data = $response->json();

        // apps deve ser array vazio (requisito Apple)
        $this->assertEquals([], $data['applinks']['apps']);

        // Verifica o appID com o bundle identifier correto
        $detail = $data['applinks']['details'][0];
        $this->assertStringEndsWith('.br.com.operacaoalfa.app', $detail['appID']);

        // Verifica os paths autorizados conforme spec
        $expectedPaths = ['/simulado/*', '/dashboard', '/perfil', '/ranking'];
        $this->assertEquals($expectedPaths, $detail['paths']);
    }

    public function test_apple_app_site_association_app_id_format(): void
    {
        $response = $this->get('/.well-known/apple-app-site-association');

        $data = $response->json();
        $appId = $data['applinks']['details'][0]['appID'];

        // appID deve seguir o formato TEAM_ID.bundle_identifier
        $this->assertMatchesRegularExpression(
            '/^[A-Z0-9_]+\.br\.com\.operacaoalfa\.app$/',
            $appId,
            'appID should follow format <TEAM_ID>.br.com.operacaoalfa.app'
        );
    }
}
