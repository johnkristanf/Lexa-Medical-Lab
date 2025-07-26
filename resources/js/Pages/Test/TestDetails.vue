<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { onMounted, reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import PatientTestModal from '@/Components/modal/PatientTestModal.vue'

    const props = defineProps({
        testDetails: Array,
    })

    const showTestDetailsDialog = ref(false)
    const selectedPatientID = ref(null)
    const selectedTestID = ref(null)

    const openTestDialog = (patientID, testID) => {
        console.log('patientID', patientID)
        console.log('testID', testID)
        showTestDetailsDialog.value = true
        selectedPatientID.value = patientID
        selectedTestID.value = testID
    }

    const closeTestDialog = () => {
        showTestDetailsDialog.value = false
        selectedPatientID.value = null
        selectedTestID.value = null
    }

    onMounted(() => {
        // Initialize any data or perform actions when the component is mounted
        console.log('props.testDetails', props.testDetails)
    })
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Test Details</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable
                        :value="props.testDetails"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="referer_fullname" header="Referrer Full Name"></Column>
                        <Column field="doctor_license_no" header="Doctor License No#"></Column>
                        <Column field="reason_for_test" header="Reason for Test"></Column>
                        <Column field="test_schedule" header="Test Schedule">
                            <template #body="slotProps">
                                {{
                                    new Intl.DateTimeFormat('en-PH', {
                                        timeZone: 'Asia/Manila',
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: true,
                                    }).format(new Date(slotProps.data.test_schedule))
                                }}
                            </template>
                        </Column>
                        <Column field="total_price" header="Total Price"></Column>
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
                                        hour12: true,
                                    }).format(new Date(slotProps.data.created_at))
                                }}
                            </template>
                        </Column>
                        <Column header="Actions">
                            <template #body="slotProps">
                                <!-- <a
                                    :href="route('print.test.details', slotProps.data.id)"
                                    target="_blank"
                                    style="
                                        background-color: green;
                                        color: white;
                                        border: none;
                                        padding: 0.2rem 0.8rem;
                                        border-radius: 4px;
                                        text-decoration: none;
                                    "
                                >
                                    Print
                                </a> -->

                                <button
                                    class="bg-green-600 rounded-md p-2 text-white"
                                    @click="
                                        openTestDialog(slotProps.data.patient_id, slotProps.data.id)
                                    "
                                >
                                    View
                                </button>
                            </template>
                        </Column>
                    </DataTable>
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
