<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('service.lbl_name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>

        <div v-for="field in customefield" :key="field.id">
          <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
        </div>

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
            <label class="form-label" for="category-status">{{ $t('service.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
            </div>
          </div>
        </div>
      </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/servicefacility'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { readFile } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'
const IS_SUBMITED = ref(false)
// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] }
})
const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)
const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => res.status && setFormData(res.data))
  } else {
    setFormData(defaultData())
  }
})

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    description: '',
    status: 1
    // custom_fields_data: {}
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      status: data.status
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close

const reset_datatable_close_offcanvas = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
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

const numberRegex = /^\d+$/
// Validations
const validationSchema = yup.object({
  name: yup.string().required('Name is a required field'),
  description: yup
    .string()
    .nullable()
    .test('no-script-tags', 'The Description field cannot contain script tags.', function (value) {
      const scriptTagRegex = /<script\b[^>]*>(.*?)/is
      return !value || !scriptTagRegex.test(value)
    })
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: status } = useField('status')
// const { value: custom_fields_data } = useField('custom_fields_data')

const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
})

// Form Submit
const formSubmit = handleSubmit(async (values) => {
  const saveButton = document.getElementById('save-button')
  saveButton.disabled = true
  IS_SUBMITED.value = true
  values.custom_fields_data = JSON.stringify(values.custom_fields_data)

  try {
    if (currentId.value > 0) {
      await updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
    } else {
      await storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
      document.getElementById('feature_image').value = ''
    }
  } catch (error) {
    console.error('Error:', error)
  } finally {
    IS_SUBMITED.value = false
    saveButton.disabled = false
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>
