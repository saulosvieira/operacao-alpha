@push('css')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<style>
    /* Operação Alpha - Custom Styles */
    .brand-link {
        font-weight: 600;
    }

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
@endpush
