<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle title="App Configuration Settings" icon="fa-solid fa-sliders"></CardTitle>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-social_login">{{ $t('setting_integration_page.lbl_social_login') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="social_login" :checked="social_login == 1 ? true : false" name="social_login" id="category-social_login" type="checkbox" v-model="social_login" />
        </div>
      </div>
    </div>
    <div v-if="social_login == 1">
      <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label" for="category-google_login">{{ $t('setting_integration_page.lbl_google_login') }} </label>
          <div class="form-check form-switch">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="google_login" :checked="google_login == 1 ? true : false" name="google_login" id="category-google_login" type="checkbox" v-model="google_login" />
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label" for="category-apple_login">{{ $t('setting_integration_page.lbl_apple_login') }} </label>
          <div class="form-check form-switch">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="apple_login" :checked="apple_login == 1 ? true : false" name="apple_login" id="category-apple_login" type="checkbox" v-model="apple_login" />
          </div>
        </div>
      </div>
      <!-- <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label" for="category-otp_login">{{ $t('setting_integration_page.lbl_otp_login') }} </label>
          <div class="form-check form-switch">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="otp_login" :checked="otp_login == 1 ? true : false" name="otp_login" id="category-otp_login" type="checkbox" v-model="otp_login" />
          </div>
        </div>
      </div> -->
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-enable_event">{{ $t('setting_integration_page.lbl_enable_event') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_event" :checked="is_event == 1 ? true : false" name="is_event" id="category-is_event" type="checkbox" v-model="is_event" />
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-enable_blog">{{ $t('setting_integration_page.lbl_enable_blog') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_blog" :checked="is_blog == 1 ? true : false" name="is_blog" id="category-is_blog" type="checkbox" v-model="is_blog" />
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-enable_user_push_notification">{{ $t('setting_integration_page.lbl_enable_user_push_notification') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_user_push_notification" :checked="is_user_push_notification == 1 ? true : false" name="is_user_push_notification" id="category-is_user_push_notification" type="checkbox" v-model="is_user_push_notification" />
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-enable_provider_push_notification">{{ $t('setting_integration_page.lbl_enable_provider_push_notification') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_provider_push_notification" :checked="is_provider_push_notification == 1 ? true : false" name="is_provider_push_notification" id="category-is_provider_push_notification" type="checkbox" v-model="is_provider_push_notification" />
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-enable_chat_gpt">{{ $t('setting_integration_page.lbl_enable_chat_gpt') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="enable_chat_gpt" :checked="enable_chat_gpt == 1 ? true : false" name="enable_chat_gpt" id="category-enable_chat_gpt" type="checkbox" v-model="enable_chat_gpt" />
        </div>
      </div>
    </div>
    <div v-if="enable_chat_gpt == 1">
      <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label" for="category-test_without_key">{{ $t('setting_integration_page.lbl_test_without_key') }} </label>
          <div class="form-check form-switch">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="test_without_key" :checked="test_without_key == 1 ? true : false" name="test_without_key" id="category-test_without_key" type="checkbox" v-model="test_without_key" />
          </div>
        </div>
      </div>
      <div v-if="test_without_key == 0">
        <div class="form-group">
          <label for="category-chatgpt_key">{{ $t('setting_integration_page.key') }}</label>
          <input type="text" class="form-control" v-model="chatgpt_key" id="chatgpt_key" name="chatgpt_key" :errorMessage="errors.chatgpt_key" :errorMessages="errorMessages.chatgpt_key" />
          <p class="text-danger" v-for="msg in errorMessages.chatgpt_key" :key="msg">{{ msg }}</p>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-firebase_notification">{{ $t('setting_integration_page.lbl_firebase_notification') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="firebase_notification" :checked="firebase_notification == 1 ? true : false" name="firebase_notification" id="category-firebase_notification" type="checkbox" v-model="firebase_notification" />
        </div>
      </div>
    </div>

    <div v-if="firebase_notification == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group mb-0">
            <label class="form-label" for="firebase_project_id">{{ $t('setting_integration_page.firebase_project_id') }}</label>
            <input type="text" class="form-control" v-model="firebase_project_id" placeholder="Firebase Project ID" id="firebase_project_id" name="firebase_project_id" :errorMessage="errors.firebase_project_id" :errorMessages="errorMessages.firebase_project_id" />
            <p class="text-danger" v-for="msg in errorMessages.firebase_project_id" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.firebase_project_id }}</span>
          </div>
        </div>
        <div class="form-group col-sm-6 mb-0">
          <label for="json_file" class="form-control-label">
            {{ $t('setting_integration_page.firebase_json_file') }}
            <span class="ml-3">
              <a class="text-primary" href="https://console.firebase.google.com/">Download JSON File</a>
            </span>
          </label>
          <div class="custom-file">
            <input type="file" class="form-control" id="json_file" name="json_file" ref="refInput" accept=".json" @change="fileUpload" />
            <div v-if="file_name !== undefined && file_name !== null && file_name !== ''">
              <label class="custom-file-label upload-label border-0" style="font-weight: bold">{{ file_name }}</label>
            </div>
            <div v-else>
              <label class="custom-file-label upload-label border-0">Upload Firebase JSON files only once.</label>
            </div>
            <small class="help-block with-errors text-danger"></small>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="form-group ml-3">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="notification" v-model="notification" id="firebase_notification" value="firebase_notification" :checked="notification == 'firebase_notification'" />
        <label class="form-check-label" for="firebase_notification"> {{ $t('setting_integration_page.lbl_firebase_notification') }} </label>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="firebase_project_id" class="form-label">{{ $t('setting_integration_page.firebase_project_id') }}</label>
          <input type="text" class="form-control" v-model="firebase_project_id" id="firebase_project_id" name="firebase_project_id" :errorMessage="errors.firebase_project_id" :errorMessages="errorMessages.firebase_project_id" />
          <p class="text-danger" v-for="msg in errorMessages.firebase_project_id" :key="msg">{{ msg }}</p>
        </div>
        <div class="col-md-6 mt-2 mt-md-0">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="json_file"
              >{{ $t('setting_integration_page.firebase_json_file') }} <span class="ml-3"><a class="text-primary" href="https://console.firebase.google.com/">Download JSON File</a></span></label
            >
          </div>
          <input type="file" class="form-control" id="json_file" name="json_file" ref="refInput" accept=".json" @change="fileUpload" />
        </div>
      </div> 
    </div>-->
      <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label" for="category-enable_multi_vender">{{ $t('setting_integration_page.lbl_enable_multi_vender') }} </label>
          <div class="form-check form-switch">
            <input class="form-check-input" :true-value="1" :false-value="0" :value="enable_multi_vendor" :checked="enable_multi_vendor == 1 ? true : false" name="enable_multi_vendor" id="category-enable_multi_vendor" type="checkbox" v-model="enable_multi_vendor" />
          </div>
        </div>
      </div>
    

    <!-- submitbtn -->
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>
<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)

