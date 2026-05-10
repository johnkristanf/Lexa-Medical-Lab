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

    import { onMounted, reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import { TRANSACTION_TYPES } from '@/Enums/Patient'
    import PatientDetailsModal from '@/Components/modal/PatientDetailsModal.vue'
    import TestModal from '@/Components/modal/TestModal.vue'
    import EmailResultReminder from '@/Components/modal/EmailResultReminder.vue'
    import UpdatePatientDetails from '@/Components/modal/UpdatePatientDetails.vue'
    import AddButton from '@/Components/AddButton.vue'
    import { formatDate } from '@/helpers/formatter'
    import { EnvelopeIcon, InboxArrowDownIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/20/solid'
    import { router } from '@inertiajs/vue3'

    const props = defineProps({
        patients: Array,
        inventory_logs: Array,
        testTypesPurpose: Array,
        testTypesRequest: Array,
        testCategory: Array,
        testType: Array,
        patientUpdate: Array,
        priority_types: Array,
    })

    const showAddScheduleModal = ref(false)
    const showSchedulesModal = ref(false)

    // EMAIL DETAILS MODAL REFS
    const showEmailAppointmentModal = ref(false)
    const selectedEmail = ref()

    const patientUpdate = ref(null)
    const showUpdatePatientDetails = ref(false)

    const openEmailAppointmentDetails = (email) => {
        showEmailAppointmentModal.value = true
        selectedEmail.value = email
    }

    const openUpdatePatientDetails = (patient) => {
        patientUpdate.value = patient
        showUpdatePatientDetails.value = true
    }

    const patientID = ref(null)
    const patientPriotityType = ref(null)

    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    const togglesTestModal = reactive({
        showTestModal: false,
        showInventoryDrawer: false,
    })

    const showTestModal = (patient_id, priority_type) => {
        console.log('priority_type: ', priority_type)

        patientID.value = patient_id
        togglesTestModal.showTestModal = true
        patientPriotityType.value = priority_type
    }

    const deletePatient = (id) => {
        if (confirm('Are you sure you want to delete this patient?')) {
            router.delete(route('patient.delete', id), {
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Patient deleted successfully')
                },
            })
        }
    }

    // TABLE HEADERS
    const patientTableHeaders = [
        'Patient ID',
        'Transaction',
        'Patient Type',
        'Full Name',
        'Address',
        'Contact Number',
        'Email',
        'Sex',
        'Birth Date',
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
                        <button
                            class="bg-red-600 text-white text-sm font-medium px-4 py-2 rounded hover:opacity-75"
                        >
                            <a class="text-white-600" :href="route('patient.report')" target="_blank">
                                Print Reports
                            </a>
                        </button>

                        <AddButton color="green" @click="toggles.showAddSupplyModal = true">
                            Add Patient
                        </AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput
                            route="patient.details.create"
                            placeholder="Search Patient ID, Name, Email"
                        />
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
                                    <fwb-table-cell>
                                        <span
                                            v-if="patient.transaction_type === TRANSACTION_TYPES.APPOINTMENT"
                                            class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-blue-400"
                                        >
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Appointment
                                        </span>
                                        <span
                                            v-else-if="patient.transaction_type === TRANSACTION_TYPES.WALK_IN"
                                            class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-purple-400"
                                        >
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                              <path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/>
                                            </svg>
                                            Walk-in
                                        </span>
                                        <span v-else class="text-gray-500">-</span>
                                    </fwb-table-cell>
                                    <fwb-table-cell>{{ patient.priority_type?.name ?? '' }}</fwb-table-cell>
                                    <fwb-table-cell
                                        class="whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]"
                                    >
                                        {{ patient.first_name }} {{ patient.middle_name }}
                                        {{ patient.last_name }}
                                    </fwb-table-cell>

                                    <fwb-table-cell>{{ patient.address }}</fwb-table-cell>
                                    <fwb-table-cell>{{ patient.contact_number }}</fwb-table-cell>
                                    <fwb-table-cell>{{ patient.email }}</fwb-table-cell>
                                    <fwb-table-cell>{{ patient.gender }}</fwb-table-cell>
                                    <fwb-table-cell>
                                        {{ formatDate(patient.date_of_birth, false) }}
                                    </fwb-table-cell>
                                    <!-- Actions -->
                                    <fwb-table-cell class="flex items-center gap-3">
                                        <button
                                            @click="showTestModal(patient.id, patient.priority_type)"
                                            class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded whitespace-nowrap hover:opacity-75"
                                        >
                                            <InboxArrowDownIcon class="size-4 text-white" />
                                        </button>

                                        <button
                                            :disabled="!patient.email"
                                            @click="openEmailAppointmentDetails(patient.email)"
                                            :class="[
                                                'text-sm font-medium px-4 py-2 rounded whitespace-nowrap text-white',
                                                !patient.email
                                                    ? 'bg-gray-400 cursor-not-allowed'
                                                    : 'bg-green-600 hover:opacity-75',
                                            ]"
                                            :title="!patient.email ? 'No email provided' : ''"
                                        >
                                            <EnvelopeIcon class="size-4 text-white" />
                                        </button>

                                        <button
                                            @click="openUpdatePatientDetails(patient)"
                                            class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded whitespace-nowrap hover:opacity-75"
                                        >
                                            <PencilSquareIcon class="size-4 text-white" />
                                        </button>

                                        <button
                                            @click="deletePatient(patient.id)"
                                            class="bg-red-600 text-white text-sm font-medium px-4 py-2 rounded whitespace-nowrap hover:opacity-75"
                                        >
                                            <TrashIcon class="size-4 text-white" />
                                        </button>
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>

                            <template v-else>
                                <fwb-table-row>
                                    <fwb-table-cell colspan="10" class="text-center bg-gray-100 text-gray-500">
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
            :priority_types="props.priority_types"
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
            :patientPriorityType="patientPriotityType"
            :testCategory="testCategory"
            @close="togglesTestModal.showTestModal = false"
            :testType="testType"
        />
        <EmailResultReminder
            v-if="showEmailAppointmentModal"
            :email="selectedEmail"
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
