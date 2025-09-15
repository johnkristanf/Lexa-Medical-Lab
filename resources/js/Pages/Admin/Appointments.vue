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
    import AddScheduleModal from '@/Components/modal/AddScheduleModal.vue'
    import EmailAppointmentDetails from '@/Components/modal/EmailAppointmentDetails.vue'
    import AppointmentDetails from '@/Components/modal/AppointmentDetails.vue'
    import AddButton from '@/Components/AddButton.vue'
import StatusButton from '@/Components/StatusButton.vue'

    // COMPONENT PROPS
    const props = defineProps({
        appointments: Array,
        schedules: Array,
    })

    // MODAL REFS
    const showAddScheduleModal = ref(false)
    const showSchedulesModal = ref(false)

    // EMAIL DETAILS MODAL REFS
    const showEmailAppointmentModal = ref(false)
    const showAppointmentDetailsModal = ref(false)

    const selectedSchedule = ref()
    const selectedAppointmentID = ref()
    const selectedAppointmentNumber = ref()
    const selectedAppointment = ref()
    const selectedAppointmentEmail = ref()

    const openEmailAppointmentDetails = (appointment_id, appointment_number, schedule, email) => {
        showEmailAppointmentModal.value = true
        selectedAppointmentID.value = appointment_id
        selectedAppointmentNumber.value = appointment_number
        selectedSchedule.value = schedule
        selectedAppointmentEmail.value = email
    }

    const openAppointmentDetails = (appointment) => {
        selectedAppointment.value = appointment
        showAppointmentDetailsModal.value = true
    }

    function updateStatus(id, status) {
        router.put(
            `/admin/appointments/${id}/status`,
            { status },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Immediately update the status locally so UI changes
                    const appointment = props.appointments.find((a) => a.id === id)
                    if (appointment) {
                        appointment.status = status
                    }
                },
            },
        )
    }

    const handleShowAddSchedule = () => {
        showAddScheduleModal.value = true
        showSchedulesModal.value = false
    }

    const headers = ['Appointment #', 'Full Name', 'Email', 'Status', 'Schedule', 'Actions']
</script>

<template>
    <Head title="Appointments" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl mb-3 text-gray-600">Patient Appointments</h1>

            <!-- SEARCH INPUT -->

            <div class="flex gap-3">
                <AddButton @click="showSchedulesModal = true">View Schedules</AddButton>
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
            <fwb-table class="h-full">
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
                    <template v-if="appointments.length > 0">
                        <fwb-table-row v-for="appointment in appointments" :key="appointment.id">
                            <fwb-table-cell>
                                {{ appointment.appointment_number ?? 'N/A' }}
                            </fwb-table-cell>

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
                                    appointment.schedule?.date
                                        ? formatDate(appointment.schedule.date)
                                        : 'Not scheduled'
                                }}
                            </fwb-table-cell>
                            <fwb-table-cell class="flex items-center">
                                <div
                                    v-if="appointment.status == 'pending'"
                                    class="flex gap-2 items-center"
                                >
                                    <fwb-dropdown text="Status" color="green">
                                        <fwb-list-group
                                            class="w-32 text-sm text-gray-700 dark:text-gray-200"
                                        >
                                            <fwb-list-group-item
                                                class="flex justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                @click="updateStatus(appointment.id, 'arrived')"
                                            >
                                                Arrived
                                            </fwb-list-group-item>
                                        </fwb-list-group>
                                    </fwb-dropdown>

                                    <AddButton
                                        @click="
                                            openEmailAppointmentDetails(
                                                appointment.id,
                                                appointment.appointment_number,
                                                appointment.schedule.date,
                                                appointment.email,
                                            )
                                        "
                                    >
                                        Send Email
                                    </AddButton>
                                </div>

                                <div v-else>
                                    <AddButton
                                        color="green"
                                        @click="openAppointmentDetails(appointment)"
                                    >
                                        View Details
                                    </AddButton>
                                </div>
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
            @addSchedule="handleShowAddSchedule"
        />

        <AddScheduleModal v-if="showAddScheduleModal" @close="showAddScheduleModal = false" />

        <EmailAppointmentDetails
            v-if="showEmailAppointmentModal"
            :selectedAppointmentID="selectedAppointmentID"
            :selectedAppointmentNumber="selectedAppointmentNumber"
            :selectedAppointmentEmail="selectedAppointmentEmail"
            :selectedSchedule="selectedSchedule"
            @close="showEmailAppointmentModal = false"
        />

        <AppointmentDetails
            v-if="showAppointmentDetailsModal"
            :selectedAppointment="selectedAppointment"
            @close="showAppointmentDetailsModal = false"
        />
    </AdminLayout>
</template>
