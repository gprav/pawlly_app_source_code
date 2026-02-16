@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <x-backend.section-header>
                <div>
                    <x-backend.quick-action url='{{ route("backend.$module_name.bulk_action") }}'>
                        <div class="">
                            <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                style="width:100%">
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
                    <div class="input-group flex-nowrap">
                        <div class="datatable-filter">
                            <select name="training_type_rolewise" id="training_type_rolewise" class="select2 form-control"
                                data-filter="select" style="width: 100%">
                                <option value="all">{{ __('product.all') }}</option>
                                <option value="my_type">
                                    {{ __('messages.my_type') }}</option>
                                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))
                                    <option value="added_by_employee">
                                        {{ __('messages.added_by_employee') }}</option>
                                @endif
                                @unless (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))
                                    <option value="added_by_admin">
                                        {{ __('messages.added_by_admin') }}</option>
                                @endunless
                            </select>
                        </div>
                    </div>
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i class="icon-Search"></i></span>
                        <input type="text" class="form-control form-control-sm dt-search" placeholder="Search..."
                            aria-label="Search" aria-describedby="addon-wrapping">

                    </div>
                    <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('Create') }} {{ __($module_title) }}"
                        class=" d-flex align-items-center gap-1">{{ __('messages.new') }}</x-buttons.offcanvas>
                </x-slot>
            </x-backend.section-header>
        </div>
        <div class="card-body ">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    <div data-render="app">
        <service-training-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
        </service-training-offcanvas>
        <duration-list create-title="Duration List"></duration-list>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/service/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>

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
                data: 'name',
                name: 'name',
                orderable: true,
                searchable: true,
                title: "{{ __('service.lbl_name') }}"
            },
            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))
                {
                    data: 'employee',
                    name: 'employee',
                    title: "{{ __('menu.lbl_employee') }}",
                    orderable: true,
                    searchable: false,
                },
            @endif {
                data: 'duration_list',
                name: 'duration_list',
                title: "{{ __('service.lbl_duration') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('service.lbl_status') }}"
            },
        ]

        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('service.lbl_action') }}"
        }]


        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                advanceFilter: () => {
                    return {
                        training_type_rolewise: $('#training_type_rolewise').val(),
                    }
                }
            });
            let table = $('#datatable').DataTable();

            $('#training_type_rolewise').change(function() {
                const selectedValue = $(this).val();
                if (selectedValue === 'my_type') {
                    table.column('employee:name').visible(false);
                    table.column('status:name').visible(true);
                    table.column('action:name').visible(true);
                } else if (selectedValue === 'added_by_admin') {
                    table.column('status:name').visible(false);
                    table.column('action:name').visible(false);
                } else {
                    table.column('employee:name').visible(true);
                    table.column('status:name').visible(true);
                    table.column('action:name').visible(true);
                }

                table.ajax.reload();
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

        $(document).on('update_quick_action', function() {
            // resetActionButtons()
        })
    </script>
@endpush
