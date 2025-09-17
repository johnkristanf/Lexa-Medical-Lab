<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, computed, onMounted } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import AddButton from '@/Components/AddButton.vue'

    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'
    import DangerButton from '@/Components/DangerButton.vue'

    const props = defineProps({
        supplies: Array,
        inventory_logs: Array,
        supplyUpdate: Object,
        categories: Array,
    })

    onMounted(() => {
        console.log('supplies: ', props.supplies)
    })

    // const search = ref('')

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

    // const filteredSupplies = computed(() => {
    //     if (!search.value) {
    //         return props.supplies
    //     }

    //     return props.supplies.filter(item => {
    //         const itemName = item.participants?.toLowerCase() || ''
    //         const brand = item.brand_name?.toLowerCase() || ''
    //         const unit = item.unit?.toLowerCase() || ''
    //         const quantity = String(item.quantity || '').toLowerCase()
    //         const manufacture = item.manufacture_date?.toLowerCase() || ''
    //         const expiration = item.expiration_date?.toLowerCase() || ''
    //         const lot = item.lot_number?.toLowerCase() || ''

    //         return (
    //             itemName.includes(search.value.toLowerCase()) ||
    //             brand.includes(search.value.toLowerCase()) ||
    //             unit.includes(search.value.toLowerCase()) ||
    //             quantity.includes(search.value.toLowerCase()) ||
    //             manufacture.includes(search.value.toLowerCase()) ||
    //             expiration.includes(search.value.toLowerCase()) ||
    //             lot.includes(search.value.toLowerCase())
    //         )
    //     })
    // })

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const inventoryTableHeaders = [
        'Category',
        'Description',
        'Brand Name',
        'Unit',
        'Supplies Left',
        'Manufacturing Date',
        'Expiration Date',
        'Lot #',
        'Action',
    ]
</script>

<template>
    <Head title="Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Supply Inventory
            </h2>
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
                        <a
                            :href="route('inventory.print')"
                            target="_blank"
                            class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700"
                        >
                            Print As PDF
                        </a>

                        <AddButton @click="toggles.showAddSupplyModal = true">Add Supply</AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput
                            route="inventory.supplies"
                            placeholder="Search Description, Brand, Lot #"
                        />
                    </div>

                    <fwb-table
                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    >
                        <fwb-table-head>
                            <fwb-table-head-cell
                                v-for="(header, index) in inventoryTableHeaders"
                                :key="index"
                                class="px-4 py-2 text-sm font-semibold tracking-wide uppercase bg-green-600 text-white"
                            >
                                {{ header }}
                            </fwb-table-head-cell>
                        </fwb-table-head>

                        <!-- Table Body -->
                        <fwb-table-body>
                            <!-- If supplies is not empty -->
                            <template v-if="supplies && supplies.length > 0">
                                <fwb-table-row v-for="supply in supplies" :key="supply.id">
                                    <fwb-table-cell>{{ supply.category.name }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.participants }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.brand_name }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.unit }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.quantity }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.manufacture_date }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.expiration_date }}</fwb-table-cell>
                                    <fwb-table-cell>{{ supply.lot_number }}</fwb-table-cell>

                                    <!-- Actions -->
                                    <fwb-table-cell>
                                        <div class="flex gap-2">
                                            <a
                                                :href="
                                                    route('inventory.supply.batches', {
                                                        id: supply.id,
                                                    })
                                                "
                                                title="View Batch"
                                                class="bg-[#70e000] px-3 py-1 rounded text-white hover:bg-[#1b4332]"
                                            >
                                                <i class="pi pi-eye text-white text-lg"></i>
                                            </a>
                                            <button
                                                @click="openUpdateSupply(supply)"
                                                title="Update Supply"
                                                class="bg-[#70e000] px-3 h-[28px] rounded text-white hover:bg-[#1b4332]"
                                            >
                                                <i class="pi pi-th-large text-white text-lg"></i>
                                            </button>
                                        </div>
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>

                            <!-- If no records -->
                            <template v-else>
                                <fwb-table-row>
                                    <fwb-table-cell
                                        colspan="5"
                                        class="text-center text-gray-500 py-4"
                                    >
                                        No supplies found
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>
                        </fwb-table-body>
                    </fwb-table>
                </div>
            </div>
        </div>

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
                                'bg-green-100 text-green-800':
                                    log.operation_type === OPERATION_TYPES.ADDED,
                                'bg-red-100 text-yellow-800':
                                    log.operation_type === OPERATION_TYPES.DEDUCTED,
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
