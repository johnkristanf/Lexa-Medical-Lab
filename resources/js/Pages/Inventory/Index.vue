<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'

    const props = defineProps({
        supplies: Array,
        inventory_logs: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const sampleOperationType = 'added'
</script>

<template>
    <Head title="Dashboard" />

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
                        <fwb-button
                            class="bg-gray-900 hover:bg-gray-500"
                            @click="toggles.showInventoryDrawer = true"
                        >
                            View Logs
                        </fwb-button>

                        <fwb-button color="green" @click="toggles.showAddSupplyModal = true">
                            Add Supply
                        </fwb-button>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable
                        :value="props.supplies"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="participants" header="Participants"></Column>
                        <Column field="brand_name" header="Brand Name"></Column>
                        <Column field="unit" header="Unit"></Column>
                        <Column field="quantity" header="Supplies Left"></Column>
                        <Column field="manufacture_date" header="Manufacturing Date"></Column>
                        <Column field="expiration_date" header="Expiration Date"></Column>
                        <Column field="lot_number" header="Lot #"></Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->
        <AddSupplyModal
            v-if="toggles.showAddSupplyModal"
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

                <div v-for="log in props.inventory_logs" class="flex flex-col gap-4 border-2 border-gray-400 p-3 rounded-md">
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
