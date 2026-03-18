<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { onMounted, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import PatientTestModal from '@/Components/modal/PatientTestModal.vue'
    import { formatDate, formatTime } from '@/helpers/formatter'

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
    const selectedTestStatus = ref(null)

    const openTestDialog = (patientID, testID, status) => {
        showTestDetailsDialog.value = true
        selectedPatientID.value = patientID
        selectedTestID.value = testID
        selectedTestStatus.value = status
    }

    const closeTestDialog = () => {
        showTestDetailsDialog.value = false
        selectedPatientID.value = null
        selectedTestID.value = null
    }

    const testDetailsTableHeaders = [
        'Patient Name',
        'Test Catalog',
        'Referrer Full Name',
        'Test Schedule',
        'Total Price',
        'Status',
        'Actions',
    ]
</script>

<template>
    <Head title="Laboratory Test" />

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
                        <SearchInput route="test.details.create" placeholder="Search Referrer Name" />
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
                                <fwb-table-row v-for="(test, index) in props.testDetails" :key="index">
                                    <!-- Patient Name -->
                                    <fwb-table-cell>
                                        {{
                                            test.patient
                                                ? `${test.patient.first_name} ${test.patient.last_name}`
                                                : 'N/A'
                                        }}
                                    </fwb-table-cell>

                                    <!-- Test Catalog -->
                                    <fwb-table-cell>
                                        <template
                                            v-if="
                                                test.selected_categories &&
                                                test.selected_categories.length > 0
                                            "
                                        >
                                            <div v-for="cat in test.selected_categories" :key="cat.id">
                                                {{ cat.test_category ? cat.test_category.name : '' }}
                                            </div>
                                        </template>
                                        <template v-else-if="test.test_category">
                                            <div>{{ test.test_category.name }}</div>
                                        </template>
                                        <template v-else>N/A</template>
                                    </fwb-table-cell>

                                    <!-- Referrer -->
                                    <fwb-table-cell>{{ test.referer_fullname }}</fwb-table-cell>

                                    <!-- Test Schedule -->
                                    <fwb-table-cell>
                                        {{ formatDate(test.test_schedule, false) }}
                                        <span v-if="test.test_schedule_time">
                                            {{ formatTime(test.test_schedule_time) }}
                                        </span>
                                    </fwb-table-cell>

                                    <!-- Total Price -->
                                    <fwb-table-cell>{{ test.total_price }}</fwb-table-cell>
                                    <fwb-table-cell>
                                        <span
                                            :class="{
                                                'bg-yellow-200 text-yellow-800 font-semibold px-2 py-1 rounded':
                                                    test.status === 'pending',
                                                'bg-green-200 text-green-800 font-semibold px-2 py-1 rounded':
                                                    test.status === 'completed',
                                            }"
                                        >
                                            {{ test.status.toUpperCase() }}
                                        </span>
                                    </fwb-table-cell>

                                    <!-- Actions -->
                                    <fwb-table-cell class="!text-left">
                                        <AddButton
                                            class="bg-green-600 rounded-md px-3 py-1 text-white hover:bg-green-700"
                                            @click="openTestDialog(test.patient_id, test.id, test.status)"
                                        >
                                            {{ test.status === 'completed' ? 'Edit Result' : 'Result' }}
                                        </AddButton>
                                    </fwb-table-cell>
                                </fwb-table-row>
                            </template>

                            <!-- Empty State -->
                            <template v-else>
                                <fwb-table-row>
                                    <fwb-table-cell colspan="8" class="bg-gray-100 text-gray-500 text-center">
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
            :testStatus="selectedTestStatus"
            @close="closeTestDialog"
        />
    </AuthenticatedLayout>
</template>
