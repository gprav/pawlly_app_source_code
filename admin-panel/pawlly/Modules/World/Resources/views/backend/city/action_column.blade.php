<div class="d-flex gap-2 align-items-center">
    @hasPermission('edit_city')
        <button type="button" class="fs-4 text-primary border-0 bg-transparent" data-crud-id="{{ $data->id }}"
            title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="icon-Edit"></i></button>
    @endhasPermission
    @hasPermission('delete_city')
        <a href="{{ route('backend.city.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
            class="fs-4 text-danger" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', ['module' => __('city.singular_title'), 'name' => $data->name ?? ' ']) }}">
            <i class="icon-delete"></i></a>
    @endhasPermission
</div>
