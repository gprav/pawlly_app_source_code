@extends('backend.layouts.app')

@section('title')
    {{ $module_title }}
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <x-backend.section-header>
            <div>
                <x-backend.quick-action url='{{ route("backend.vendors.bulk_action") }}'>
                    <div class="">
                        <select name="action_type" class="form-control select2 col-12" id="quick-action-type" style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            <option value="change-status">{{ __('messages.status') }}</option>
                            <option value="delete">{{ __('messages.delete') }}</option>
                        </select>
                    </div>
                    <div class="select-status d-none quick-action-field" id="change-status-action">
                        <select name="status" class="form-control select2" id="status" style="width:100%">
                            <option value="1">{{ __('messages.active') }}</option>
                            <option value="0">{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                </x-backend.quick-action>
            </div>
            <x-slot name="toolbar">
                <div class="datatable-filter me-2">
                    <select name="column_status" id="column_status" class="select2 form-control" data-filter="select" style="width: 100%">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i class="icon-Search"></i></span>
                    <input type="text" class="form-control form-control-sm dt-search" placeholder="Search..." aria-label="Search" aria-describedby="addon-wrapping">
                </div>

                @hasPermission('add_employees')
                <a href="{{ route('backend.employees.index', ['employee_type' => 'pet_store', 'type' => 'new']) }}" class="btn btn-primary d-flex align-items-center gap-1 ms-2">
                    <i class="icon-Add"></i> {{ __('messages.new') }}
                </a>
                @endhasPermission
            </x-slot>
        </x-backend.section-header>
    </div>
    <div class="card-body">
        <table id="datatable" class="table table-striped border table-responsive">
        </table>
    </div>
</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script type="text/javascript" defer>
    const columns = [{
            name: 'check',
            data: 'check',
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            width: '0%',
            exportable: false,
            orderable: false,
            searchable: false,
        },
        {
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            title: "{{ __('messages.sr_no') }}",
            width: '5%',
            orderable: false,
            searchable: false,
        },
        {
            data: 'profile_image',
            name: 'profile_image',
            title: "{{ __('messages.image') }}",
            orderable: false,
            searchable: false,
            width: '8%'
        },
        {
            data: 'full_name',
            name: 'full_name',
            title: "{{ __('messages.name') }}",
            width: '15%',
        },
        {
            data: 'email',
            name: 'email',
            title: "{{ __('messages.email') }}",
            width: '15%',
        },
        {
            data: 'mobile',
            name: 'mobile',
            title: "{{ __('messages.mobile') }}",
            width: '12%',
        },
        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.status') }}",
            width: '8%',
        },
        {
            data: 'created_at',
            name: 'created_at',
            title: "{{ __('messages.created_at') }}",
            width: '10%',
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.action') }}",
            width: '10%',
        }
    ]

    const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('messages.action') }}",
        width: '5%'
    }]

    let finalColumns = [
        ...columns,
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.vendors.index_data") }}',
            finalColumns,
            advanceFilter: () => {
                return {
                    status: $('#column_status').val(),
                }
            },
            orderColumn: [[ 1, "desc" ]],
        });

        $('#column_status').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });
    })

    function resetQuickAction() {
        const actionValue = $('#quick-action-type').val();
        if (actionValue != '') {
            $('#quick-action-apply').removeAttr('disabled');

            if (actionValue == 'change-status') {
                $('.quick-action-field').addClass('d-none');
                $('#change-status-action').removeClass('d-none');
            } else {
                $('.quick-action-field').addClass('d-none');
            }
        } else {
            $('#quick-action-apply').attr('disabled', true);
            $('.quick-action-field').addClass('d-none');
        }
    }

    $('#quick-action-type').change(function() {
        resetQuickAction()
    });
</script>
@endpush
