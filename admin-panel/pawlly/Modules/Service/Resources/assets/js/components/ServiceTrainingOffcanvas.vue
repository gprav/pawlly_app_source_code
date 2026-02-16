<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('service.lbl_name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>
        <div class="form-group col-md-12">
          <label class="form-label" for="description">{{ $t('service.lbl_description') }}</label>
          <textarea class="form-control" v-model="description" id="description"></textarea>
          <span v-if="errorMessages['description']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.description }}</span>
        </div>
        <div class="form-group">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="category-status">{{ $t('pet.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="pet-status" type="checkbox" v-model="status" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
              <h6 class="m-0">{{ $t('service.add') }} {{ $t('service.lbl_duration') }} <span class="text-danger">*</span></h6>
            </div>
            <div class="d-flex align-items-center gap-3">
              <a @click="addDuration" class="btn btn-sm text-primary border-0 px-0 float-end">Add More</a>
            </div>
          </div>
          <div v-for="(duration, index) in durations" :key="index" :class="{ 'border-bottom pb-3 mb-3': index !== durations.length - 1 }" class="col-md-12 pt-0">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label" :for="'duration-' + index">{{ $t('service.lbl_duration') }} <span class="text-danger">*</span></label>

                  <!-- Single flat-pickr for both hours and minutes -->
                  <flat-pickr :placeholder="$t('service.lbl_duration')" :id="'duration-' + index" class="form-control" v-model="duration.duration" :config="config_duration" @change="updateDuration(index)"></flat-pickr>
                  <span class="text-danger">{{ duration.errors.duration }}</span>
                </div>
              </div>
              <InputField :id="'amount-' + index" class="col-md-12" type="text" :is-required="true" :label="$t('service.lbl_price')" placeholder="" v-model="duration.amount" :error-message="duration.errors.amount"></InputField>
              <!-- Add a status checkbox -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" :id="'status-' + index" v-model="duration.status" :true-value="1" :false-value="0" />
                      <label class="form-check-label" :for="'status-' + index">
                        {{ $t('service.lbl_status') }}
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div v-if="durations.length > 1" class="form-group text-end">
                    <button type="button" @click="removeDuration(index)" class="btn btn-danger px-3"><i class="fa-regular fa-trash-can"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <span v-if="duplicateDurationMessage" class="text-danger">
            {{ duplicateDurationMessage }}
          </span>
        </div>
      </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/servicetraining'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest, useOnOffcanvasHide, useOnOffcanvasShow } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
const IS_SUBMITED = ref(false)
// Props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

// Requests
const { getRequest, storeRequest, updateRequest } = useRequest()

// Configuration for time pickers
const config_hours = {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H',
  time_24hr: true,
  defaultHour: 0,
  defaultMinute: 0,
  allowInput: true
}
const config_minutes = {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'i',
  time_24hr: true,
  defaultHour: 0,
  defaultMinute: 0,
  allowInput: true
}
const config_duration = {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'h:i K',
  time_24hr: false,
  enableSeconds: false, // Optional: Disables seconds if you don't need them
  defaultHour: '00', // Update default hour to 9
  defaultMinute: '00',
  static: true
}

const defaultDuration = { duration: '00:30', hours: '00', minutes: '30', amount: '', status: 1, errors: {} }

const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status && res.data) {
        durations.value =
          res.data.training_duration && res.data.training_duration.length > 0
            ? res.data.training_duration.map((d) => {
                const [hours, minutes] = d.duration.split(':')
                return {
                  duration: d.duration,
                  hours,
                  minutes,
                  amount: d.amount,
                  status: d.status,
                  errors: {}
                }
              })
            : [defaultDuration]
        setFormData(res.data)
      }
    })
  } else {
    durations.value = [defaultDuration]
    setFormData(defaultData())
  }
})

// Reactive state
const durations = ref([defaultDuration])
const errorMessages = ref({})

// Validation schema
const validationSchema = yup.object({
  name: yup.string().required('Name is a required field'),
  description: yup
    .string()
    .nullable()
    .test('no-script-tags', 'The Description field cannot contain script tags.', function (value) {
      const scriptTagRegex = /<script\b[^>]*>(.*?)/is
      return !scriptTagRegex.test(value)
    })
})

