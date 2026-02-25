<?php

// Feature: edduz-subscription-integration, Property 10: Configuração incompleta desabilita integração

use App\Services\EdduzApiClient;
use Faker\Factory as Faker;

/**
 * Validates: Requirement 5.4
 *
 * Property 10: Configuração incompleta desabilita integração
 *
 * For any missing required Edduz environment variable (EDDUZ_API_URL, EDDUZ_API_KEY,
 * EDDUZ_WEBHOOK_TOKEN), EdduzApiClient::isConfigured() must return false.
 * When all three are present and non-empty, isConfigured() must return true.
 */

test('Property 10: isConfigured() retorna false quando EDDUZ_API_URL está ausente', function () {
    $faker = Faker::create();
    $iterations = 100;

    for ($i = 0; $i < $iterations; $i++) {
        $apiKey = $faker->uuid();
        $webhookToken = $faker->sha256();

        // Empty/null baseUrl
        $emptyValues = ['', null];
        $emptyBaseUrl = $emptyValues[array_rand($emptyValues)] ?? '';

        $client = new EdduzApiClient(
            baseUrl: (string) $emptyBaseUrl,
            apiKey: $apiKey,
            webhookToken: $webhookToken,
        );

        expect($client->isConfigured())->toBeFalse(
            "isConfigured() deve retornar false quando EDDUZ_API_URL está vazio (iteração {$i})"
        );
    }
});

test('Property 10: isConfigured() retorna false quando EDDUZ_API_KEY está ausente', function () {
    $faker = Faker::create();
    $iterations = 100;

    for ($i = 0; $i < $iterations; $i++) {
        $baseUrl = $faker->url();
        $webhookToken = $faker->sha256();

        $client = new EdduzApiClient(
            baseUrl: $baseUrl,
            apiKey: '',
            webhookToken: $webhookToken,
        );

        expect($client->isConfigured())->toBeFalse(
            "isConfigured() deve retornar false quando EDDUZ_API_KEY está vazio (iteração {$i})"
        );
    }
});

test('Property 10: isConfigured() retorna false quando EDDUZ_WEBHOOK_TOKEN está ausente', function () {
    $faker = Faker::create();
    $iterations = 100;

    for ($i = 0; $i < $iterations; $i++) {
        $baseUrl = $faker->url();
        $apiKey = $faker->uuid();

        $client = new EdduzApiClient(
            baseUrl: $baseUrl,
            apiKey: $apiKey,
            webhookToken: '',
        );

        expect($client->isConfigured())->toBeFalse(
            "isConfigured() deve retornar false quando EDDUZ_WEBHOOK_TOKEN está vazio (iteração {$i})"
        );
    }
});

test('Property 10: isConfigured() retorna false quando qualquer variável obrigatória está ausente', function () {
    $faker = Faker::create();
    $iterations = 100;

    for ($i = 0; $i < $iterations; $i++) {
        $baseUrl = $faker->url();
        $apiKey = $faker->uuid();
        $webhookToken = $faker->sha256();

        // Randomly pick which variable to omit (0=baseUrl, 1=apiKey, 2=webhookToken)
        $missingIndex = $i % 3;

        $client = new EdduzApiClient(
            baseUrl: $missingIndex === 0 ? '' : $baseUrl,
            apiKey: $missingIndex === 1 ? '' : $apiKey,
            webhookToken: $missingIndex === 2 ? '' : $webhookToken,
        );

        expect($client->isConfigured())->toBeFalse(
            "isConfigured() deve retornar false quando a variável #{$missingIndex} está ausente (iteração {$i})"
        );
    }
});

test('Property 10: isConfigured() retorna true quando todas as variáveis obrigatórias estão presentes', function () {
    $faker = Faker::create();
    $iterations = 100;

    for ($i = 0; $i < $iterations; $i++) {
        $baseUrl = $faker->url();
        $apiKey = $faker->uuid();
        $webhookToken = $faker->sha256();

        $client = new EdduzApiClient(
            baseUrl: $baseUrl,
            apiKey: $apiKey,
            webhookToken: $webhookToken,
        );

        expect($client->isConfigured())->toBeTrue(
            "isConfigured() deve retornar true quando todas as variáveis estão presentes (iteração {$i})"
        );
    }
});
