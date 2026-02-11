<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, usePage } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, computed } from 'vue'
    import BatchModal from '@/Components/modal/BatchModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import { router } from '@inertiajs/vue3'

    // Utilities
    const page = usePage()

    const props = defineProps({
        supplies: Array,
    })

    const search = ref('')

    const toggles = reactive({
        showBatchModal: false,
        showInventoryDrawer: false,
    })

    // --- Breadcrumb logic: isArchiveBatchRoute? ---
    // We'll match the route starts with "/medical/supply/batches" for flexible dynamic param highlighting
    const currentRouteName = computed(() => page.url)
    const isInBatchesArchiveRoute = computed(() => currentRouteName.value.startsWith('/medical/supply/batches'))

    // --- rest of original logic ---
    const filteredSupplies = computed(() => {
        if (!search.value) {
            return props.supplies
        }

        return props.supplies.filter((item) => {
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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supply Batch</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">

                    <!-- Breadcrumb Navigation -->
                    <nav class="flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a 
                                    :class="[
                                        'inline-flex items-center font-medium px-2 py-1 rounded',
                                        !isInBatchesArchiveRoute ? 'text-green-700 bg-green-100' : 'text-gray-700 hover:text-green-700 hover:bg-gray-100'
                                    ]"
                                    href="/supplies/create/data"
                                >
                                    Supplies
                                </a>
                            </li>
                            <li>
                                <span class="mx-2 text-gray-400 font-semibold">/</span>
                            </li>
                            <li>
                                <a
                                    href="/medical/supply/batches"
                                    :class="[
                                        'inline-flex items-center font-medium px-2 py-1 rounded',
                                        isInBatchesArchiveRoute ? 'text-green-700 bg-green-100' : 'text-gray-600 hover:text-green-600'
                                    ]"
                                >
                                    Archive
                                </a>
                            </li>
                        </ol>
                    </nav>
                    <!-- End Breadcrumb -->
                    
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <!-- SEARCH INPUT -->
                        <SearchInput v-model="search" />
                    </div>

                    <DataTable
                        :value="filteredSupplies"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="unit" header="Unit"></Column>
                        <Column field="manufacture_date" header="Manufacturing Date"></Column>
                        <Column field="expiration_date" header="Expiration Date"></Column>
                        <Column field="lot_number" header="Lot #"></Column>
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
