{{-- Componente de paginação universal Voemtx --}}
{{-- Componente de paginação universal Voemtx --}}
@if ($paginator->hasPages())
    <div class="d-flex justify-content-center">
        {!! $paginator->links() !!}
    </div>
@endif
