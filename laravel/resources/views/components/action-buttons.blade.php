@props([
    'id',
    'entity', // Ex: 'aerodromos', 'clientes', etc.
    'viewRoute' => null, // Ex: 'aerodromos.show'
    'editRoute' => null, // Ex: 'aerodromos.edit'
    'deleteRoute' => null, // Ex: 'aerodromos.destroy'
])

<div class="btn-group btn-group-sm" role="group" data-entity="{{ $entity }}" style="pointer-events: auto;">
    @if($viewRoute)
        <a href="{{ route($viewRoute, $id) }}"
           class="btn btn-outline-primary"
           title="Visualizar"
           data-bs-toggle="tooltip"
           data-bs-placement="top">
            <i class="far fa-eye"></i>
        </a>
    @endif
    
    @if($editRoute)
        <a href="{{ route($editRoute, $id) }}"
            class="btn btn-outline-secondary"
            title="Editar"
            data-bs-toggle="tooltip"
            data-bs-placement="top">
            <i class="far fa-edit"></i>
        </a>
    @endif
    
    @if($deleteRoute)
        <button type="button"
            class="btn btn-outline-danger delete-btn"
            data-id="{{ $id }}"
            title="Excluir"
            data-bs-toggle="tooltip"
            data-bs-placement="top">
            <i class="far fa-trash-alt"></i>
        </button>
        <form action="{{ route($deleteRoute, $id) }}" 
              method="POST" 
              class="d-none"
              id="delete-form-{{ $entity }}-{{ $id }}">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

{{-- O modal de confirmação de exclusão é incluído apenas uma vez no layout principal --}}
@once
    @if($deleteRoute)
        @push('delete-modals')
            @include('components.delete-confirmation')
        @endpush
    @endif
@endonce
