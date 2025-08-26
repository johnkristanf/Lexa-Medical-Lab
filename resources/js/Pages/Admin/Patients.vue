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
    import { onMounted } from 'vue'

    const props = defineProps({
        patients: Array,
    })

    const headers = [
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
    <Head title="Appointments" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl mb-3 text-gray-600">Patients Information</h1>

            <!-- SEARCH INPUT -->

            <div class="flex gap-3">
                <div class="relative">
                    <div
                        class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                    >
                        <svg
                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 20"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                            />
                        </svg>
                    </div>
                    <input
                        type="search"
                        id="default-search"
                        class="block w-full ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                        placeholder="Search Name..."
                        required
                    />
                </div>
            </div>
        </div>

        <fwb-table hoverable>
            <fwb-table-head>
                <fwb-table-head-cell
                    v-for="(header, index) in headers"
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
