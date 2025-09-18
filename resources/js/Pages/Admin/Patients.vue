<script setup>
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'

    import AdminLayout from '@/Layouts/AdminLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { formatDate } from '@/helpers/formatter'
    import SearchInput from '@/Components/SearchInput.vue'

    const props = defineProps({
        patients: Array,
    })

    const patientTableHeaders = [
        'Patient ID',
        'Full Name',
        'Sex',
        'Birth Date',
        'Address',
        'Contact No.',
        'Email',
    ]
</script>

<template>
    <Head title="Patients" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl mb-3 text-gray-600">Patients Information</h1>

            <!-- SEARCH INPUT -->
            <SearchInput route="admin.patients" placeholder="Search Name" />
        </div>

        <fwb-table hoverable>
            <fwb-table-head>
                <fwb-table-head-cell
                    v-for="(header, index) in patientTableHeaders"
                    :key="index"
                    class="px-4 py-2 text-sm font-semibold tracking-wide uppercase bg-green-600 text-white"
                >
                    {{ header }}
                </fwb-table-head-cell>
            </fwb-table-head>

            <fwb-table-body>
                <template v-if="patients.length > 0">
                    <fwb-table-row v-for="patient in patients" :key="patient.id">
                        <fwb-table-cell>{{ patient.patient_id }}</fwb-table-cell>
                        <fwb-table-cell>
                            {{ patient.last_name }}, {{ patient.first_name }}
                            <span v-if="patient.middle_name">{{ patient.middle_name }}</span>
                        </fwb-table-cell>
                        <fwb-table-cell>{{ patient.gender.toUpperCase() }}</fwb-table-cell>
                        <fwb-table-cell>{{ formatDate(patient.date_of_birth) }}</fwb-table-cell>
                        <fwb-table-cell>{{ patient.address }}</fwb-table-cell>
                        <fwb-table-cell>{{ patient.contact_number }}</fwb-table-cell>
                        <fwb-table-cell>{{ patient.email ?? 'N/A' }}</fwb-table-cell>
                    </fwb-table-row>
                </template>

                <template v-else>
                    <fwb-table-row>
                        <fwb-table-cell colspan="4" class="text-center bg-gray-100 text-gray-500">
                            No patient record found.
                        </fwb-table-cell>
                    </fwb-table-row>
                </template>
            </fwb-table-body>
        </fwb-table>
    </AdminLayout>
</template>
