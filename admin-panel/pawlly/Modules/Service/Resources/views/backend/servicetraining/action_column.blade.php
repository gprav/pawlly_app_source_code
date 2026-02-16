<div class="d-flex gap-2 align-items-center">
    @if ($data->created_by === $user->id || $user->hasRole(['admin', 'demo_admin']))
        @can('edit_training_type')
            <button type="button" class="fs-4 text-primary border-0 bg-transparent" data-crud-id="{{ $data->id }}"
                title="{{ __('Edit') }} " data-bs-toggle="tooltip"> <i class="icon-Edit"></i></button>
        @endcan
        @can('delete_training_type')
            <a href="{{ route("backend.$module_name.destroy", $data->id) }}"
                id="delete-{{ $module_name }}-{{ $data->id }}" class="fs-4 text-danger btn-sm" data-type="ajax"
                data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                data-confirm="{{ __('messages.are_you_sure?', ['module' => $module_name, 'name' => $data->name ?? ' ']) }}">
                <i class="icon-delete"></i></a>
        @endcan
    @else
        <p> - </p>
    @endif
</div>
