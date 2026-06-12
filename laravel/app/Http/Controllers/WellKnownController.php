<?php

/**
 * Controlador para servir arquivos de verificação .well-known
 *
 * Serve os arquivos necessários para Android App Links (assetlinks.json)
 * e iOS Universal Links (apple-app-site-association) com Content-Type correto.
 *
 * @see https://developer.android.com/training/app-links/verify-android-applinks
 * @see https://developer.apple.com/documentation/xcode/supporting-associated-domains
 *
 * Requisito 11.6 do flutter-hybrid-app spec.
 */

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class WellKnownController extends Controller
{
    /**
     * Android App Links — Digital Asset Links
     *
     * Serve /.well-known/assetlinks.json com o SHA-256 do certificado de release
     * do app Flutter para ambos os hosts do Domínio_Sistema.
     *
     * Para obter o SHA-256 fingerprint do certificado de release:
     *   keytool -list -v -keystore <path-to-release-keystore> -alias <alias>
     *
     * Ou para debug:
     *   keytool -list -v -keystore ~/.android/debug.keystore -alias androiddebugkey -storepass android
     *
     * Substitua o placeholder abaixo pelo fingerprint real antes de publicar.
     */
    public function assetLinks(): JsonResponse
    {
        $statements = [
            [
                'relation' => ['delegate_permission/common.handle_all_urls'],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => 'br.com.operacaoalfa.app',
                    'sha256_cert_fingerprints' => [
                        // TODO: Substituir pelo SHA-256 real do certificado de release
                        // Obter via: keytool -list -v -keystore <release.keystore> -alias <alias>
                        config('app.android_asset_links_sha256', 'AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99'),
                    ],
                ],
            ],
        ];

        return response()->json($statements)
            ->header('Content-Type', 'application/json');
    }

    /**
     * iOS Universal Links — Apple App Site Association
     *
     * Serve /.well-known/apple-app-site-association (sem extensão .json na URL)
     * com os paths autorizados para deep linking no app iOS.
     *
     * O appID segue o formato: <TEAM_ID>.<bundle_identifier>
     *
     * Substitua TEAM_ID_HERE pelo Apple Developer Team ID real antes de publicar.
     */
    public function appleAppSiteAssociation(): JsonResponse
    {
        $association = [
            'applinks' => [
                'apps' => [],
                'details' => [
                    [
                        'appID' => config('app.apple_team_id', 'TEAM_ID_HERE') . '.br.com.operacaoalfa.app',
                        'paths' => [
                            '/simulado/*',
                            '/dashboard',
                            '/perfil',
                            '/ranking',
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($association)
            ->header('Content-Type', 'application/json');
    }
}
