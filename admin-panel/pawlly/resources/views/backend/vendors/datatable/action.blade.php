<div class="d-flex gap-2 align-items-center">
    @hasPermission('view_employees')
    <a href="{{ route('backend.vendors.show', $data->id) }}" class="btn btn-sm btn-icon btn-success" data-bs-toggle="tooltip" title="View Details">
        <i class="icon-Eye"></i>
    </a>
    @endhasPermission

    @hasPermission('edit_employees')
    <a href="{{ route('backend.employees.index', ['employee_type' => 'pet_store']) }}?id={{ $data->id }}" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" title="Edit Vendor">
        <i class="icon-Edit"></i>
    </a>
    @endhasPermission

    @hasPermission('delete_employees')
    <a href="{{ route('backend.vendors.destroy', $data->id) }}" class="btn btn-sm btn-icon btn-danger" data-method="delete" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="Delete Vendor" data-confirm="Are you sure?">
        <i class="icon-delete"></i>
    </a>
    @endhasPermission
</div>
