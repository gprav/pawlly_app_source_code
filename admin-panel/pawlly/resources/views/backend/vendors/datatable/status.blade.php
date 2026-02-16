<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" role="switch" name="status" id="status-{{ $data->id }}"
        {{ $data->status == 1 ? 'checked' : '' }}
        data-url="{{ route('backend.vendors.update_status', $data->id) }}"
        onchange="updateStatus(this, '{{ $data->id }}')">
</div>

<script>
function updateStatus(element, id) {
    const url = $(element).data('url');
    const status = $(element).is(':checked') ? 1 : 0;

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function(response) {
            if(response.status) {
                showToast('success', response.message);
            } else {
                showToast('error', response.message);
                $(element).prop('checked', !status);
            }
        },
        error: function() {
            showToast('error', 'Failed to update status');
            $(element).prop('checked', !status);
        }
    });
}

function showToast(type, message) {
    if (typeof Snackbar !== 'undefined') {
        Snackbar.show({
            text: message,
            pos: 'bottom-right',
            backgroundColor: type === 'success' ? '#3ac47d' : '#d92550',
            actionTextColor: '#fff'
        });
    }
}
</script>
