<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'
    import SelectButton from 'primevue/selectbutton'
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
        categories: Array,
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

    console.log('supplies: ', props.supplies)
    console.log('inventory_logs: ', props.inventory_logs)

    const sampleOperationType = 'added'

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
                        <!-- <fwb-button
                            class="bg-gray-900 hover:bg-gray-500"
                            @click="toggles.showInventoryDrawer = true"

                        >
                            View Logs
                        </fwb-button> -->

                        <AddButton color="green" @click="toggles.showCategoryModal = true">
                            Add Category
                        </AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput route="category.supplies.create" placeholder="Search Category" />
                    </div>

                    <FwbTable>
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
                            <FwbTableRow v-for="category in props.categories" :key="category.id">
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

        <!-- ADD SUPLY MODAL -->
        <AddSupplyModal v-if="toggles.showAddSupplyModal" @close="toggles.showAddSupplyModal = false" />

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
                    :key="log.props.inventory_logs"
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
