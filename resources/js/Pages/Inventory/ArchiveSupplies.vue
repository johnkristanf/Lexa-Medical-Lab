<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { reactive, ref, watch } from 'vue'
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
        arcvhivedSupplies: Object,
    })

    const toggles = reactive({
        showBatchModal: false,
        showInventoryDrawer: false,
    })

    const tableHeaders = [
        { key: 'brand_name', label: 'Brand Name', custom: true },
        { key: 'manufacture_date', label: 'Manufacture Date', custom: true },
        { key: 'expiration_date', label: 'Expiration Date', custom: true },
        { key: 'batch_number', label: 'Product Batch #' },
    ]

    const perPage = ref(props.arcvhivedSupplies.per_page || 10)
    const search = ref('')

    watch(perPage, (value) => {
        router.get(
            route('archive.supplies.create'),
            { perPage: value, page: 1, search: search.value },
            { preserveState: true, replace: true },
        )
    })

    function doSearch(value) {
        search.value = value
        router.get(
            route('archive.supplies.create'),
            { search: value, perPage: perPage.value },
            { preserveState: true, replace: true },
        )
    }

    function goToPage(url) {
        if (!url) return
        router.get(
            url,
            { perPage: perPage.value, search: search.value },
            { preserveState: true, replace: true },
        )
    }

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
                    <SearchInput
                        v-model="search"
                        @update:value="doSearch"
                        route="archive.supplies.create"
                        placeholder="Search Supplies"
                    />
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
                        <FwbTableRow v-for="supply in arcvhivedSupplies.data" :key="supply.id">
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

                <!-- PAGINATION WITH PER PAGE -->
                <div
                    class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4"
                >
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between w-full">
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ arcvhivedSupplies.from }}</span>
                                to
                                <span class="font-medium">{{ arcvhivedSupplies.to }}</span>
                                of
                                <span class="font-medium">{{ arcvhivedSupplies.total }}</span>
                                results
                            </p>

                            <!-- Per Page Dropdown -->
                            <div class="flex items-center gap-2">
                                <label for="perPage" class="text-sm text-gray-700">Per page:</label>
                                <select
                                    id="perPage"
                                    v-model="perPage"
                                    class="rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <!-- Page Navigation -->
                        <div>
                            <nav
                                class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                aria-label="Pagination"
                            >
                                <!-- Previous -->
                                <button
                                    :disabled="!arcvhivedSupplies.prev_page_url"
                                    @click="goToPage(arcvhivedSupplies.prev_page_url)"
                                    class="relative inline-flex items-center gap-1 rounded-l-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                    :class="
                                        arcvhivedSupplies.prev_page_url
                                            ? 'text-gray-700 hover:bg-gray-50'
                                            : 'text-gray-400 cursor-not-allowed opacity-50'
                                    "
                                >
                                    Previous
                                </button>

                                <!-- Page Numbers -->
                                <button
                                    v-for="link in arcvhivedSupplies.links.slice(1, -1).slice(0, 5)"
                                    :key="link.label"
                                    @click="link.url && goToPage(link.url)"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300',
                                        link.active
                                            ? 'z-10 bg-green-600 text-white'
                                            : 'text-gray-900 hover:bg-gray-50',
                                    ]"
                                >
                                    {{ link.label }}
                                </button>

                                <!-- Next -->
                                <button
                                    :disabled="!arcvhivedSupplies.next_page_url"
                                    @click="goToPage(arcvhivedSupplies.next_page_url)"
                                    class="relative inline-flex items-center gap-1 rounded-r-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                    :class="
                                        arcvhivedSupplies.next_page_url
                                            ? 'text-gray-700 hover:bg-gray-50'
                                            : 'text-gray-400 cursor-not-allowed opacity-50'
                                    "
                                >
                                    Next
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
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
