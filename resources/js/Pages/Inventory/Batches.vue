<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'

    const props = defineProps({
        supplies: Array,
    })

    const search = ref('')

    const toggles = reactive({
        showBatchModal: false,
        showInventoryDrawer: false,
    })

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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supply Inventory</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <!-- SEARCH INPUT -->
                        <SearchInput route="inventory.supply.batches" placeholder="Search Supplies" />
                    </div>

                    <!-- FLOWBITE TABLE -->
                    <fwb-table>
                        <fwb-table-head>
                            <fwb-table-head-cell class="bg-green-600 text-white">
                                Brand Name
                            </fwb-table-head-cell>
                            <fwb-table-head-cell class="bg-green-600 text-white">
                                Manufacturing Date
                            </fwb-table-head-cell>
                            <fwb-table-head-cell class="bg-green-600 text-white">
                                Expiration Date
                            </fwb-table-head-cell>
                            <fwb-table-head-cell class="bg-green-600 text-white">
                                Product Batch #
                            </fwb-table-head-cell>
                            <fwb-table-head-cell class="bg-green-600 text-white">Action</fwb-table-head-cell>
                        </fwb-table-head>

                        <fwb-table-body>
                            <fwb-table-row v-for="supply in props.supplies" :key="supply.id">
                                <fwb-table-cell>{{ supply.brand_name }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.manufacture_date }}</fwb-table-cell>
                                <fwb-table-cell>{{ supply.expiration_date }}</fwb-table-cell>
                                <fwb-table-cell>
                                    {{ supply.batches[0]?.batch_number ?? 'N/A' }}
                                </fwb-table-cell>
                                <fwb-table-cell>
                                    <button
                                        title="Archive Data"
                                        @click="archive(supply.id)"
                                        class="bg-yellow-500 px-3 py-1 rounded text-white hover:bg-yellow-600"
                                    >
                                        <i class="pi pi-folder-plus text-white text-lg"></i>
                                    </button>
                                </fwb-table-cell>
                            </fwb-table-row>
                        </fwb-table-body>
                    </fwb-table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
