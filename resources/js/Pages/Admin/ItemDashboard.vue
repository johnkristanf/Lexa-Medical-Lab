<script setup>
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
    FwbButton,
} from 'flowbite-vue'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { reactive } from 'vue'
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

                    <AdminLayout>
                        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl  text-gray-600">Dashboard</h1>
                </div>
                    <div class="flex flex-col lg:flex-row gap-6 w-[90%] mx-auto mt-[3%]">
                    <div class="flex-1 bg-[#ced4da] text-black p-6 rounded-lg shadow-lg">
                    <h2 class="text-center mb-6 text-2xl font-bold">Nearly Out of Stock</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                <th class="py-3 px-4 text-left">Product Name</th>
                <th class="py-3 px-4 text-left">Current Stock</th>
                <th class="py-3 px-4 text-left">Minimum Stock</th>
                <th class="py-3 px-4 text-left w-1/4">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <tr class="hover:bg-gray-100">
                <td class="py-2 px-4 break-words">Paracetamol 500mg</td>
                <td class="py-2 px-4">15</td>
                <td class="py-2 px-4">50</td>
                <td class="py-2 px-4">
                    <span class="bg-[#d90429] text-white px-2 py-1 rounded text-xs">Critical</span>
                </td>
                </tr>
                <tr class="hover:bg-gray-100">
                <td class="py-2 px-4 break-words">Ibuprofen 400mg</td>
                <td class="py-2 px-4">8</td>
                <td class="py-2 px-4">30</td>
                <td class="py-2 px-4">
                    <span class="bg-[#d90429] text-white px-2 py-1 rounded text-xs">Critical</span>
                </td>
                </tr>
                <tr class="hover:bg-gray-100">
                <td class="py-2 px-4 break-words">Amoxicillin 250mg</td>
                <td class="py-2 px-4">22</td>
                <td class="py-2 px-4">40</td>
                <td class="py-2 px-4">
                    <span class="bg-[#ffba08] text-black px-2 py-1 rounded text-xs">Low</span>
                </td>
                </tr>
            </tbody>
            </table>
        </div>
        </div>

            <div class="flex-1 bg-[#ced4da] text-black p-6 rounded-lg shadow-lg">
            <h2 class="text-center mb-6 text-2xl font-bold">Nearly Expired</h2>

    <!-- ✅ Scroll wrapper -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                <th class="py-3 px-4 text-left">Product Name</th>
                <th class="py-3 px-4 text-left">Expiration Date</th>
                <th class="py-3 px-4 text-left">Batch Number</th>
                <th class="py-3 px-4 text-left">Days Left</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <tr
                v-for="batch in props.nearlyExpired"
                :key="batch.id"
                class="hover:bg-gray-100"
                >
                <td class="py-2 px-4 break-words">
                    {{ batch.medical_supply?.brand_name ?? 'N/A' }}
                </td>
                <td class="py-2 px-4">{{ formatDate(batch.expiration_date) }}</td>
                <td class="py-2 px-4 whitespace-nowrap">
                    {{ batch.batch_number }}
                </td>
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
  </AdminLayout>
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
