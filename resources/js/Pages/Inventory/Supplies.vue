<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import {
        FwbTable,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableBody,
        FwbTableRow,
        FwbTableCell,
    } from 'flowbite-vue'
    import { reactive, ref, computed, watch } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'

    const props = defineProps({
        supplies: Object,
        inventory_logs: Array,
        supplyUpdate: Object,
        categories: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    const selectedCategory = ref('')
    const search = ref('')
    const open = ref(false)

    const showUpdateSupply = ref(false)
    const supplyUpdate = ref(null)

    const perPage = ref(props.supplies.per_page || 10)

    const openUpdateSupply = (supply) => {
        supplyUpdate.value = supply
        showUpdateSupply.value = true
    }

    const filteredCategories = computed(() => {
        if (!search.value) return props.categories
        return props.categories.filter((c) => c.name.toLowerCase().includes(search.value.toLowerCase()))
    })

    function selectCategory(id) {
        selectedCategory.value = id
        open.value = false

        router.get(
            route('supplies.create.page'),
            { category: id, perPage: perPage.value },
            { preserveState: true, replace: true },
        )
    }

    watch(perPage, (value) => {
        router.get(
            route('supplies.create.page'),
            { category: selectedCategory.value, perPage: value, page: 1 },
            { preserveState: true, replace: true },
        )
    })

    const tableHeaders = [
        { key: 'participants', label: 'Item' },
        { key: 'brand_name', label: 'Brand Name' },
        { key: 'unit', label: 'Unit' },
        { key: 'quantity', label: 'Supplies Left' },
        { key: 'manufacture_date', label: 'Manufacturing Date' },
        { key: 'expiration_date', label: 'Expiration Date' },
        { key: 'lot_number', label: 'Lot #' },
        { key: 'action', label: 'Action', custom: true },
    ]
</script>

<template>
    <Head title="Supplies" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supplies</h2>
        </template>

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="card p-8">
                <!-- TABLE FUNCTIONS -->
                <div class="w-full flex justify-end gap-3 mb-4">
                    <!--  Category Dropdown -->
                    <div class="relative w-64">
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-left text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                        >
                            {{
                                selectedCategory
                                    ? props.categories.find((c) => c.id === Number(selectedCategory))?.name
                                    : 'All Categories'
                            }}
                        </button>

                        <div
                            v-if="open"
                            class="absolute mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-10"
                        >
                            <div class="p-2">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search..."
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-1 focus:ring-green-500 focus:outline-none"
                                />
                            </div>

                            <ul class="max-h-40 overflow-y-auto">
                                <li
                                    @click="selectCategory('')"
                                    class="px-3 py-2 text-sm cursor-pointer hover:bg-green-100"
                                >
                                    All Categories
                                </li>
                                <li
                                    v-for="category in filteredCategories"
                                    :key="category.id"
                                    @click="selectCategory(category.id)"
                                    class="px-3 py-2 text-sm cursor-pointer hover:bg-green-100"
                                >
                                    {{ category.name }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!--  Search -->
                    <SearchInput route="supplies.create.page" placeholder="Search Supplies" />
                </div>

                <!--  Table -->
                <FwbTable class="w-full min-w-[50rem]">
                    <FwbTableHead>
                        <FwbTableHeadCell
                            v-for="(header, index) in tableHeaders"
                            :key="index"
                            class="bg-green-600 text-white"
                        >
                            {{ header.label }}
                        </FwbTableHeadCell>
                    </FwbTableHead>

                    <FwbTableBody>
                        <FwbTableRow v-for="supply in supplies.data" :key="supply.id">
                            <FwbTableCell v-for="(header, index) in tableHeaders" :key="index">
                                <template v-if="!header.custom">
                                    {{ supply[header.key] || 'N/A' }}
                                </template>
                                <template v-else-if="header.key === 'action'">
                                    <a
                                        :href="route('inventory.supply.batches', { id: supply.id })"
                                        class="bg-gray-900 px-3 py-1 rounded text-white hover:opacity-75"
                                    >
                                        View
                                    </a>
                                    <button
                                        @click="openUpdateSupply(supply)"
                                        class="bg-green-600 px-3 h-[28px] ml-[8px] rounded text-white hover:opacity-75"
                                    >
                                        Deduct
                                    </button>
                                </template>
                            </FwbTableCell>
                        </FwbTableRow>
                    </FwbTableBody>
                </FwbTable>

                <!-- Pagination -->
                <div
                    class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4"
                >
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ supplies.from }}</span>
                                to
                                <span class="font-medium">{{ supplies.to }}</span>
                                of
                                <span class="font-medium">{{ supplies.total }}</span>
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

                        <div>
                            <nav
                                class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                aria-label="Pagination"
                            >
                                <!-- Prev -->
                                <button
                                    :disabled="!supplies.prev_page_url"
                                    @click="
                                        supplies.prev_page_url &&
                                        router.get(supplies.prev_page_url, { perPage })
                                    "
                                    :class="[
                                        'relative inline-flex items-center gap-1 rounded-l-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300',
                                        supplies.prev_page_url
                                            ? 'text-gray-700 hover:bg-gray-50'
                                            : 'text-gray-400 cursor-not-allowed opacity-50',
                                    ]"
                                >
                                    Previous
                                </button>

                                <!-- Numbered Pages -->
                                <button
                                    v-for="link in supplies.links.slice(1, -1).slice(0, 5)"
                                    @click="link.url && router.get(link.url, { perPage })"
                                    :key="link.label"
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
                                    :disabled="!supplies.next_page_url"
                                    @click="
                                        supplies.next_page_url &&
                                        router.get(supplies.next_page_url, { perPage })
                                    "
                                    :class="[
                                        'relative inline-flex items-center gap-1 rounded-r-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300',
                                        supplies.next_page_url
                                            ? 'text-gray-700 hover:bg-gray-50'
                                            : 'text-gray-400 cursor-not-allowed opacity-50',
                                    ]"
                                >
                                    Next
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Supply Modal -->
        <UpdateSupply
            v-if="showUpdateSupply"
            :supplyUpdate="supplyUpdate"
            @close="showUpdateSupply = false"
        />

        <!-- Add Supply Modal -->
        <AddSupplyModal
            v-if="toggles.showAddSupplyModal"
            :categories="categories"
            @close="toggles.showAddSupplyModal = false"
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
