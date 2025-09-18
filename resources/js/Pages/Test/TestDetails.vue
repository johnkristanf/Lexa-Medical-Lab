<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { onMounted, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import PatientTestModal from '@/Components/modal/PatientTestModal.vue'

    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'
    import AddButton from '@/Components/AddButton.vue'

    const props = defineProps({
        testDetails: Array,
    })

    onMounted(() => {
        console.log('testDetails: ', props.testDetails)
    })

    const showTestDetailsDialog = ref(false)
    const selectedPatientID = ref(null)
    const selectedTestID = ref(null)

    const openTestDialog = (patientID, testID) => {
        showTestDetailsDialog.value = true
        selectedPatientID.value = patientID
        selectedTestID.value = testID
    }

    const closeTestDialog = () => {
        showTestDetailsDialog.value = false
        selectedPatientID.value = null
        selectedTestID.value = null
    }

    const testDetailsTableHeaders = [
        'Referrer Full Name',
        'Doctor License No#',
        'Test Schedule',
        'Total Price',
        'Status',
        'Actions',
    ]
</script>

<template>
    <Head title="Test Details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Laboratory Test</h2>
        </template>

        <div>
            <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <!-- SEARCH INPUT -->
                        <SearchInput
                            route="test.details.create"
                            placeholder="Search Referrer Name"
                        />
                    </div>

                    <fwb-table hoverable>
                        <!-- Table Head -->
                        <fwb-table-head>
                            <fwb-table-head-cell
                                v-for="(header, index) in testDetailsTableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                            >
                                {{ header }}
                            </fwb-table-head-cell>
                        </fwb-table-head>

                        <!-- Table Body -->
                        <fwb-table-body>
                            <template v-if="props.testDetails && props.testDetails.length > 0">
                                <fwb-table-row
                                    v-for="(test, index) in props.testDetails"
                                    :key="index"
                                >
                                    <!-- Referrer -->
                                    <fwb-table-cell>{{ test.referer_fullname }}</fwb-table-cell>

                                    <!-- Doctor License -->
                                    <fwb-table-cell>{{ test.doctor_license_no }}</fwb-table-cell>

                                    <!-- Test Schedule -->
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
                                            }).format(new Date(test.test_schedule))
                                        }}
                                    </fwb-table-cell>

                                    <!-- Total Price -->
                                    <fwb-table-cell>{{ test.total_price }}</fwb-table-cell>
                                    <fwb-table-cell>{{ test.status.toUpperCase() }}</fwb-table-cell>

                                    <!-- Actions -->
                                    <fwb-table-cell class="!text-left">
                                        <AddButton
                                            class="bg-green-600 rounded-md px-3 py-1 text-white hover:bg-green-700"
                                            @click="openTestDialog(test.patient_id, test.id)"
                                        >
                                            Result
                                        </AddButton>
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>

                            <!-- Empty State -->
                            <template v-else>
                                <fwb-table-row>
                                    <fwb-table-cell colspan="3" class="bg-gray-100 text-gray-500">
                                        No test records found.
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>
                        </fwb-table-body>
                    </fwb-table>
                </div>
            </div>
        </div>

        <!-- PATIENT TEST MODAL -->
        <PatientTestModal
            v-if="showTestDetailsDialog && selectedPatientID && selectedTestID"
            :patientID="selectedPatientID"
            :testID="selectedTestID"
            @close="closeTestDialog"
        />
    </AuthenticatedLayout>
</template>
