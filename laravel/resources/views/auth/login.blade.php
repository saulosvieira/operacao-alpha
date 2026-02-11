@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('Faça login para acessar'))

@section('adminlte_css')
<style>
    /* Login Page - Black Background */
    .login-page {
        background-color: #000000 !important;
    }

    /* Keep login box white for contrast */
    .login-box .card {
        background-color: #ffffff;
    }

    /* Ensure logo has good contrast on black background */
    .login-logo a {
        color: #ffffff !important;
    }
</style>
@endsection

@section('auth_body')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required autofocus value="{{ old('email', '') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Senha" required value="{{ old('password', '') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Lembrar-me</label>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>
        </div>
    </form>
@endsection

@section('auth_footer')
    @if (Route::has('password.request'))
        <a class="btn btn-link" href="{{ route('password.request') }}">
            Esqueceu a senha?
        </a>
    @endif
@endsection
