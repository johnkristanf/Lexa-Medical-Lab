<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Drawer } from 'primevue'
    import { reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import TestCategoryModal from '@/Components/modal/TestCategoryModal.vue'
    import TestTypesModal from '@/Components/modal/TestTypesModal.vue'
    import AddButton from '@/Components/AddButton.vue'
    import DangerButton from '@/Components/DangerButton.vue'

    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'

    const props = defineProps({
        test_category: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    const testTypesToggle = reactive({
        // showAddTestTypesModal: false,
        showInventoryDrawer: false,
        category_id: null,
    })

    const category = ref(null)

    function DeletedTestCategory(id) {
        if (confirm('Are you sure you want to delete this category?')) {
            router.delete(route('test.category.delete', id), {
                onSuccess: () => {
                    console.log('Category deleted successfully')
                },
            })
        }
    }

    function test(categoryq) {
        category.value = categoryq
        testTypesToggle.showAddTestTypesModal = true
    }

    const categoryTableHeaders = ['Category Name', 'Created At', 'Updated At', 'Actions']
</script>

<template>
    <Head title="Test Catalog" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Test Catalog</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <AddButton @click="toggles.showAddSupplyModal = true">Add Category</AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput route="test.category.create" placeholder="Search Category Name" />
                    </div>

                    <fwb-table hoverable>
                        <!-- Table Head -->
                        <fwb-table-head>
                            <fwb-table-head-cell
                                v-for="(header, index) in categoryTableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                            >
                                {{ header }}
                            </fwb-table-head-cell>
                        </fwb-table-head>

                        <!-- Table Body -->
                        <fwb-table-body>
                            <template v-if="test_category && test_category.length > 0">
                                <fwb-table-row v-for="(category, index) in props.test_category" :key="index">
                                    <!-- Category Name -->
                                    <fwb-table-cell>{{ category.name }}</fwb-table-cell>

                                    <!-- Created At -->
                                    <fwb-table-cell>
                                        {{
                                            new Intl.DateTimeFormat('en-PH', {
                                                timeZone: 'Asia/Manila',
                                                year: 'numeric',
                                                month: '2-digit',
                                                day: '2-digit',
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: true,
                                            }).format(new Date(category.created_at))
                                        }}
                                    </fwb-table-cell>

                                    <!-- Updated At -->
                                    <fwb-table-cell>
                                        {{
                                            new Intl.DateTimeFormat('en-PH', {
                                                timeZone: 'Asia/Manila',
                                                year: 'numeric',
                                                month: '2-digit',
                                                day: '2-digit',
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: true,
                                            }).format(new Date(category.updated_at))
                                        }}
                                    </fwb-table-cell>

                                    <!-- Actions -->
                                    <fwb-table-cell class="flex gap-2">
                                        <AddButton @click="test(category)">Add Test Type</AddButton>
                                        <DangerButton @click="DeletedTestCategory(category.id)">
                                            Delete
                                        </DangerButton>
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>

                            <!-- No records -->
                            <template v-else>
                                <fwb-table-row>
                                    <fwb-table-cell colspan="2" class="bg-gray-100 text-gray-500">
                                        No categories found.
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>
                        </fwb-table-body>
                    </fwb-table>
                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->
        <TestCategoryModal v-if="toggles.showAddSupplyModal" @close="toggles.showAddSupplyModal = false" />

        <TestTypesModal
            v-if="testTypesToggle.showAddTestTypesModal"
            @close="testTypesToggle.showAddTestTypesModal = false"
            :category="category"
        />

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
        background-color: #208b3a; /* blue header */
        color: white;
    }

    .custom-datatable ::v-deep(.p-datatable-tbody > tr > td) {
        background-color: #ffffff; /* white rows */
        color: #374151; /* gray-700 text */
    }
</style>
