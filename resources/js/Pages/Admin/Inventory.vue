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
    import { reactive, ref, computed } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import DangerButton from '@/Components/DangerButton.vue'
    import AdminLayout from '@/Layouts/AdminLayout.vue'

    const props = defineProps({
        supplies: Array,
        inventory_logs: Array,
        supplyUpdate: Object,
        categories: Array,
    })

    const search = ref('')

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

    const filteredSupplies = computed(() => {
        if (!search.value) {
            return props.supplies
        }

        return props.supplies.filter((item) => {
            const itemName = item.participants?.toLowerCase() || ''
            const brand = item.brand_name?.toLowerCase() || ''
            const unit = item.unit?.toLowerCase() || ''
            const quantity = String(item.quantity || '').toLowerCase()
            const manufacture = item.manufacture_date?.toLowerCase() || ''
            const expiration = item.expiration_date?.toLowerCase() || ''
            const lot = item.lot_number?.toLowerCase() || ''

            return (
                itemName.includes(search.value.toLowerCase()) ||
                brand.includes(search.value.toLowerCase()) ||
                unit.includes(search.value.toLowerCase()) ||
                quantity.includes(search.value.toLowerCase()) ||
                manufacture.includes(search.value.toLowerCase()) ||
                expiration.includes(search.value.toLowerCase()) ||
                lot.includes(search.value.toLowerCase())
            )
        })
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

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supply Inventory</h2>
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

                        <DangerButton>
                            <a color="green" :href="route('inventory.print')" target="_blank">Print As PDF</a>
                        </DangerButton>

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
