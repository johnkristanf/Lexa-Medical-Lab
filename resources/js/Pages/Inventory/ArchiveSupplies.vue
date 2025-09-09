<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Column, DataTable } from 'primevue'
import { reactive, ref, computed } from 'vue'
import SearchInput from '@/Components/SearchInput.vue'

const props = defineProps({
    supplies: Array,
    inventory_logs: Array,
    arcvhivedSupplies: Array,
})

const search = ref('')

const toggles = reactive({
    showBatchModal: false,
    showInventoryDrawer: false,
})

const filteredSupplies = computed(() => {
    if (!search.value) {
        return props.arcvhivedSupplies
    }

    return props.arcvhivedSupplies.filter(item => {
        const brand = item.medical_supply?.brand_name?.toLowerCase() || ''
        const manufacture = item.medical_supply?.manufacture_date?.toLowerCase() || ''
        const expiration = item.medical_supply?.expiration_date?.toLowerCase() || ''
        const batch = item.batches?.batch_number?.toLowerCase() || ''

        return (
            brand.includes(search.value.toLowerCase()) ||
            manufacture.includes(search.value.toLowerCase()) ||
            expiration.includes(search.value.toLowerCase()) ||
            batch.includes(search.value.toLowerCase())
        )
    })
})

console.log('Archived Supplies: ', props.arcvhivedSupplies)
</script>

<template>
    <Head title="Archived Supplies" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Archived Supplies
            </h2>
        </template>

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="card p-8">
                <!-- TABLE HEADER ACTIONS -->
                <div class="w-full flex justify-end gap-3 mb-4">
                    <SearchInput v-model="search" />
                </div>

                <DataTable
                    :value="filteredSupplies"
                    tableStyle="min-width: 50rem"
                    class="custom-datatable"
                >
                    <!-- Brand Name -->
                    <Column header="Brand Name">
                        <template #body="{ data }">
                            {{ data.medical_supply?.brand_name || 'N/A' }}
                        </template>
                    </Column>

                    <!-- Manufacture Date -->
                    <Column header="Manufacture Date">
                        <template #body="{ data }">
                            {{ data.medical_supply?.manufacture_date || 'N/A' }}
                        </template>
                    </Column>

                    <!-- Expiration Date -->
                    <Column header="Expiration Date">
                        <template #body="{ data }">
                            {{ data.medical_supply?.expiration_date || 'N/A' }}
                        </template>
                    </Column>

                    <!-- Product Batch # -->
                    <Column header="Product Batch #">
                        <template #body="{ data }">
                            {{ data.batches?.batch_number || 'N/A' }}
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
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
