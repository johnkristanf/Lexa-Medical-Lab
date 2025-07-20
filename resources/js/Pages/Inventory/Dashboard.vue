<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Column, DataTable, Drawer } from 'primevue'
import { FwbButton } from 'flowbite-vue'
import { reactive, ref } from 'vue'
import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
import SearchInput from '@/Components/SearchInput.vue'
import { OPERATION_TYPES } from '@/Enums/Inventory'
import ItemData from '@/Components/ItemData.vue'

const props = defineProps({
  supplies: Array,
  inventory_logs: Array,
  nearlyExpired: Array,
})

const toggles = reactive({
  showAddSupplyModal: false,
  showInventoryDrawer: false,
})

// Utility functions
const daysLeft = (expiration) => {
  const now = new Date()
  const exp = new Date(expiration)
  const diffTime = exp.getTime() - now.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 3600 * 24))
  return diffDays
}

const formatDate = (date) => {
  const d = new Date(date)
  return d.toLocaleDateString('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
  })
}
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard
      </h2>
    </template>

    <div class="flex flex-col lg:flex-row gap-6 w-[90%] mx-auto mt-[3%]">
      <!-- Nearly Out of Stock Container -->
      <div class="flex-1 bg-[#ced4da] text-black p-6 rounded-lg shadow-lg">
        <h2 class="text-center mb-6 text-2xl font-bold">Nearly Out of Stock</h2>

        <div class="overflow-x-auto">
          <table class="w-full text-center border-collapse">
            <thead>
              <tr>
                <th class="border-b border-white py-2">Product Name</th>
                <th class="border-b border-white py-2">Current Stock</th>
                <th class="border-b border-white py-2">Minimum Stock</th>
                <th class="border-b border-white py-2">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="py-2 px-4">Paracetamol 500mg</td>
                <td class="py-2 px-4">15</td>
                <td class="py-2 px-4">50</td>
                <td class="py-2 px-4">
                  <span class="bg-[#d90429] px-2 py-1 rounded text-xs">Critical</span>
                </td>
              </tr>
              <tr>
                <td class="py-2 px-4">Ibuprofen 400mg</td>
                <td class="py-2 px-4">8</td>
                <td class="py-2 px-4">30</td>
                <td class="py-2 px-4">
                  <span class="bg-[#d90429] px-2 py-1 rounded text-xs">Critical</span>
                </td>
              </tr>
              <tr>
                <td class="py-2 px-4">Amoxicillin 250mg</td>
                <td class="py-2 px-4">22</td>
                <td class="py-2 px-4">40</td>
                <td class="py-2 px-4">
                  <span class="bg-[#ffba08] px-2 py-1 rounded text-xs">Low</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Nearly Expired Container -->
      <div class="flex-1 bg-[#ced4da] text-black p-6 rounded-lg shadow-lg">
        <h2 class="text-center mb-6 text-2xl font-bold">Nearly Expired</h2>

        <div class="overflow-x-auto">
          <table class="w-full text-center border-collapse">
            <thead>
              <tr>
                <th class="border-b border-white py-2">Product Name</th>
                <th class="border-b border-white py-2">Expiration Date</th>
                <th class="border-b border-white py-2">Batch Number</th>
                <th class="border-b border-white py-2">Days Left</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="batch in props.nearlyExpired"
                :key="batch.id"
                :class="daysLeft(batch.expiration_date) <= 30 ? 'bg-[#ced4da]' : ''"
              >
                <td class="py-2 px-4">{{ batch.medical_supply?.brand_name ?? 'N/A' }}</td>
                <td class="py-2 px-4">{{ formatDate(batch.expiration_date) }}</td>
                <td class="py-2 px-4">{{ batch.batch_number }}</td>
                <td class="py-2 px-4">
                  <span
                    :class="{
                      'bg-[#d90429] text-white': daysLeft(batch.expiration_date) <= 30,
                      'bg-[#ffba08] text-black': daysLeft(batch.expiration_date) > 30,
                    }"
                    class="px-2 py-1 rounded text-xs"
                  >
                    {{ daysLeft(batch.expiration_date) }} days
                  </span>
                </td>
              </tr>
              <tr v-if="props.nearlyExpired.length === 0">
                <td colspan="4" class="py-2 text-center text-gray-600">
                  No nearly expired items
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <ItemData />
  </AuthenticatedLayout>
</template>



<style scoped>
.custom-datatable ::v-deep(.p-datatable-thead > tr > th) {
  background-color: #208b3a;
  color: white;
}

.custom-datatable ::v-deep(.p-datatable-tbody > tr > td) {
  background-color: #ffffff;
  color: #374151;
}
</style>
