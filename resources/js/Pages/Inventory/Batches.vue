<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import BatchModal from '@/Components/modal/BatchModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'

    const props = defineProps({
        supplies: Array,
        inventory_logs: Array,
    })

    const toggles = reactive({
        showBatchModal: false,
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
                        <SearchInput />
                    </div>

                   <DataTable
                    :value="props.supplies"
                    tableStyle="min-width: 50rem"
                    class="custom-datatable"
                >
                    <Column field="brand_name" header="Brand Name"></Column>
                    <Column field="manufacture_date" header="Manufacturing Date"></Column>
                    <Column field="expiration_date" header="Expiration Date"></Column>
                    <Column field="batch_number" header="Product Batch #"></Column>
                    <Column header="Action">
                        <template #body>
                           <button title="Add Batch" @click="toggles.showBatchModal= true">
                           <i class="pi pi-objects-column text-white-600 text-lg"></i>
                             </button>
                        </template>
                    </Column>
                </DataTable>
                </div>
            </div>
        </div>

        <!-- ADD SUPPLY MODAL -->
        <BatchModal
            v-if="toggles.showBatchModal"
            @close="toggles.showBatchModal = false"
        />
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

