<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head,router } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import TestCategoryModal from '@/Components/modal/TestCategoryModal.vue'
    import TestTypesModal from '@/Components/modal/TestTypesModal.vue'

    const props = defineProps({
        test_category: Array,
        inventory_logs: Array,
    })

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

     const testTypesToggle = reactive({
        // showAddTestTypesModal: false,
        showInventoryDrawer: false,
        category_id : null,
    })

    const category = ref(null)

function DeletedTestCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(route('test.category.delete', id), {
            onSuccess: () => {
                console.log('Category deleted successfully');
            },
        });
    }
}

function test(categoryq) {
    category.value = categoryq
    testTypesToggle.showAddTestTypesModal = true
    // console.log(testTypesToggle.showAddTestTypesModal)
}

    // function openInventoryDrawer() {
    //     toggles.showInventoryDrawer = true
    // }

    // function closeInventoryDrawer() {
    //     toggles.showInventoryDrawer = false
    // }
// }

// function openTestModal(id) {
//     console.log(id)
//     testTypesToggle.showAddTestTypesModal = true
//     category_id = id;
// }
    const sampleOperationType = 'added'
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Test Category
            </h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">

                        <fwb-button color="green" @click="toggles.showAddSupplyModal = true">
                            Add Category
                        </fwb-button>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                      <DataTable
                        :value="props.test_category.data"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                       >
                        <Column field="name" header="Category Name"></Column>
                        <Column field="created_at" header="Created At">
                        <template #body="slotProps">
                            {{
                            new Intl.DateTimeFormat('en-PH', {
                                timeZone: 'Asia/Manila',
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                 hour: '2-digit',
                                 minute: '2-digit',
                                 hour12: true
                            }).format(new Date(slotProps.data.created_at))
                            }}
                        </template></Column>
                        <Column field="updated_at" header="Update At">
                           <template #body="slotProps">
                            {{
                            new Intl.DateTimeFormat('en-PH', {
                                timeZone: 'Asia/Manila',
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                 hour: '2-digit',
                                 minute: '2-digit',
                                 hour12: true
                            }).format(new Date(slotProps.data.created_at))
                            }}
                        </template>
                        </Column>
                       <Column header="Actions">
                    <template #body="slotProps">
                        <div style="display: flex; gap: 0.5rem;">
                        <!-- <button
                            @click="toggles.showAddSupplyModal = true, slotProps.data"
                            style="background-color: orange; color: white; border: none; padding: 0.3rem 0.5rem; border-radius: 4px;"
                        >
                            Edit
                        </button> -->

                         <button
                            @click="DeletedTestCategory(slotProps.data.id) "
                            style="background-color: red; color: white; border: none; padding: 0.3rem 0.5rem; border-radius: 4px;"
                        >
                            Delete
                        </button>

                        <button
                            @click="test(slotProps.data)"
                            style="background-color: green; color: white; border: none; padding: 0.3rem 0.5rem; border-radius: 4px;"
                        >
                            Add Test Type
                        </button>
                        </div>
                    </template>
                    </Column>
                    </DataTable>
            <div className="flex space-x-1 mt-2">
            <Link
            v-if="test_category.prev_page_url"
            :href="test_category.prev_page_url"
            disabled={isPrevDisabled}
            class="px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200"
            :class="!test_category.prev_page_url ? 'bg-gray-400 cursor-not-allowed opacity-70' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'">
             <!-- className={`
                px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200
                ${
                isPrevDisabled
                    ? 'bg-gray-400 cursor-not-allowed opacity-70'
                    : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'
                }
            `} -->
            Previous
            </Link>
            <button
            v-else
            disabled={isPrevDisabled}
            class="px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200
                bg-gray-400 cursor-not-allowed opacity-70">
             <!-- className={`
                px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200
                ${
                isPrevDisabled
                    ? 'bg-gray-400 cursor-not-allowed opacity-70'
                    : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'
                }
            `} -->
            Previous
            </button>

            <Link
             v-if="test_category.next_page_url"
            :href="test_category.next_page_url"
            disabled={isNextDisabled}
            class="px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200"
            :class="!test_category.next_page_url ? 'bg-gray-400 cursor-not-allowed opacity-70' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'">

             <!-- className={'
                px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200
                ${
                isNextDisabled
                    ? 'bg-gray-400 cursor-not-allowed opacity-70'
                    : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'
                }
            '} -->
            Next
            </Link>
            <button
             v-else
            disabled={isNextDisabled}
            class="px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200
                bg-gray-300 cursor-not-allowed opacity-70"
            >

             <!-- className={'
                px-4 py-2 rounded-md font-medium text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                transition-all duration-200
                ${
                isNextDisabled
                    ? 'bg-gray-400 cursor-not-allowed opacity-70'
                    : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'
                }
            '} -->
            Next
            </button>

        </div>
                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->
        <TestCategoryModal
            v-if="toggles.showAddSupplyModal"
            @close="toggles.showAddSupplyModal = false"
        />

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

                <div v-for="log in props.inventory_logs" class="flex flex-col gap-4 border-2 border-gray-400 p-3 rounded-md">
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
                                'bg-green-100 text-green-800':
                                    log.operation_type === OPERATION_TYPES.ADDED,
                                'bg-red-100 text-yellow-800':
                                    log.operation_type === OPERATION_TYPES.DEDUCTED,
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
