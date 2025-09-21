<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref, computed } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import UpdateSupply from '@/Components/modal/UpdateSupply.vue'

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

                        <SearchInput />
                    </div>

                    <DataTable
                        :value="filteredSupplies"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="participants" header="Item"></Column>
                        <Column field="brand_name" header="Brand Name"></Column>
                        <Column field="unit" header="Unit"></Column>
                        <Column field="quantity" header="Supplies Left"></Column>
                        <Column field="manufacture_date" header="Manufacturing Date"></Column>
                        <Column field="expiration_date" header="Expiration Date"></Column>
                        <Column field="lot_number" header="Lot #"></Column>
                        <Column header="Action">
                            <template #body="slotProps">
                                <a
                                    :href="route('inventory.supply.batches', { id: slotProps.data.id })"
                                    title="View Batch"
                                    class="bg-[#70e000] px-3 py-1 rounded text-white hover:bg-[#1b4332]"
                                >
                                    View
                                </a>

                                <button
                                    @click="openUpdateSupply(slotProps.data)"
                                    title="Update Supply"
                                    class="bg-[#70e000] px-3 h-[28px] ml-[8px] rounded text-white hover:bg-[#1b4332]"
                                >
                                    Deduct
                                </button>
                            </template>
                        </Column>
                    </DataTable>
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
