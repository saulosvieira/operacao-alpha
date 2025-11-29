@props([
    'headers' => [],
    'items' => [],
    'emptyMessage' => 'Nenhum registro encontrado.',
    'id' => 'dataTable',
    'withActions' => true,
    'viewRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null
])

@inject('tableHelper', 'App\Helpers\TableHelper')

@php
    $hasActions = $withActions && ($viewRoute || $editRoute || $deleteRoute);
@endphp

<div class="table-responsive">
    <table class="table align-middle" id="{{ $id }}">
        <thead class="table-light">
            <tr>
                @foreach($headers as $header)
                    <th class="align-middle" {!! $header['attributes'] ?? '' !!}>
                        @if(isset($header['sortable']) && $header['sortable'])
                            <a href="{{ $tableHelper::sortUrl($header['sort']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                <span>{{ $header['label'] }}</span>
                                @if(request('sort') === $header['sort'])
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @elseif(isset($header['sort']))
                                    <i class="fas fa-sort text-muted ms-1"></i>
                                @endif
                            </a>
                        @else
                            <span class="d-flex align-items-center">
                                <span>{{ $header['label'] }}</span>
                            </span>
                        @endif
                    </th>
                @endforeach

                @if($hasActions)
                    <th class="text-end align-middle pe-3" style="width: 15%;">Ações</th>
                @endif
            </tr>
        </thead>
        <tbody class="border-top">
            @if(count($items) > 0)
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ count($headers) + ($hasActions ? 1 : 0) }}" class="text-center py-4">
                        <div class="py-5">
                            <p class="mb-0 text-muted">{{ $emptyMessage }}</p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