// Form handling
const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: status } = useField('status')
const duplicateDurationMessage = ref('')
// Form submit handler
const formSubmit = handleSubmit((values) => {
  let isValid = true
  IS_SUBMITED.value = true
  const lastDuration = durations.value[durations.value.length - 1]
  const isPositiveInteger = !isNaN(Number(lastDuration.amount)) && Number(lastDuration.amount) > 0

  // const isDuplicate = durations.value.some((dur, index) => {
  //   return index !== durations.value.length - 1 && dur.hours === lastDuration.hours && dur.minutes === lastDuration.minutes
  // })

  const isDuplicate = durations.value.some((dur, index) => {
    return index !== durations.value.length - 1 && dur.duration === lastDuration.duration
  })
  // if (!lastDuration.hours) {
  //   lastDuration.errors.hours = 'Hours is required'
  //   isValid = false
  // }
  if (!lastDuration.duration) lastDuration.errors.hours = 'Duration is required'
  // if (!lastDuration.minutes) {
  //   lastDuration.errors.minutes = 'Minutes is required'
  //   isValid = false
  // }
  if (!lastDuration.amount) {
    lastDuration.errors.amount = 'Price is required'
    isValid = false
    IS_SUBMITED.value = false
  } else if (!isPositiveInteger) {
    lastDuration.errors.amount = 'Price cannot be negative or zero.'
    isValid = false
    IS_SUBMITED.value = false
  }
  if (isDuplicate) {
    duplicateDurationMessage.value = 'This duration is already present. Please update the price instead.'
    isValid = false
    IS_SUBMITED.value = false
  }

  if (!isValid) {
    return
  }

  const saveButton = document.getElementById('save-button')
  saveButton.disabled = true
  // Serialize durations array
  values.durations = durations.value.map((duration) => ({
    duration: duration.duration,
    hours: duration.hours,
    minutes: duration.minutes,
    amount: duration.amount,
    status: duration.status
  }))

  const durationSet = new Set()
  let hasDuplicates = false
  for (const duration of values.durations) {
    const key = `${duration.duration}`
    if (durationSet.has(key)) {
      hasDuplicates = true
      break
    }
    durationSet.add(key)
  }

  if (hasDuplicates) {
    duplicateDurationMessage.value = 'This duration is already present. Please update the price instead.'
    IS_SUBMITED.value = false
    return
  } else {
    duplicateDurationMessage.value = '' // Clear duplicate message on successful submit
    IS_SUBMITED.value = false
  }

  // Convert durations to JSON string for backend
  values.durations = JSON.stringify(values.durations)

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

// Methods
const addDuration = () => {
  const lastDuration = durations.value[durations.value.length - 1]
  const isPositiveInteger = !isNaN(Number(lastDuration.amount)) && Number(lastDuration.amount) > 0

  // Check for duplicate hours and minutes
  const isDuplicate = durations.value.some((dur, index) => {
    return index !== durations.value.length - 1 && dur.duration === lastDuration.duration
  })

  if (!isDuplicate && lastDuration.duration && isPositiveInteger) {
    durations.value.push({ duration: '', hours: '00', minutes: '30', amount: '', status: 1, errors: {} })
    duplicateDurationMessage.value = '' // Clear duplicate message on successful add
  } else {
    if (isDuplicate) {
      duplicateDurationMessage.value = 'This duration is already present. Please update the price instead.'
    }
    // if (!lastDuration.hours) lastDuration.errors.hours = 'Hours is required'
    // if (!lastDuration.minutes) lastDuration.errors.minutes = 'Minutes is required'
    if (!lastDuration.duration) lastDuration.errors.hours = 'Duration is required'
    if (!lastDuration.amount) {
      lastDuration.errors.amount = 'Price is required'
    } else if (!isPositiveInteger) {
      lastDuration.errors.amount = 'Price cannot be negative or zero.'
    }
  }
}

const removeDuration = (index) => {
  if (durations.value.length > 1) {
    durations.value.splice(index, 1)
  }
}
const toggleStatus = (index) => {
  durations.value[index].status = durations.value[index].status ? 1 : 0
}

// Method to update hours and minutes when duration changes
const updateDuration = (index) => {
  const durationValue = durations.value[index].duration

  if (durationValue) {
    const [hours, minutes] = durationValue.split(':').map((item) => parseInt(item, 10))
    console.log(durations)
    // Update the duration object with the split values
    durations.value[index].hours = hours || 0 // Default to 0 if invalid
    durations.value[index].minutes = minutes || 0 // Default to 0 if invalid
  }
}
// Watcher for dynamic validation of amount
watch(
  durations,
  (newDurations) => {
    console.log(newDurations)

    newDurations.forEach((duration, index) => {
      watch(
        () => duration.duration,
        (newDuration) => {
          if (newDuration) {
            duration.errors.duration = ''
          }
        }
      )
      // watch(
      //   () => duration.hours,
      //   (newHours) => {
      //     if (newHours) {
      //       duration.errors.hours = ''
      //     }
      //   }
      // )
      // watch(
      //   () => duration.minutes,
      //   (newMinutes) => {
      //     if (newMinutes) {
      //       duration.errors.minutes = ''
      //     }
      //   }
      // )
      watch(
        () => duration.amount,
        (newAmount) => {
          const isPositiveDecimal = !isNaN(Number(newAmount)) && Number(newAmount) > 0
          if (newAmount && isPositiveDecimal) {
            duration.errors.amount = ''
          } else if (!isPositiveDecimal) {
            duration.errors.amount = 'Price must be a positive number.'
            IS_SUBMITED.value = false
          }
        }
      )
    })
  },
  { deep: true }
)
// On mounted
onMounted(() => {
  if (currentId.value <= 0) {
    setFormData(defaultData())
  }
})

// Helper methods
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    description: '',
    status: 1,
    durations: [defaultDuration]
  }
}

const setFormData = (data) => {
  if (!data) {
    data = defaultData()
  }
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      status: data.status,
      durations: data.durations?.length ? data.durations : [defaultDuration]
    }
  })
}

// Reset datatable and close offcanvas
const reset_datatable_close_offcanvas = (res) => {
  const saveButton = document.getElementById('save-button')
  IS_SUBMITED.value = false
  saveButton.disabled = false
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    durations.value = [{ hours: '00', minutes: '30', amount: '', status: 1, errors: {} }]
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    if (res.all_message) {
      errorMessages.value = res.all_message
    } else {
      errorMessages.value = res.errors
    }
  }
}

useOnOffcanvasHide('form-offcanvas', () => {
  const saveButton = document.getElementById('save-button')
  saveButton.disabled = false
  durations.value = [{ hours: '00', minutes: '30', amount: '', status: 1, errors: {} }]
  setFormData(defaultData())
})
useOnOffcanvasShow('form-offcanvas', () => {
  durations.value = [{ hours: '00', minutes: '30', amount: '', status: 1, errors: {} }]
  setFormData(defaultData())
})
</script>
