
<div class="d-flex gap-3 align-items-center">
  <img src="{{ $data->employee->profile_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ $data->employee->full_name ?? default_user_name()  }}</h6>
    <span>{{ $data->employee->email ?? '--' }}</span>
  </div>
</div>