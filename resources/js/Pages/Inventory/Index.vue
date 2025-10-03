<script setup>
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, watch } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import DangerButton from '@/Components/DangerButton.vue'
    import AddButton from '@/Components/AddButton.vue'

    const props = defineProps({
        supplies: Object,
        inventory_logs: Array,
        supplyUpdate: Object,
        categories: Array,
    })

    const perPage = ref(props.supplies.perPage || 10)

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

    // Watch perPage and reload data
    watch(perPage, (value) => {
        router.get(
            route('inventory.supplies'),
            {
                ...props.filters,
                perPage: value,
                page: 1, // reset to first page whenever perPage changes
            },
            { preserveState: true, replace: true },
        )
    })

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const invetoryTableHeaders = [
        'Item',
        'Brand Name',
        'Unit',
        'Supplies Left',
        'Manufacturing Date',
        'Expiration Date',
        'Lot #',
    ]
</script>

<template>
    <Head title="Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supply Inventory</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <DangerButton>
                            <a color="green" :href="route('inventory.print')" target="_blank">Print As PDF</a>
                        </DangerButton>

                        <AddButton color="green" @click="toggles.showAddSupplyModal = true">
                            Add Supply
                        </AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput route="inventory.supplies" placeholder="Search Supplies" />
                    </div>

                    <fwb-table>
                        <fwb-table-head>
                            <fwb-table-head-cell
                                v-for="header in invetoryTableHeaders"
                                :key="header"
                                class="bg-green-600 text-white"
                            >
                                {{ header }}
                            </fwb-table-head-cell>
                        </fwb-table-head>

                        <fwb-table-body>
                            <fwb-table-row v-for="supply in supplies.data" :key="supply.id">
                                <fwb-table-cell>{{ supply.participants }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.brand_name }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.unit }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.quantity }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.manufacture_date }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.expiration_date }}</fwb-table-cell>
                                <fwb-table-cell class="!text-left">{{ supply.lot_number }}</fwb-table-cell>
                            </fwb-table-row>
                        </fwb-table-body>
                    </fwb-table>

                    <!-- PAGINATION -->
                    <div
                        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4"
                    >
                        <div class="flex flex-1 justify-between sm:hidden">
                            <button
                                :disabled="!supplies.prev_page_url"
                                :class="[
                                    'relative inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium',
                                    supplies.prev_page_url
                                        ? 'bg-white text-gray-700 hover:bg-gray-50'
                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed',
                                ]"
                            >
                                Previous
                            </button>
                            <button
                                :disabled="!supplies.next_page_url"
                                :class="[
                                    'relative ml-3 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium',
                                    supplies.next_page_url
                                        ? 'bg-white text-gray-700 hover:bg-gray-50'
                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed',
                                ]"
                            >
                                Next
                            </button>
                        </div>
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ supplies.from }}</span>
                                    to
                                    <span class="font-medium">{{ supplies.to }}</span>
                                    of
                                    <span class="font-medium">{{ supplies.total }}</span>
                                    results
                                </p>

                                <!-- Per Page Dropdown -->
                                <div class="flex items-center gap-2">
                                    <label for="perPage" class="text-sm text-gray-700">Per page:</label>
                                    <select
                                        id="perPage"
                                        v-model="perPage"
                                        class="rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500"
                                    >
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <nav
                                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                    aria-label="Pagination"
                                >
                                    <button
                                        :disabled="!supplies.prev_page_url"
                                        @click="router.get(supplies.prev_page_url, { perPage })"
                                        :class="[
                                            'relative inline-flex items-center gap-1 rounded-l-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300',
                                            supplies.prev_page_url
                                                ? 'text-gray-700 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                                                : 'text-gray-400 cursor-not-allowed opacity-50',
                                        ]"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <span>Previous</span>
                                    </button>

                                    <button
                                        v-for="link in supplies.links.slice(1, -1).slice(0, 5)"
                                        @click="link.url && router.get(link.url, { perPage })"
                                        :key="link.label"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300',
                                            link.active
                                                ? 'z-10 bg-green-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600'
                                                : 'text-gray-900 hover:bg-gray-50 focus:z-20 focus:outline-offset-0',
                                        ]"
                                    >
                                        {{ link.label }}
                                    </button>

                                    <button
                                        :disabled="!supplies.next_page_url"
                                        @click="router.get(supplies.next_page_url, { perPage })"
                                        :class="[
                                            'relative inline-flex items-center gap-1 rounded-r-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300',
                                            supplies.next_page_url
                                                ? 'text-gray-700 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                                                : 'text-gray-400 cursor-not-allowed opacity-50',
                                        ]"
                                    >
                                        <span>Next</span>
                                        <svg
                                            class="h-4 w-4"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALS -->
        <UpdateSupply
            v-if="showUpdateSupply"
            :supplyUpdate="supplyUpdate"
            @close="showUpdateSupply = false"
        />

        <!-- ADD SUPLY MODAL -->
        <AddSupplyModal
            v-if="toggles.showAddSupplyModal"
            :categories="categories"
            @close="toggles.showAddSupplyModal = false"
        />

        <!-- INVENTORY LOGS -->
        <Drawer
            v-model:visible="toggles.showInventoryDrawer"
            header="Inventory Logs"
            position="right"
            class="!w-full sm:!w-80 lg:!w-[25rem]"
        >
            <div class="flex flex-col gap-3">
                <div
                    v-for="log in props.inventory_logs"
                    :key="log.id"
                    class="flex flex-col gap-4 border-2 border-gray-400 p-3 rounded-md"
                >
                    <h1>
                        Brand Name:
                        <br />
                        - {{ log.medical_supplies.brand_name }}
                    </h1>
                    <h1>
                        Current Quantity:
                        <br />
                        - {{ log.medical_supplies.quantity }}
                    </h1>

                    <div class="flex flex-col gap-2">
                        <h1>Operation Type:</h1>
                        <span
                            class="w-1/2 text-center inline-block px-2 py-1 text-sm font-bold uppercase rounded-md"
                            :class="{
                                'bg-green-100 text-green-800': log.operation_type === OPERATION_TYPES.ADDED,
                                'bg-red-100 text-yellow-800': log.operation_type === OPERATION_TYPES.DEDUCTED,
                            }"
                        >
                            {{ log.operation_type }}
                        </span>
                    </div>

                    <h1>
                        Total Quantity {{ log.operation_type.toUpperCase() }}:
                        <br />
                        {{ log.total_quantity }}
                    </h1>
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
