<template>
  <div class="offcanvas-footer border-top">
    <div class="d-grid d-md-flex gap-3 p-3">
      <!-- Save Button -->
      <button :disabled="newValue" class="btn btn-primary" name="submit">
        <template v-if="newValue">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
        </template>
        <template v-else> <i class="fa-solid fa-floppy-disk"></i> {{ $t('messages.save') }} </template>
      </button>

      <!-- Close Button -->
      <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="offcanvas"><i class="fa-solid fa-angles-left"></i> {{ $t('messages.close') }}</button>
    </div>
  </div>
</template>

<script setup>
import { watch, ref } from 'vue'

const props = defineProps({
  IS_SUBMITED: {
    type: Number
  }
})
const newValue = ref(props.IS_SUBMITED)

watch(
  () => props.IS_SUBMITED,
  (newVal) => {
    newValue.value = newVal
    console.log(newVal)
  }
)
</script>
