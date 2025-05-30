<script setup>
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
        FwbBadge,
        FwbDropdown,
        FwbListGroup,
        FwbListGroupItem,
        FwbButton,
    } from 'flowbite-vue'

    import AdminLayout from '@/Layouts/AdminLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { ref } from 'vue'
    import { formatDate } from '@/helpers/formatter'
    import SchedulesModal from '@/Components/modal/SchedulesModal.vue'
    import EmailAppointmentDetails from '@/Components/modal/EmailAppointmentDetails.vue'

    const showSchedulesModal = ref(false)

    // EMAIL DETAILS MODAL REFS
    const showEmailAppointmentModal = ref(false)
    const selectedSchedule = ref()

    const openEmailAppointmentDetails = (schedule) => {
        showEmailAppointmentModal.value = true
        selectedSchedule.value = schedule
    }

    const props = defineProps({
        appointments: Array,
        schedules: Array,
    })

    function updateStatus(id, status) {
        router.put(
            `/admin/appointments/${id}/status`,
            { status },
            {
                preserveScroll: true,
                onSuccess: () => {
                    console.log(`Updated appointment ${id} to ${status}`)
                },
            },
        )
    }
</script>

<template>
    <Head title="Appointments" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl mb-3 text-gray-600">Patient Appointments</h1>

            <!-- SEARCH INPUT -->

            <div class="flex gap-3">
                <fwb-button color="green" @click="showSchedulesModal = true">
                    View Schedules
                </fwb-button>
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
                        placeholder="Search Name, Schedule..."
                        required
                    />
                </div>
            </div>
        </div>

        <div class="h-screen">
            <fwb-table class="h-full" hoverable>
                <fwb-table-head class="bg-green-600 text-white">
                    <fwb-table-head-cell>Full Name</fwb-table-head-cell>
                    <fwb-table-head-cell>Email</fwb-table-head-cell>
                    <fwb-table-head-cell>Status</fwb-table-head-cell>
                    <fwb-table-head-cell>Schedule</fwb-table-head-cell>
                    <fwb-table-head-cell>Actions</fwb-table-head-cell>
                </fwb-table-head>

                <fwb-table-body>
                    <template v-if="appointments.length > 0">
                        <fwb-table-row v-for="appointment in appointments" :key="appointment.id">
                            <fwb-table-cell>
                                {{ appointment.first_name }} {{ appointment.middle_name ?? '' }}
                                {{ appointment.last_name }}
                            </fwb-table-cell>
                            <fwb-table-cell>{{ appointment.email ?? 'N/A' }}</fwb-table-cell>
                            <fwb-table-cell>
                                <fwb-badge
                                    :type="appointment.status === 'arrived' ? 'green' : 'yellow'"
                                >
                                    {{ appointment.status.toUpperCase() }}
                                </fwb-badge>
                            </fwb-table-cell>
                            <fwb-table-cell>
                                {{
                                    appointment.schedule?.schedule
                                        ? formatDate(appointment.schedule.schedule)
                                        : 'Not scheduled'
                                }}
                            </fwb-table-cell>
                            <fwb-table-cell class="flex items-center">
                                <div
                                    v-if="appointment.status == 'pending'"
                                    class="flex gap-2 items-center"
                                >
                                    <fwb-button
                                        color="light"
                                        @click="
                                            openEmailAppointmentDetails(
                                                appointment.schedule.schedule,
                                            )
                                        "
                                    >
                                        Send Email
                                    </fwb-button>

                                    <fwb-dropdown text="Status" color="green">
                                        <fwb-list-group
                                            class="w-32 text-sm text-gray-700  dark:text-gray-200"
                                        >
                                            <fwb-list-group-item
                                                class="flex justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                @click="updateStatus(appointment.id, 'arrived')"
                                            >
                                                Arrived
                                            </fwb-list-group-item>
                                        </fwb-list-group>
                                    </fwb-dropdown>
                                </div>
                                <h1 class="text-green-600 text-center" v-else>Patient Arrived</h1>
                            </fwb-table-cell>
                        </fwb-table-row>
                    </template>

                    <!-- NO APPOINTMENTS -->
                    <template v-else>
                        <fwb-table-row>
                            <fwb-table-cell colspan="3" class="text-center text-gray-500">
                                No appointments found.
                            </fwb-table-cell>
                        </fwb-table-row>
                    </template>
                </fwb-table-body>
            </fwb-table>
        </div>

        <SchedulesModal
            v-if="showSchedulesModal"
            :schedules="schedules"
            @close="showSchedulesModal = false"
        />

        <EmailAppointmentDetails
            v-if="showEmailAppointmentModal"
            :selectedSchedule="selectedSchedule"
            @close="showEmailAppointmentModal = false"
        />
    </AdminLayout>
</template>
