<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer,} from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import SelectButton from 'primevue/selectbutton';
    import CategoryModal from '@/Components/modal/CategoryModal.vue'



    const props = defineProps({
        inventory_logs: Array,
        categories: Array
    })

    const toggles = reactive({
        showCategoryModal: false,
        showInventoryDrawer: false,
    })

    const showUpdateSupply = ref(false)
    const supplyUpdate = ref(null)

    const openUpdateSupply = (supply) => {
         supplyUpdate.value = supply
        showUpdateSupply.value = true
    }

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const sampleOperationType = 'added'



        </script>

<template>
    <Head title="Category" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Category Supply
            </h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-8 mb-4">

                        <!-- <fwb-button
                            class="bg-gray-900 hover:bg-gray-500"
                            @click="toggles.showInventoryDrawer = true"

                        >
                            View Logs
                        </fwb-button> -->

                        <fwb-button color="green" @click="toggles.showCategoryModal = true">
                            Add Supply
                        </fwb-button>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                   <DataTable
                    :value="props.categories"
                    tableStyle="min-width:10rem"
                    class="custom-datatable" >

                    <Column field="name" header="Category Name"></Column>
                    <Column field="description" header="Description"></Column>

                </DataTable>
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
            @close="toggles.showAddSupplyModal = false"
        />
         <CategoryModal
            v-if="toggles.showCategoryModal"
            @close="toggles.showCategoryModal = false"
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