const fileUpload = (e) => {
  let files = e.target.files
  json_file.value = files[0] // Assuming single file upload
}

const file_name = ref('')
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      is_event: data.is_event || 0,
      is_blog: data.is_blog || 0,
      is_user_push_notification: data.is_user_push_notification || 0,
      is_provider_push_notification: data.is_provider_push_notification || 0,
      enable_chat_gpt: data.enable_chat_gpt || 0,
      test_without_key: data.test_without_key || 0,
      chatgpt_key: data.chatgpt_key || '',
      notification: data.notification || '',
      // firebase_key:data.firebase_key||'',
      firebase_notification: data.firebase_notification || 0,
      firebase_project_id: data.firebase_project_id || '',
      enable_multi_vendor: data.enable_multi_vendor || 0,
      enable_new_petstore_login: data.enable_new_petstore_login || 0,
      json_file: data.json_file,
      social_login: data.social_login || 0,
      google_login: data.google_login || 0,
      apple_login: data.apple_login || 0,
      otp_login: data.otp_login || 0
    }
  })
}
//validation
const validationSchema = yup.object({
  // chatgpt_key: yup.string().test('chatgpt_key', 'Must be a valid ChatGPT key', function (value) {
  //   if (this.parent.test_without_key == '0' && !value) {
  //     return false
  //   }
  //   return true
  // }),

  // firebase_key: yup.string().test('firebase_key', 'Must be a valid Firebase API key', function (value) {
  //   if (this.parent.notification == 'firebase_notification' && !value) {
  //     return false;
  //   }
  //   return true
  // }),

  firebase_project_id: yup.string().test('firebase_project_id', 'Must be a valid Firebase API key', function (value) {
    if (this.parent.firebase_notification == '1' && !value) {
      return false
    }
    return true
  })
})
const { handleSubmit, errors, resetForm, validate } = useForm({ validationSchema })
const errorMessages = ref({})
const { value: is_event } = useField('is_event')
const { value: is_blog } = useField('is_blog')
const { value: is_user_push_notification } = useField('is_user_push_notification')
const { value: is_provider_push_notification } = useField('is_provider_push_notification')
const { value: enable_chat_gpt } = useField('enable_chat_gpt')
const { value: test_without_key } = useField('test_without_key')
const { value: chatgpt_key } = useField('chatgpt_key')
const { value: notification } = useField('notification')
// const { value: firebase_key } = useField('firebase_key')
const { value: firebase_notification } = useField('firebase_notification')
const { value: firebase_project_id } = useField('firebase_project_id')
const { value: enable_multi_vendor } = useField('enable_multi_vendor')
const { value: enable_new_petstore_login } = useField('enable_new_petstore_login')
const { value: json_file } = useField('json_file')
const { value: social_login } = useField('social_login')
const { value: google_login } = useField('google_login')
const { value: apple_login } = useField('apple_login')
const { value: otp_login } = useField('otp_login')

