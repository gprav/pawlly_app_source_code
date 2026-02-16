@extends('backend.layouts.app')

@section('title', __($module_title))

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-body">

            <div class="row mt-4">
                <div class="col">

                    <table id="datatable" class="table table-striped border table-responsive notification-table">
                        <thead>
                            <tr>
                                <th>
                                    {{ __('notification.lbl_id') }}
                                </th>
                                <th>
                                    {{ __('notification.type') }}
                                </th>
                                <th>
                                    {{ __('notification.lbl_text') }}
                                </th>
                                <th>
                                    {{ __('notification.lbl_customer') }}
                                </th>
                                <th>
                                    {{ __('notification.lbl_update') }}
                                </th>
                                <th>
                                    {{ __('notification.lbl_action') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($$module_name as $module_name_singular)
                                <?php
                                $row_class = '';
                                $span_class = '';
                                if ($module_name_singular->read_at == '') {
                                    $row_class = 'table-info';
                                    $span_class = 'font-weight-bold';
                                }
                                ?>

                                <input type="hidden" id="idData" value="{{ $module_name_singular->id }}">

                                <tr class="{{ $row_class }}">
                                    <td>
                                        @if (optional($module_name_singular->data['data'])->notification_group == 'booking')
                                            <a
                                                href="{{ route('backend.bookings.bookingShow', ['id' => optional($module_name_singular->data['data'])->id, 'notification_id' => $module_name_singular->id]) }}">
                                                #{{ optional($module_name_singular->data['data'])->id }}
                                            </a>
                                        @else
                                            <a
                                                href="{{ route('backend.orders.show', ['id' => optional($module_name_singular->data['data'])->id, 'notification_id' => $module_name_singular->id]) }}">
                                                #{{ optional($module_name_singular->data['data'])->id }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="{{ $span_class }}">{{ ucfirst(optional($module_name_singular->data['data'])->notification_group) }}</span>
                                    </td>
                                    @php
                                        $notification = \Modules\NotificationTemplate\Models\NotificationTemplateContentMapping::where(
                                            'subject',
                                            $module_name_singular->data['subject'],
                                        )->first();
                                    @endphp
                                    <td>
                                        <div class="d-flex gap-3 align-items-center">
                                            <div class="text-start">
                                                <a href="#">
                                                    <h6>{{ $module_name_singular->data['subject'] }}</h6>
                                                </a>
                                                <span
                                                    class="{{ $span_class }}">{{ optional($notification)->notification_message }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    @php
                                        $user = \App\Models\User::find(
                                            optional($module_name_singular->data['data'])->user_id,
                                        );
                                    @endphp
                                    <td>
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ optional($user)->profile_image ?? default_user_avatar() }}"
                                                alt="avatar" class="avatar avatar-40 rounded-pill">
                                            <div class="text-start">
                                                <h6 class="m-0">{{ optional($user)->full_name ?? default_user_name() }}
                                                </h6>
                                                <span>{{ optional($user)->email ?? '--' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $module_name_singular->updated_at->diffForHumans() }}
                                    </td>
                                    <td>
                                        <a onclick="remove_notification()"
                                            id="delete-{{ $module_name }}-{{ $module_name_singular->id }}"
                                            class="fs-4 text-danger" data-type="ajax" data-method="DELETE"
                                            data-token="{{ csrf_token() }}" data-bs-toggle="tooltip"
                                            title="{{ __('Delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
                                            <i class="icon-delete"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('No data found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-7">

                    <!-- <div class="float-left">

                                        {{ __('Total') }} {{ $$module_name->total() }} {{ __($module_name) }}
                                    </div> -->
                    <form id="paginationForm" method="GET" action="{{ url()->current() }}" class="d-inline">
                        <label for="perPageSelect" class="me-2">Show</label>
                        <select name="per_page" id="perPageSelect" class="form-select form-select-sm d-inline w-auto"
                            onchange="document.getElementById('paginationForm').submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="ms-2">entries</span>
                    </form>
                    Showing {{ $$module_name->firstItem() }} to {{ $$module_name->lastItem() }} of
                    {{ $$module_name->total() }} entries
                </div>
                <div class="col-5">
                    <div class="float-end">
                        {!! $$module_name->render() !!}
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('after-scripts')
        <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
        <script src="{{ mix('js/vue.min.js') }}"></script>
        <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>

        <script>
            function remove_notification() {
                var id = document.getElementById('idData').value; 
                var url = "{{ route('notification.remove', ['id' => ':id']) }}";
                url = url.replace(':id', id);

                var message = 'Are you certain you want to delete it? ID: ' + id;

                confirmSwal(message).then((result) => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted',
                                text: response.message,
                                icon: 'success'
                            });
                            window.location.reload();
                            successSnackbar(response.message);
                        },
                        error: function(response) {
                            alert('error');
                        }
                    });
                });
            }
        </script>
    @endpush
@endsection
