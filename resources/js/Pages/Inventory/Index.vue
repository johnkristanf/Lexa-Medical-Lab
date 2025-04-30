<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { ref } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'

    const props = defineProps({
        supplies: Array,
    })

    const showAddSupplyModal = ref(false)

    console.log('supplies: ', props.supplies)
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Supply Inventory
            </h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <fwb-button color="green" @click="showAddSupplyModal = true">
                            Add Supply
                        </fwb-button>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable
                        :value="props.supplies"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="participants" header="Participants"></Column>
                        <Column field="brand_name" header="Brand Name"></Column>
                        <Column field="unit" header="Unit"></Column>
                        <Column field="quantity" header="Supplies Left"></Column>
                        <Column field="manufacture_date" header="Manufacturing Date"></Column>
                        <Column field="expiration_date" header="Expiration Date"></Column>
                        <Column field="lot_number" header="Lot #"></Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->
        <AddSupplyModal v-if="showAddSupplyModal" @close="showAddSupplyModal = false" />
    </AuthenticatedLayout>
</template>
