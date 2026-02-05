<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Drawer } from 'primevue'
    import { reactive, ref, watch } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import StockModal from '@/Components/modal/StockModal.vue'
    import {
        FwbTable,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableBody,
        FwbTableRow,
        FwbTableCell,
    } from 'flowbite-vue'

    const props = defineProps({
        supplies: Object,
        inventory_logs: Array,
        supplyUpdate: Object,
        addStock: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    const perPage = ref(props.supplies.per_page || 10)

    watch(perPage, (value) => {
        router.get(
            route('medical.stock.create'),
            {
                ...props.filters,
                perPage: value,
                page: 1,
            },
            { preserveState: true, replace: true },
        )
    })

    const showStockModal = ref(false)
    const addStock = ref(null)

    const openStockModal = (stock) => {
        addStock.value = stock
        showStockModal.value = true
    }

    const tableHeaders = [
        { key: 'brand_name', label: 'Brand Name' },
        { key: 'quantity', label: 'Stock' },
        { key: 'critical_stock', label: 'Critical Stock', custom: true },
        { key: 'batch_number', label: 'Product Batch #', custom: true },
        { key: 'action', label: 'Action', custom: true },
    ]
</script>

<template>
    <Head title="Stock" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supply Stock</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <SearchInput
                            route="medical.stock.create"
                            placeholder="Search Supplies"
                            v-model="search"
                        />
                    </div>

                    <!-- TABLE -->
                    <FwbTable class="w-full min-w-[50rem]">
                        <FwbTableHead>
                            <FwbTableHeadCell
                                v-for="(header, index) in tableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                            >
                                {{ header.label }}
                            </FwbTableHeadCell>
                        </FwbTableHead>

                        <FwbTableBody>
                            <FwbTableRow v-for="supply in supplies.data" :key="supply.id">
                                <FwbTableCell v-for="(header, index) in tableHeaders" :key="index">
                                    <!-- Normal fields -->
                                    <template v-if="!header.custom">
                                        {{ supply[header.key] }}
                                    </template>

                                    <!-- Critical Stock -->
                                    <template v-else-if="header.key === 'critical_stock'">
                                        {{ supply.stocks?.[0]?.critical_stock ?? 'N/A' }}
                                    </template>

                                    <!-- Batch Number -->
                                    <template v-else-if="header.key === 'batch_number'">
                                        {{ supply.batches?.[0]?.batch_number ?? 'N/A' }}
                                    </template>

                                    <!-- Action -->
                                    <template v-else-if="header.key === 'action'">
                                        <button
                                            @click="openStockModal(supply)"
                                            title="Update Supply"
                                            class="bg-[#70e000] px-3 h-[28px] ml-[8px] rounded text-white hover:bg-[#1b4332]"
                                        >
                                            <i class="pi pi-th-large text-white text-lg"></i>
                                        </button>
                                    </template>
                                </FwbTableCell>
                            </FwbTableRow>
                        </FwbTableBody>
                    </FwbTable>

                    <!-- PAGINATION -->
                    <div
                        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4"
                    >
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

                            <!-- Page Navigation -->
                            <div>
                                <nav
                                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                    aria-label="Pagination"
                                >
                                    <!-- Previous -->
                                    <button
                                        :disabled="!supplies.prev_page_url"
                                        @click="router.get(supplies.prev_page_url, { perPage, search })"
                                        class="relative inline-flex items-center gap-1 rounded-l-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                        :class="
                                            supplies.prev_page_url
                                                ? 'text-gray-700 hover:bg-gray-50'
                                                : 'text-gray-400 cursor-not-allowed opacity-50'
                                        "
                                    >
                                        Previous
                                    </button>

                                    <!-- Page Numbers -->
                                    <button
                                        v-for="link in supplies.links.slice(1, -1).slice(0, 5)"
                                        :key="link.label"
                                        @click="link.url && router.get(link.url, { perPage, search })"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300',
                                            link.active
                                                ? 'z-10 bg-green-600 text-white'
                                                : 'text-gray-900 hover:bg-gray-50',
                                        ]"
                                    >
                                        {{ link.label }}
                                    </button>

                                    <!-- Next -->
                                    <button
                                        :disabled="!supplies.next_page_url"
                                        @click="router.get(supplies.next_page_url, { perPage, search })"
                                        class="relative inline-flex items-center gap-1 rounded-r-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                        :class="
                                            supplies.next_page_url
                                                ? 'text-gray-700 hover:bg-gray-50'
                                                : 'text-gray-400 cursor-not-allowed opacity-50'
                                        "
                                    >
                                        Next
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALS -->
        <StockModal v-if="showStockModal" :addStock="addStock" @close="showStockModal = false" />
        <AddSupplyModal v-if="toggles.showAddSupplyModal" @close="toggles.showAddSupplyModal = false" />

        <!-- Drawer -->
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
