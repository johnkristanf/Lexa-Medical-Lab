<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, computed } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
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
        supplies: Array,
        inventory_logs: Array,
        supplyUpdate: Object,
        addStock: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    const search = ref('')

    const showStockModal = ref(false)
    const addStock = ref(null)

    const openStockModal = (stock) => {
        console.log('Opening StockModal with:', stock)
        addStock.value = stock
        showStockModal.value = true
    }
    const filteredSupplies = computed(() => {
        if (!search.value) {
            return props.supplies
        }

        return props.supplies.filter((item) => {
            const brand = item.brand_name?.toLowerCase() || ''
            const quantity = String(item.quantity || '').toLowerCase()
            const critical = String(item.stocks?.[0]?.critical_stock || '').toLowerCase()
            const batch = item.batches?.[0]?.batch_number?.toLowerCase() || ''

            return (
                brand.includes(search.value.toLowerCase()) ||
                quantity.includes(search.value.toLowerCase()) ||
                critical.includes(search.value.toLowerCase()) ||
                batch.includes(search.value.toLowerCase())
            )
        })
    })

    const tableHeaders = [
        { key: 'brand_name', label: 'Brand Name' },
        { key: 'quantity', label: 'Stock' },
        { key: 'critical_stock', label: 'Critical Stock', custom: true },
        { key: 'batch_number', label: 'Product Batch #', custom: true },
        { key: 'action', label: 'Action', custom: true },
    ]

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const sampleOperationType = 'added'
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
                        <!-- <fwb-button
                            class="bg-gray-900 hover:bg-gray-500"
                            @click="toggles.showInventoryDrawer = true"
                        >
                            View Logs
                        </fwb-button> -->

                        <!-- <fwb-button color="green" @click="toggles.showAddSupplyModal = true">
                            Add Supply
                        </fwb-button> -->

                        <!-- SEARCH INPUT -->
                        <SearchInput v-model="search" />
                    </div>

                    <FwbTable class="w-full min-w-[50rem]">
                        <!-- Table Head -->
                        <FwbTableHead>
                            <FwbTableHeadCell
                                v-for="(header, index) in tableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                            >
                                {{ header.label }}
                            </FwbTableHeadCell>
                        </FwbTableHead>

                        <!-- Table Body -->
                        <FwbTableBody>
                            <FwbTableRow v-for="supply in filteredSupplies" :key="supply.id">
                                <!-- Loop cells -->
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
                </div>
            </div>
        </div>

        <StockModal v-if="showStockModal" :addStock="addStock" @close="showStockModal = false" />

        <!-- ADD SUPLY MODAL -->
        <AddSupplyModal v-if="toggles.showAddSupplyModal" @close="toggles.showAddSupplyModal = false" />

        <!-- DRAWER FOR INVENTORY LOGS -->
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
