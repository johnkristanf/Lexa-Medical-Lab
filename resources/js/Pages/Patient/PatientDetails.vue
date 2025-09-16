<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Drawer } from 'primevue'
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'

    import { reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import PatientDetailsModal from '@/Components/modal/PatientDetailsModal.vue'
    import TestModal from '@/Components/modal/TestModal.vue'
    import EmailResultReminder from '@/Components/modal/EmailResultReminder.vue'
    import UpdatePatientDetails from '@/Components/modal/UpdatePatientDetails.vue'
    import AddButton from '@/Components/AddButton.vue'
    import { formatDate } from '@/helpers/formatter'

    const props = defineProps({
        patients: Array,
        inventory_logs: Array,
        testTypesPurpose: Array,
        testTypesRequest: Array,
        testCategory: Array,
        testType: Array,
        patientUpdate: Array,
    })


    const showAddScheduleModal = ref(false)
    const showSchedulesModal = ref(false)

    // EMAIL DETAILS MODAL REFS
    const showEmailAppointmentModal = ref(false)
    const selectedSchedule = ref()

    const patientUpdate = ref(null)
    const showUpdatePatientDetails = ref(false)

    const openEmailAppointmentDetails = (schedule) => {
        showEmailAppointmentModal.value = true
        selectedSchedule.value = schedule
    }

    const openUpdatePatientDetails = (patient) => {
        patientUpdate.value = patient
        showUpdatePatientDetails.value = true
    }

    const patientID = ref(null)

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    const togglesTestModal = reactive({
        showTestModal: false,
        showInventoryDrawer: false,
    })

    const showTestModal = (patient_id) => {
        ;(patientID.value = patient_id), (togglesTestModal.showTestModal = true)
        console.log('sa patient ni', patientID.value)
    }

    // TABLE HEADERS
    const patientTableHeaders = [
        'Patient ID',
        'Full Name',
        'Gender',
        'Birth Date',
        'Address',
        'Phone Number',
        'Email',
        'Actions',
    ]
</script>

<template>
    <Head title="Patient Details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Patient Details</h2>
        </template>

        <div>
            <div class="mx-auto max-w-8xl mt-3 sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <AddButton color="green" @click="toggles.showAddSupplyModal = true">
                            Add Patient
                        </AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <fwb-table hoverable>
                        <fwb-table-head>
                            <fwb-table-head-cell
                                v-for="(header, index) in patientTableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                            >
                                {{ header }}
                            </fwb-table-head-cell>
                        </fwb-table-head>

                        <fwb-table-body>
                            <template v-if="patients && patients.length > 0">
                                <fwb-table-row v-for="(patient, index) in patients" :key="index">
                                    <fwb-table-cell>{{ patient.patient_id }}</fwb-table-cell>
                                    <fwb-table-cell
                                        class="whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]"
                                    >
                                        {{ patient.first_name }} {{ patient.middle_name }}
                                        {{ patient.last_name }}
                                    </fwb-table-cell>
                                    <fwb-table-cell>{{ patient.gender }}</fwb-table-cell>
                                    <fwb-table-cell>
                                        {{ formatDate(patient.date_of_birth, false) }}
                                    </fwb-table-cell>
                                    <fwb-table-cell>{{ patient.address }}</fwb-table-cell>
                                    <fwb-table-cell>{{ patient.contact_number }}</fwb-table-cell>
                                    <fwb-table-cell>{{ patient.email }}</fwb-table-cell>

                                    <!-- Actions -->
                                    <fwb-table-cell class="flex items-center gap-3">
                                        <button
                                            @click="showTestModal(patient.id)"
                                            class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded whitespace-nowrap hover:opacity-75"
                                        >
                                            Conduct Test
                                        </button>

                                        <button
                                            @click="openEmailAppointmentDetails(patient)"
                                            class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded whitespace-nowrap hover:opacity-75"
                                        >
                                            Send Email
                                        </button>

                                        <button
                                            @click="openUpdatePatientDetails(patient)"
                                            class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded whitespace-nowrap hover:opacity-75"
                                        >
                                            Update
                                        </button>
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>

                            <template v-else>
                                <fwb-table-row>
                                    <fwb-table-cell
                                        colspan="10"
                                        class="text-center bg-gray-100 text-gray-500"
                                    >
                                        No patient records found.
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>
                        </fwb-table-body>
                    </fwb-table>
                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->
        <PatientDetailsModal
            v-if="toggles.showAddSupplyModal"
            @close="toggles.showAddSupplyModal = false"
        />

        <UpdatePatientDetails
            v-if="showUpdatePatientDetails"
            :patientUpdate="patientUpdate"
            @close="showUpdatePatientDetails = false"
        />

        <TestModal
            v-if="togglesTestModal.showTestModal"
            :testTypesPurpose="testTypesPurpose"
            :testTypesRequest="testTypesRequest"
            :patientID="patientID"
            :testCategory="testCategory"
            @close="togglesTestModal.showTestModal = false"
            :testType="testType"
        />
        <EmailResultReminder
            v-if="showEmailAppointmentModal"
            :selectedSchedule="selectedSchedule"
            @close="showEmailAppointmentModal = false"
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
                    v-bind:key="log.id"
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
