<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { Column, DataTable, Drawer } from 'primevue'
import { FwbButton } from 'flowbite-vue'
import { reactive, ref, computed } from 'vue'
import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
import SearchInput from '@/Components/SearchInput.vue'
import { OPERATION_TYPES } from '@/Enums/Inventory'
import UpdateSupply from '@/Components/modal/UpdateSupply.vue'

const props = defineProps({
    supplies: Object,        // paginator
    inventory_logs: Array,
    supplyUpdate: Object,
    categories: Array,
    filters: Object
})

const search = ref(props.filters?.search ?? '')
const perPage = ref(props.filters?.perPage ?? 10)

// Table modals
const toggles = reactive({
    showAddSupplyModal: false,
    showInventoryDrawer: false,
})
const showUpdateSupply = ref(false)
const supplyUpdate = ref(null)

const openUpdateSupply = (supply) => {
    supplyUpdate.value = supply
    showUpdateSupply.value = true
}

// ✅ Pagination actions
function changePerPage() {
    router.get(route('inventory'), {
        perPage: perPage.value,
        search: search.value
    }, { preserveState: true })
}

function goToPage(url) {
    if (url) {
        router.get(url, { perPage: perPage.value, search: search.value }, { preserveState: true })
    }
}
</script>

<template>

    <Head title="Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Supply Inventory
            </h2>
        </template>

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="card p-8">
                <!-- TABLE FUNCTIONS -->
                <div class="w-full flex justify-end gap-3 mb-4">
                    <fwb-button color="green" :href="route('inventory.print')" target="_blank">
                        Print As PDF
                    </fwb-button>

                    <fwb-button color="green" @click="toggles.showAddSupplyModal = true">
                        Add Supply
                    </fwb-button>

                    <!-- SEARCH INPUT -->
                    <SearchInput v-model="search" @input="changePerPage" />
                </div>

                <!-- SUPPLIES TABLE -->
                <DataTable
                :value="props.supplies.data"
                tableStyle="min-width: 50rem"
                class="custom-datatable"
                >
                    <Column field="participants" header="Item"></Column>
                    <Column field="brand_name" header="Brand Name"></Column>
                    <Column field="unit" header="Unit"></Column>
                    <Column field="quantity" header="Supplies Left"></Column>
                    <Column field="manufacture_date" header="Manufacturing Date"></Column>
                    <Column field="expiration_date" header="Expiration Date"></Column>
                    <Column field="lot_number" header="Lot #"></Column>
                    <Column header="Action">
                        <template #body="slotProps">
                            <a :href="route('inventory.supply.batches', { id: slotProps.data.id })" title="View Batch"
                                class="bg-[#70e000] px-3 py-1 rounded text-white hover:bg-[#38b000]">
                                <i class="pi pi-eye text-white-600 text-lg"></i>
                            </a>

                            <button @click="openUpdateSupply(slotProps.data)" title="Update Supply"
                                class="bg-[#70e000] px-3 h-[28px] ml-[8px] rounded text-white hover:bg-[#38b000]">
                                <i class="pi pi-th-large text-white-600 text-lg"></i>
                            </button>
                        </template>
                    </Column>
                </DataTable>

                <!-- PAGINATION CONTROLS -->
                <div class="flex items-center justify-between mt-6">
                    <!-- Per Page Selector -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Show:</label>
                        <select v-model="perPage" @change="changePerPage"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-600">entries</span>
                    </div>

                    <!-- Pagination Info and Controls -->
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                            Showing {{ props.supplies.from }} to {{ props.supplies.to }} of {{ props.supplies.total }}
                            entries
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-1">
                            <button @click="goToPage(props.supplies.prev_page_url)"
                                :disabled="!props.supplies.prev_page_url" class="px-3 py-1 text-sm border rounded-md"
                                :class="!props.supplies.prev_page_url ? 'bg-[#465362]  hover:bg-[#7d8597] text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'">
                                Previous
                            </button>

                            <button @click="goToPage(props.supplies.next_page_url)"
                                :disabled="!props.supplies.next_page_url" class="px-3 py-1 text-sm border rounded-md"
                                :class="!props.supplies.next_page_url ? 'bg-[#465362] hover:bg-[#7d8597] text-gray-400 cursor-not-allowed' : 'bg-white'">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALS -->
        <UpdateSupply v-if="showUpdateSupply" :supplyUpdate="supplyUpdate" @close="showUpdateSupply = false" />

        <AddSupplyModal v-if="toggles.showAddSupplyModal" :categories="categories"
            @close="toggles.showAddSupplyModal = false" />


        <!-- INVENTORY LOGS -->
        <Drawer v-model:visible="toggles.showInventoryDrawer" header="Inventory Logs" position="right"
            class="!w-full sm:!w-80 lg:!w-[25rem]">
            <div class="flex flex-col gap-3">
                <div v-for="log in props.inventory_logs" :key="log.id"
                    class="flex flex-col gap-4 border-2 border-gray-400 p-3 rounded-md">
                    <h1>Brand Name:<br />- {{ log.medical_supplies.brand_name }}</h1>
                    <h1>Current Quantity:<br />- {{ log.medical_supplies.quantity }}</h1>

                    <div class="flex flex-col gap-2">
                        <h1>Operation Type:</h1>
                        <span class="w-1/2 text-center inline-block px-2 py-1 text-sm font-bold uppercase rounded-md"
                            :class="{
                                'bg-green-100 text-green-800': log.operation_type === OPERATION_TYPES.ADDED,
                                'bg-red-100 text-yellow-800': log.operation_type === OPERATION_TYPES.DEDUCTED,
                            }">
                            {{ log.operation_type }}
                        </span>
                    </div>

                    <h1>Total Quantity {{ log.operation_type.toUpperCase() }}:<br />{{ log.total_quantity }}</h1>
                </div>
            </div>
        </Drawer>
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
