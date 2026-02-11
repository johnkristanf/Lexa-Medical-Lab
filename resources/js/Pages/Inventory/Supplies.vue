<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, usePage } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, computed } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import {
        FwbTable,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableBody,
        FwbTableRow,
        FwbTableCell,
    } from 'flowbite-vue'

    // Utilities
    const page = usePage()

    const props = defineProps({
        supplies: Array,
        inventory_logs: Array,
        supplyUpdate: Object,
        categories: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })
    const selectedCategory = ref('')

    const showUpdateSupply = ref(false)
    const supplyUpdate = ref(null)

    const openUpdateSupply = (supply) => {
        supplyUpdate.value = supply
        showUpdateSupply.value = true
    }

    const filteredSupplies = computed(() => {
        if (!selectedCategory.value) {
            return props.supplies
        }
        return props.supplies.filter((supply) => supply.category_id === Number(selectedCategory.value))
    })

    //  Dropdown state
    const open = ref(false)
    const search = ref('')

    const filteredCategories = computed(() => {
        if (!search.value) return props.categories
        return props.categories.filter((c) => c.name.toLowerCase().includes(search.value.toLowerCase()))
    })

    function selectCategory(id) {
        selectedCategory.value = id
        open.value = false
    }

    const sampleOperationType = 'added'
    const tableHeaders = [
        { key: 'participants', label: 'Item' },
        { key: 'brand_name', label: 'Brand Name' },
        { key: 'quantity', label: 'Supplies Left' },
        { key: 'action', label: 'Action', custom: true },
    ]

    // Detect if the current route is /supplies/create/data
    const currentRouteName = computed(() => page.url)
    const isInCreateDataRoute = computed(() => currentRouteName.value.startsWith('/supplies/create/data'))
</script>

<template>
    <Head title="Supplies" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supplies</h2>
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
                                        isInCreateDataRoute ? 'text-green-700 bg-green-100' : 'text-gray-700 hover:text-green-700 hover:bg-gray-100'
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
                                    class="inline-flex items-center text-gray-600 hover:text-green-600 font-medium px-2 py-1 rounded"
                                >
                                    Archive
                                </a>
                            </li>
                        </ol>
                    </nav>
                    <!-- End Breadcrumb -->

                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <!-- ✅ Custom Searchable Dropdown -->
                        <div class="relative w-64">
                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-left text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                            >
                                {{
                                    selectedCategory
                                        ? props.categories.find((c) => c.id === Number(selectedCategory))
                                              ?.name
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

                        <SearchInput route="supplies.create.page" placeholder="Search Supplies" />
                    </div>

                    
                    <FwbTable class="w-full min-w-[50rem]">
                        <!-- Table Head -->
                        <FwbTableHead>
                            <FwbTableHeadCell
                                v-for="(header, index) in tableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                                :class="{ 'text-left': header.key === 'action' }"
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
                                    :class="{ 'text-left': header.key === 'action' }"
                                >
                                    <!-- Default fields -->
                                    <template v-if="!header.custom">
                                        {{ supply[header.key] || 'N/A' }}
                                    </template>

                                    <!-- Action column -->
                                    <template v-else-if="header.key === 'action'">
                                        <div class="flex items-center justify-start gap-2">
                                            <a
                                                :href="route('inventory.supply.batches', { id: supply.id })"
                                                title="View Batch"
                                                class="bg-gray-900 px-3 py-1 rounded text-white hover:opacity-75"
                                            >
                                                View
                                            </a>

                                            <button
                                                @click="openUpdateSupply(supply)"
                                                title="Update Supply"
                                                class="bg-green-600 px-3 h-[28px] ml-[0px] rounded text-white hover:opacity-75"
                                            >
                                                Deduct
                                            </button>
                                        </div>
                                    </template>
                                </FwbTableCell>
                            </FwbTableRow>
                        </FwbTableBody>
                    </FwbTable>
                </div>
            </div>
        </div>

        <UpdateSupply
            v-if="showUpdateSupply"
            :supplyUpdate="supplyUpdate"
            @close="showUpdateSupply = false"
        />

        <!-- ADD SUPPLY MODAL -->
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
