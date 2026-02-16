@extends('backend.layouts.app')

@section('title')
    {{ $module_title }}
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <!-- Vendor Profile Card -->
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $vendor->profile_image ?? default_user_avatar() }}" class="rounded-circle avatar avatar-120 mb-3" alt="{{ $vendor->full_name }}">
                <h4 class="mb-1">{{ $vendor->full_name }}</h4>
                <p class="text-muted mb-3">{{ $vendor->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($vendor->status == 1)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif

                    @if($vendor->enable_store == 1)
                        <span class="badge bg-primary">Store Enabled</span>
                    @else
                        <span class="badge bg-secondary">Store Disabled</span>
                    @endif
                </div>

                <div class="border-top pt-3">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h5 class="mb-0">{{ number_format($stats['total_products']) }}</h5>
                            <small class="text-muted">Products</small>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-0">{{ number_format($stats['total_orders']) }}</h5>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Email</label>
                    <p class="mb-0">{{ $vendor->email ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Phone</label>
                    <p class="mb-0">{{ $vendor->mobile ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Address</label>
                    <p class="mb-0">{{ $vendor->address ?? 'N/A' }}</p>
                </div>
                <div class="mb-0">
                    <label class="text-muted small">Member Since</label>
                    <p class="mb-0">{{ $vendor->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-50 bg-soft-primary rounded-circle">
                                    <i class="icon-Product text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Total Products</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-50 bg-soft-success rounded-circle">
                                    <i class="icon-Orders text-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Total Orders</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-50 bg-soft-warning rounded-circle">
                                    <i class="icon-tex text-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Total Revenue</h6>
                                <h3 class="mb-0">{{ Currency::format($stats['total_revenue']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-50 bg-soft-info rounded-circle">
                                    <i class="icon-Reviews text-info"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Average Rating</h6>
                                <h3 class="mb-0">{{ number_format($stats['average_rating'], 1) }} <small class="text-muted">({{ $stats['total_reviews'] }} reviews)</small></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @hasPermission('edit_employees')
                    <div class="col-md-4">
                        <a href="{{ route('backend.employees.index', ['employee_type' => 'pet_store']) }}?id={{ $vendor->id }}" class="btn btn-primary w-100">
                            <i class="icon-Edit me-2"></i> Edit Vendor
                        </a>
                    </div>
                    @endhasPermission

                    <div class="col-md-4">
                        <a href="{{ route('backend.products.index') }}?vendor_id={{ $vendor->id }}" class="btn btn-success w-100">
                            <i class="icon-Product me-2"></i> View Products
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="{{ route('backend.orders.index') }}?vendor_id={{ $vendor->id }}" class="btn btn-info w-100">
                            <i class="icon-Orders me-2"></i> View Orders
                        </a>
                    </div>

                    @hasPermission('edit_employees')
                    <div class="col-md-6">
                        <button class="btn btn-outline-{{ $vendor->status == 1 ? 'danger' : 'success' }} w-100" onclick="toggleVendorStatus({{ $vendor->id }}, {{ $vendor->status }})">
                            <i class="icon-{{ $vendor->status == 1 ? 'close' : 'check' }} me-2"></i>
                            {{ $vendor->status == 1 ? 'Deactivate' : 'Activate' }} Vendor
                        </button>
                    </div>

                    <div class="col-md-6">
                        <button class="btn btn-outline-{{ $vendor->enable_store == 1 ? 'warning' : 'primary' }} w-100" onclick="toggleStoreStatus({{ $vendor->id }}, {{ $vendor->enable_store }})">
                            <i class="icon-{{ $vendor->enable_store == 1 ? 'close' : 'check' }} me-2"></i>
                            {{ $vendor->enable_store == 1 ? 'Disable' : 'Enable' }} Store
                        </button>
                    </div>
                    @endhasPermission
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Username</label>
                        <p class="mb-0">{{ $vendor->username ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Gender</label>
                        <p class="mb-0">{{ ucfirst($vendor->gender ?? 'N/A') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Date of Birth</label>
                        <p class="mb-0">{{ $vendor->date_of_birth ? $vendor->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Last Updated</label>
                        <p class="mb-0">{{ $vendor->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
function toggleVendorStatus(vendorId, currentStatus) {
    const newStatus = currentStatus == 1 ? 0 : 1;
    const action = newStatus == 1 ? 'activate' : 'deactivate';

    if (confirm(`Are you sure you want to ${action} this vendor?`)) {
        $.ajax({
            url: '{{ url("admin-panel/vendors") }}/' + vendorId + '/update-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.status) {
                    location.reload();
                }
            },
            error: function() {
                alert('Failed to update vendor status');
            }
        });
    }
}

function toggleStoreStatus(vendorId, currentStatus) {
    const newStatus = currentStatus == 1 ? 0 : 1;
    const action = newStatus == 1 ? 'enable' : 'disable';

    if (confirm(`Are you sure you want to ${action} this vendor's store?`)) {
        $.ajax({
            url: '{{ url("admin-panel/vendors") }}/' + vendorId + '/update-store-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enable_store: newStatus
            },
            success: function(response) {
                if(response.status) {
                    location.reload();
                }
            },
            error: function() {
                alert('Failed to update store status');
            }
        });
    }
}
</script>
@endpush
