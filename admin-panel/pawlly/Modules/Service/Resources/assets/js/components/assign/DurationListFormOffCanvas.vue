<template>
    <form @submit.prevent="formSubmit">
      <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="duration-list" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header border-bottom">
          <h5 class="offcanvas-title" id="form-offcanvasLabel">  
              <span>{{TypeName}}</span>
          </h5>
          <button type="button" class="btn-close" @click="reset_datatable_close_offcanvas()" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
  
        <div class="offcanvas-body">
  
          <div v-if="!IsLoading" class="form-group">
            <div class="d-grid">
              <div class="d-flex flex-column">
                <div class="mb-4">
  
                </div>
              </div>
              <div v-if="selectedTypes.length > 0" class="list-group list-group-flush">
                <div class="d-flex align-items-center flex-grow-1 gap-2 my-2">
                    <h6 class="flex-grow-1"> Duration</h6>
                    <h6 class="flex-grow-1"> Amount</h6>
                </div>
                <div v-for="(item, index) in selectedTypes" :key="item" class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center flex-grow-1 gap-2 my-2">
                    <div class="flex-grow-1"> {{ formatDuration(item.duration) }}</div>
                    <div class="flex-grow-1"> {{ CURRENCY_SYMBOL }} {{ item.amount }}</div>
                  </div>
                 
                </div>
              </div>
              <div v-else class="text-center">
                <p>Duration are not available</p>
            </div>
            </div>
          </div>
          <div v-else class="text-center"> Proccessing.... </div>
        </div>
      </div>
    </form>
  
  
    
  </template>
  <script setup>
  import { ref, onMounted } from 'vue'
  import { DURATIN_LIST } from '../../constant/servicetraining'

  import { useModuleId, useRequest,useOnOffcanvasHide,useOnOffcanvasShow} from '@/helpers/hooks/useCrudOpration'
  import { confirmSwal } from '@/helpers/utilities'
  import { useSelect } from '@/helpers/hooks/useSelect'
  import FormHeader from '@/vue/components/form-elements/FormHeader.vue'

  
  
  
  // Request
  const { deleteRequest, getRequest, updateRequest } = useRequest()
  
  const props=defineProps({
    type: { type: String, default: '' },
  })
  
  const role = () => {
    return window.auth_role[0]
  }
  
  const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)
  
  const IsLoading=ref(false)
  


  const selectedTypes = ref([])
  // Vue Form Select END

  // Form Values
  const durationList = ref([])
  
  const TypeName = ref(null)

  const formatDuration = (duration) => {
  const [hours, minutes] = duration.split(':').map(Number);
  let formattedDuration = '';

  if (hours > 0) {
    formattedDuration += `${hours} hr${hours > 1 ? 's' : ''}`;
  }

  if (minutes > 0) {
    if (formattedDuration) formattedDuration += ' ';
    formattedDuration += `${minutes} min${minutes > 1 ? 's' : ''}`;
  }

  return formattedDuration;
};
  
  const TypeId = useModuleId(() => {
    IsLoading.value=true;
  
    reset_datatable_close_offcanvas()
  
    getRequest({ url: DURATIN_LIST, id: TypeId.value }).then((res) => {
      if (res.status && res.data) {
        selectedTypes.value = res.data.duration
        durationList.value = res.data.duration.map((item) => item.id)
        TypeName.value = res.data.type + "'s Duration list"
        IsLoading.value=false;

      }
    })

  }, 'duration_list')
  

  
  const reset_datatable_close_offcanvas = () => {
        selectedTypes.value = []
        TypeName.value = ''
  }
 
  
  </script>
  