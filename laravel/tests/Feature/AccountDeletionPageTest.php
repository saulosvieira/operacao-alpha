<?php

use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Public Account Deletion Page (Requisito 20.6)', function () {

    it('displays the account deletion form on GET /conta/excluir', function () {
        $response = $this->get('/conta/excluir');

        $response->assertStatus(200);
        $response->assertSee('Excluir Minha Conta');
        $response->assertSee('E-mail da conta');
        $response->assertSee('Confirmação');
        $response->assertSee('EXCLUIR');
        $response->assertSee('Solicitar Exclusão da Conta');
    });

    it('is publicly accessible without authentication', function () {
        // No auth headers, no session — page should still be accessible
        $response = $this->get('/conta/excluir');

        $response->assertStatus(200);
        $response->assertDontSee('login');
    });

    it('has a stable URL without session parameters', function () {
        $response = $this->get('/conta/excluir');

        $response->assertStatus(200);
        // Should not redirect to login or any other page
        $this->assertEquals(
            url('/conta/excluir'),
            $response->baseResponse->headers->get('Location') ?? url('/conta/excluir')
        );
    });

    it('validates that email is required on submission', function () {
        $response = $this->post('/conta/excluir', [
            'email' => '',
            'confirmation' => 'EXCLUIR',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('validates that email format is valid', function () {
        $response = $this->post('/conta/excluir', [
            'email' => 'not-an-email',
            'confirmation' => 'EXCLUIR',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('validates that confirmation must be exactly EXCLUIR', function () {
        $response = $this->post('/conta/excluir', [
            'email' => 'user@example.com',
            'confirmation' => 'excluir', // lowercase should fail
        ]);

        $response->assertSessionHasErrors('confirmation');
    });

    it('validates that confirmation field is required', function () {
        $response = $this->post('/conta/excluir', [
            'email' => 'user@example.com',
            'confirmation' => '',
        ]);

        $response->assertSessionHasErrors('confirmation');
    });

    it('processes deletion for an existing user', function () {
        $user = User::factory()->create([
            'email' => 'delete-me@example.com',
        ]);

        $response = $this->post('/conta/excluir', [
            'email' => 'delete-me@example.com',
            'confirmation' => 'EXCLUIR',
        ]);

        $response->assertRedirect(route('conta.excluir'));
        $response->assertSessionHas('success');

        // User should be deleted from database
        $this->assertDatabaseMissing('users', [
            'email' => 'delete-me@example.com',
        ]);
    });

    it('shows generic success message even for non-existing email (security)', function () {
        $response = $this->post('/conta/excluir', [
            'email' => 'nonexistent@example.com',
            'confirmation' => 'EXCLUIR',
        ]);

        $response->assertRedirect(route('conta.excluir'));
        $response->assertSessionHas('success');
    });

    it('displays warning about irreversible action', function () {
        $response = $this->get('/conta/excluir');

        $response->assertSee('irreversível');
        $response->assertSee('dados pessoais serão removidos');
    });

    it('has the named route conta.excluir', function () {
        $url = route('conta.excluir');
        $this->assertStringContainsString('/conta/excluir', $url);
    });

});
