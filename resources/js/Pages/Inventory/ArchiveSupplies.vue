<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable } from 'primevue'
    import { reactive, ref, computed } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
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

        return props.arcvhivedSupplies.filter((item) => {
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

    const tableHeaders = [
        { key: 'brand_name', label: 'Brand Name', custom: true },
        { key: 'manufacture_date', label: 'Manufacture Date', custom: true },
        { key: 'expiration_date', label: 'Expiration Date', custom: true },
        { key: 'batch_number', label: 'Product Batch #', custom: true },
    ]

    console.log('Archived Supplies: ', props.arcvhivedSupplies)
</script>

<template>
    <Head title="Archived Supplies" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Archived Supplies</h2>
        </template>

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="card p-8">
                <!-- TABLE HEADER ACTIONS -->
                <div class="w-full flex justify-end gap-3 mb-4">
                    <SearchInput route="archive.supplies.create" placeholder="Search Supplies" />
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
                            <FwbTableCell
                                v-for="(header, index) in tableHeaders"
                                :key="index"
                                class="!text-left"
                            >
                                <template v-if="header.key === 'brand_name'">
                                    {{ supply.medical_supply?.brand_name || 'N/A' }}
                                </template>

                                <template v-else-if="header.key === 'manufacture_date'">
                                    {{ supply.medical_supply?.manufacture_date || 'N/A' }}
                                </template>

                                <template v-else-if="header.key === 'expiration_date'">
                                    {{ supply.medical_supply?.expiration_date || 'N/A' }}
                                </template>

                                <template v-else-if="header.key === 'batch_number'">
                                    {{ supply.batches?.batch_number || 'N/A' }}
                                </template>
                            </FwbTableCell>
                        </FwbTableRow>
                    </FwbTableBody>
                </FwbTable>
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
