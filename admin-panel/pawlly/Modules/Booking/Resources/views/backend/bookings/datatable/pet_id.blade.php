<div class="d-flex gap-3 align-items-center">
  <img src="{{ $data->pet->pet_image ?? default_feature_image() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ $data->pet->name ?? '-' }}</h6>

  </div>
</div>