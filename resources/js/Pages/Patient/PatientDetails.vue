<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import PatientDetailsModal from '@/Components/modal/PatientDetailsModal.vue'
    import TestModal from '@/Components/modal/TestModal.vue'


    const props = defineProps({
        patients: Array,
        inventory_logs: Array,
        testTypesPurpose: Array,
        testTypesRequest: Array,
        testCategory: Array,
        testType: Array,

    })

    const patientID = ref(null);


    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })


     const togglesTestModal = reactive({
        showTestModal: false,
        showInventoryDrawer: false,
    })

    const showTestModal = (patient_id) => {
        patientID.value = patient_id,
        togglesTestModal.showTestModal = true;
        console.log('sa patient ni',patientID.value);

    }





    const sampleOperationType = 'added'
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Patient Details
            </h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">


                        <fwb-button color="green" @click="toggles.showAddSupplyModal = true">
                            Add Patient
                        </fwb-button>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable
                        :value="props.patients"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="patient_id" header="Patient ID"></Column>
                        <Column field="first_name" header="First Name"></Column>
                        <Column field="middle_name" header="Middle Name"></Column>
                        <Column field="last_name" header="Last Name"></Column>
                        <Column field="gender" header="Gender"></Column>
                        <Column field="date_of_birth" header="Birth Date"></Column>
                        <Column field="address" header="Address"></Column>
                        <Column field="contact_number" header="Phone Number#"></Column>
                        <Column field="email" header="Email"></Column>
                          <Column header="Actions">
                        <template #body="slotProps">
                            <button
                            @click="showTestModal(slotProps.data.id)"
                            style="background-color: green; color: white; border: none; padding: 0.2rem 0.8rem; border-radius: 4px;"
                          >
                            Test
                        </button>
                        </template>
                    </Column>
                    </DataTable>

                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->
        <PatientDetailsModal
            v-if="toggles.showAddSupplyModal"
            @close="toggles.showAddSupplyModal = false"
        />

        <TestModal
            v-if="togglesTestModal.showTestModal"
            :testTypesPurpose="testTypesPurpose"
            :testTypesRequest="testTypesRequest"
            :patientID ="patientID"
            :testCategory="testCategory"
            @close="togglesTestModal.showTestModal = false"
            :testType="testType"
        />


        <!-- DRAWER FOR INVENTORY LOGS -->
        <Drawer
            v-model:visible="toggles.showInventoryDrawer"
            header="Inventory Logs"
            position="right"
            class="!w-full sm:!w-80 lg:!w-[25rem]"
        >
            <div class="flex flex-col gap-3">

                <div v-for="log in props.inventory_logs" v-bind:key="log.id" class="flex flex-col gap-4 border-2 border-gray-400 p-3 rounded-md">
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
