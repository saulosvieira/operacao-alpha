{{--
    Componente de Filtro de Busca Reutilizável para Listagens do Admin

    Uso:
    @include('admin.components.search-filter', [
        'route' => route('admin.careers.index'),
        'placeholder' => 'Buscar por nome, descrição...',
        'filter' => $filter ?? null,
        'perPageOptions' => [15, 30, 50, 100]
    ])
--}}

@php
$searchValue = $filter->search ?? request('search', '');
$currentPerPage = $filter->perPage ?? request('per_page', 15);
$perPageOptions = $perPageOptions ?? [15, 30, 50, 100];
$placeholder = $placeholder ?? 'Buscar...';
@endphp

<div class="card-header">
    <div class="row align-items-center">
        <div class="col-md-5 mb-2 mb-md-0">
            {{ $slot ?? '' }}
        </div>
        <div class="col-md-7">
            <form method="GET" action="{{ $route }}" class="form-inline justify-content-md-end">
                {{-- Campo de busca --}}
                <div class="input-group input-group-sm mr-2 mb-2 mb-md-0" style="width: 250px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="{{ $placeholder }}"
                        value="{{ $searchValue }}"
                    >
                    @if($searchValue)
                        <div class="input-group-append">
                            <a href="{{ $route }}" class="btn btn-outline-secondary" title="Limpar filtro">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Select de itens por página --}}
                <select name="per_page" class="form-control form-control-sm mr-2 mb-2 mb-md-0" style="width: auto;" onchange="this.form.submit()">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                            {{ $option }} por página
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary btn-sm mb-2 mb-md-0">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>
</div>
