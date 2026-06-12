<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Auth\Actions\DeleteUserAccountAction;
use App\Domain\Auth\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountDeletionController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private DeleteUserAccountAction $deleteAction
    ) {}

    /**
     * Display the public account deletion request form.
     * This page is publicly accessible (no authentication required)
     * as required by Google Play policy for account deletion.
     */
    public function show(): View
    {
        return view('account.delete');
    }

    /**
     * Process the account deletion request.
     * Shares deletion logic with DELETE /api/user/account.
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'confirmation' => ['required', 'in:EXCLUIR'],
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 254 caracteres.',
            'confirmation.required' => 'A confirmação é obrigatória.',
            'confirmation.in' => 'Digite exatamente a palavra EXCLUIR para confirmar.',
        ]);

        $user = $this->userRepository->findByEmail($validated['email']);

        if (!$user) {
            // For security, we don't reveal whether the email exists.
            // We show a generic success message regardless.
            return redirect()
                ->route('conta.excluir')
                ->with('success', 'Se o e-mail informado estiver cadastrado, a solicitação de exclusão será processada. Você receberá uma confirmação por e-mail.');
        }

        try {
            $deleted = $this->deleteAction->execute((string) $user->id);

            if (!$deleted) {
                return redirect()
                    ->route('conta.excluir')
                    ->with('error', 'Não foi possível processar a solicitação. Tente novamente mais tarde.');
            }

            return redirect()
                ->route('conta.excluir')
                ->with('success', 'Se o e-mail informado estiver cadastrado, a solicitação de exclusão será processada. Você receberá uma confirmação por e-mail.');
        } catch (\Exception $e) {
            return redirect()
                ->route('conta.excluir')
                ->with('error', 'Ocorreu um erro ao processar sua solicitação. Tente novamente mais tarde.');
        }
    }
}