watch(
  () => test_without_key.value,
  (value) => {
    if (value == '1') {
      chatgpt_key.value = ''
    }
  },
  { deep: true }
)

watch(
  () => firebase_notification.value,
  (value) => {
    if (value == '0') {
      firebase_project_id.value = ''
    }
  },
  { deep: true }
)

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    if (res.all_message) {
      errorMessages.value = res.all_message
    } else {
      errorMessages.value = res.errors
    }
  }
}

//fetch data
const data = [
  'is_event',
  'is_blog',
  'is_user_push_notification',
  'is_provider_push_notification',
  'enable_chat_gpt',
  'test_without_key',
  'chatgpt_key',
  'notification',
  // 'firebase_key',
  'firebase_notification',
  'firebase_project_id',
  'enable_multi_vendor',
  'enable_new_petstore_login',
  'json_file',
  'social_login',
  'google_login',
  'apple_login',
  'otp_login'
]

onMounted(() => {
  const customData = [...data].join(',')

  createRequest(GET_URL(customData)).then((response) => {
    setFormData(response)
  })
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  console.log('hello')
  IS_SUBMITED.value = true
  const formData = new FormData()
  Object.keys(values).forEach((key) => {
    if (values[key] !== '') {
      formData[key] = values[key] || ''
    }
    if (key === 'enable_new_petstore_login') {
      formData[key] = values[key] !== '' ? parseInt(values[key]) : 0
    } else if (values[key] !== '') {
      formData[key] = values[key]
    }
  })
  if (json_file.value) {
    formData.append('json_file', json_file.value)
  }
  console.log(formData)
  storeRequest({
    url: STORE_URL,
    body: formData,
    type: 'file'
  }).then((res) => display_submit_message(res))
})

defineProps({
  label: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  errorMessage: { type: String, default: '' },
  errorMessages: { type: Array, default: () => [] }
})
</script>
