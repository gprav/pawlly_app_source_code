
<div class="d-flex gap-3 align-items-center">
  <img src="{{ $data->services->first()->employee->profile_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ $data->services->first()->employee->full_name ?? '-' }}</h6>
    <span>{{ $data->services->first()->employee->email ?? '--' }}</span>
  </div>
</div>