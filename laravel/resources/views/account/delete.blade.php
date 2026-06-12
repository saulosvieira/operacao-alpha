<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Excluir Conta - Operação Alfa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 560px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo h1 {
            font-size: 1.5rem;
            color: #1a237e;
            font-weight: 700;
        }

        .logo p {
            color: #666;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        h2 {
            font-size: 1.25rem;
            color: #c62828;
            margin-bottom: 1rem;
            text-align: center;
        }

        .warning-box {
            background-color: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .warning-box h3 {
            color: #e65100;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .warning-box ul {
            list-style: disc;
            padding-left: 1.25rem;
            font-size: 0.85rem;
            color: #555;
        }

        .warning-box ul li {
            margin-bottom: 0.25rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 0.4rem;
        }

        input[type="email"],
        input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        input[type="email"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }

        .hint {
            font-size: 0.8rem;
            color: #777;
            margin-top: 0.3rem;
        }

        .error-message {
            color: #c62828;
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        .btn-delete {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background-color: #c62828;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 0.5rem;
        }

        .btn-delete:hover {
            background-color: #b71c1c;
        }

        .btn-delete:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
            color: #2e7d32;
        }

        .alert-error {
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
            color: #c62828;
        }

        .footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #999;
        }

        .footer a {
            color: #1a237e;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Operação Alfa</h1>
            <p>Solicitação de Exclusão de Conta</p>
        </div>

        <h2>Excluir Minha Conta</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="warning-box">
            <h3>⚠️ Atenção — Esta ação é irreversível</h3>
            <ul>
                <li>Todos os seus dados pessoais serão removidos</li>
                <li>Seu histórico de simulados e estatísticas será apagado</li>
                <li>Sua assinatura ativa (se houver) será cancelada</li>
                <li>Você não poderá recuperar sua conta após a exclusão</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('conta.excluir.process') }}" id="delete-form">
            @csrf

            <div class="form-group">
                <label for="email">E-mail da conta</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="seu@email.com"
                    required
                    maxlength="254"
                    autocomplete="email"
                >
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="confirmation">Confirmação</label>
                <input
                    type="text"
                    id="confirmation"
                    name="confirmation"
                    value="{{ old('confirmation') }}"
                    placeholder="Digite EXCLUIR"
                    required
                    autocomplete="off"
                >
                <p class="hint">Digite a palavra <strong>EXCLUIR</strong> (em maiúsculas) para confirmar a exclusão.</p>
                @error('confirmation')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-delete" id="btn-submit">
                Solicitar Exclusão da Conta
            </button>
        </form>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Operação Alfa. Todos os direitos reservados.</p>
            <p style="margin-top: 0.5rem;">
                Em conformidade com a <a href="https://www.gov.br/cidadania/pt-br/acesso-a-informacao/lgpd" target="_blank" rel="noopener">LGPD</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmationInput = document.getElementById('confirmation');
            const submitBtn = document.getElementById('btn-submit');

            function toggleButton() {
                submitBtn.disabled = confirmationInput.value !== 'EXCLUIR';
            }

            confirmationInput.addEventListener('input', toggleButton);
            toggleButton();
        });
    </script>
</body>
</html>
