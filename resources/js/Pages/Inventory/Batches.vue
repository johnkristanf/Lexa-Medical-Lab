<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, computed } from 'vue'
    import BatchModal from '@/Components/modal/BatchModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import { router } from '@inertiajs/vue3'

    const props = defineProps({
        supplies: Array,
    })

    const search = ref('')

    const toggles = reactive({
        showBatchModal: false,
        showInventoryDrawer: false,
    })

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const filteredSupplies = computed(() => {
    if (!search.value) {
        return props.supplies
    }

    return props.supplies.filter(item => {
        const brand = item.brand_name?.toLowerCase() || ''
        const manufacture = item.manufacture_date?.toLowerCase() || ''
        const expiration = item.expiration_date?.toLowerCase() || ''
      const batch = item.batches?.[0]?.batch_number?.toLowerCase() || ''


        return (
            brand.includes(search.value.toLowerCase()) ||
            manufacture.includes(search.value.toLowerCase()) ||
            expiration.includes(search.value.toLowerCase()) ||
            batch.includes(search.value.toLowerCase())
        )
    })
})

    const sampleOperationType = 'added'

    function archive(id) {
    if (confirm('Are you sure you want to archive this supply?')) {
        router.post(`/archive/supplies/${id}/store`, {
            preserveScroll: true,
        })
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

                   <DataTable
                    :value="filteredSupplies"
                    tableStyle="min-width: 50rem"
                    class="custom-datatable"
                     >
                    <Column field="brand_name" header="Brand Name"></Column>
                    <Column field="manufacture_date" header="Manufacturing Date"></Column>
                    <Column field="expiration_date" header="Expiration Date"></Column>
                <Column field="batches" header="Product Batch #">
                <template #body="{ data }">
                    {{ data.batches[0]?.batch_number ?? 'N/A' }}
                </template>
                </Column>
                    <Column header="Action">
                    <template #body="{ data }">
                        <button
                            title="Archive Data"
                            @click="archive(data.id)"
                            class="bg-yellow-500 px-3 py-1 rounded text-white hover:bg-yellow-600"
                        >
                            <i class="pi pi-folder-plus text-white text-lg"></i>
                        </button>
                    </template>
                </Column>
                </DataTable>
                </div>
            </div>
        </div>

        <!-- ADD SUPPLY MODAL -->
        <!-- <BatchModal
            v-if="toggles.showBatchModal"
            @close="toggles.showBatchModal = false"
        /> -->
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

