<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Drawer } from 'primevue'
    import { reactive, ref, watch } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import CategoryModal from '@/Components/modal/CategoryModal.vue'
    import AddButton from '@/Components/AddButton.vue'
    import UpdateCategories from '@/Components/modal/UpdateCategories.vue'
    import {
        FwbTable,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableBody,
        FwbTableRow,
        FwbTableCell,
    } from 'flowbite-vue'

    const props = defineProps({
        inventory_logs: Array,
        categories: Object,
        categoryUpdate: Array,
    })

    const toggles = reactive({
        showCategoryModal: false,
        showInventoryDrawer: false,
    })

    const categoryUpdate = ref(null)
    const showCategoryUpdate = ref(false)

    const openCategoryUpdate = (category) => {
        categoryUpdate.value = category
        showCategoryUpdate.value = true
    }

    const showUpdateSupply = ref(false)
    const supplyUpdate = ref(null)

    const openUpdateSupply = (supply) => {
        supplyUpdate.value = supply
        showUpdateSupply.value = true
    }

    const perPage = ref(props.categories.per_page || 10)
    const search = ref('')

    watch(perPage, (value) => {
        router.get(
            route('category.supplies.create'),
            {
                perPage: value,
                page: 1,
                search: search.value,
            },
            { preserveState: true, replace: true },
        )
    })

    function DeletedCategory(id) {
        if (confirm('Are you sure you want to delete this category?')) {
            router.delete(route('delete.category', id), {
                onSuccess: () => {
                    console.log('Category deleted successfully')
                },
            })
        }
    }

    const headers = [
        { key: 'name', label: 'Category Name' },
        { key: 'description', label: 'Description' },
        { key: 'action', label: 'Action' },
    ]
</script>

<template>
    <Head title="Category" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Category Supply</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-8 mb-4">
                        <AddButton color="green" @click="toggles.showCategoryModal = true">
                            Add Category
                        </AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput
                            route="category.supplies.create"
                            placeholder="Search Category"
                            v-model="search"
                        />
                    </div>

                    <FwbTable class="w-full min-w-[40rem]">
                        <!-- Table Head -->
                        <FwbTableHead>
                            <FwbTableHeadCell
                                v-for="header in headers"
                                :key="header.key"
                                class="bg-green-600 text-white"
                            >
                                {{ header.label }}
                            </FwbTableHeadCell>
                        </FwbTableHead>

                        <FwbTableBody>
                            <FwbTableRow v-for="category in categories.data" :key="category.id">
                                <FwbTableCell v-for="header in headers" :key="header.key" class="!text-left">
                                    <template v-if="header.key === 'action'">
                                        <div class="flex gap-2">
                                            <button
                                                class="px-4 py-2 text-xs font-medium text-green-200 bg-green-600 rounded hover:opacity-75"
                                                @click="openCategoryUpdate(category)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="px-2 py-1 text-xs font-medium text-green-200 bg-red-600 rounded hover:opacity-75"
                                                @click="DeletedCategory(category.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Normal cells -->
                                    <template v-else>
                                        {{ category[header.key] }}
                                    </template>
                                </FwbTableCell>
                            </FwbTableRow>
                        </FwbTableBody>
                    </FwbTable>

                    <!-- PAGINATION -->
                    <div
                        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4"
                    >
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ categories.from }}</span>
                                    to
                                    <span class="font-medium">{{ categories.to }}</span>
                                    of
                                    <span class="font-medium">{{ categories.total }}</span>
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
                                        :disabled="!categories.prev_page_url"
                                        @click="router.get(categories.prev_page_url, { perPage, search })"
                                        class="relative inline-flex items-center gap-1 rounded-l-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                        :class="
                                            categories.prev_page_url
                                                ? 'text-gray-700 hover:bg-gray-50'
                                                : 'text-gray-400 cursor-not-allowed opacity-50'
                                        "
                                    >
                                        Previous
                                    </button>

                                    <!-- Page Numbers -->
                                    <button
                                        v-for="link in categories.links.slice(1, -1).slice(0, 5)"
                                        :key="link.label"
                                        @click="link.url && router.get(link.url, { perPage, search })"
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
                                        :disabled="!categories.next_page_url"
                                        @click="router.get(categories.next_page_url, { perPage, search })"
                                        class="relative inline-flex items-center gap-1 rounded-r-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                        :class="
                                            categories.next_page_url
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
        </div>

        <UpdateSupply
            v-if="showUpdateSupply"
            :supplyUpdate="supplyUpdate"
            @close="showUpdateSupply = false"
        />

        <UpdateCategories
            v-if="showCategoryUpdate"
            :updateCategory="categoryUpdate"
            @close="showCategoryUpdate = false"
        />

        <CategoryModal v-if="toggles.showCategoryModal" @close="toggles.showCategoryModal = false" />

        <!-- DRAWER FOR INVENTORY LOGS -->
        <Drawer
            v-model:visible="toggles.showInventoryDrawer"
            header="Inventory Logs"
            position="right"
            class="!w-full sm:!w-80 lg:!w-[25rem]"
        >
            <div class="flex flex-col gap-3">
                <div
                    v-for="log in props.inventory_logs"
                    :key="log.id"
                    class="flex flex-col gap-4 border-2 border-gray-400 p-3 rounded-md"
                >
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
                                'bg-green-100 text-green-800': log.operation_type === OPERATION_TYPES.ADDED,
                                'bg-red-100 text-yellow-800': log.operation_type === OPERATION_TYPES.DEDUCTED,
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
