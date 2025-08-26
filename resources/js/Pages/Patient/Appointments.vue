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
    } from 'flowbite-vue'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { FwbButton } from 'flowbite-vue'
    import { ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { formatDate } from '@/helpers/formatter'
    import { router } from '@inertiajs/vue3'
    import EmailAppointmentDetails from '@/Components/modal/EmailAppointmentDetails.vue'
    import AppointmentDetails from '@/Components/modal/AppointmentDetails.vue'

    // COMPONENT PROPS
    const props = defineProps({
        appointments: Array,
        schedules: Array,
    })

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

    const headers = ['Appointment #', 'Full Name', 'Email', 'Status', 'Schedule', 'Actions']
</script>

<template>
    <Head title="Appointment" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Appointments</h2>
        </template>

        <div class="h-screen">
            <div class="h-full mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="h-full card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">
                        <SearchInput />
                    </div>

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
                                <fwb-table-row
                                    v-for="appointment in appointments"
                                    :key="appointment.id"
                                >
                                    <fwb-table-cell>
                                        {{ appointment.appointment_number ?? 'N/A' }}
                                    </fwb-table-cell>

                                    <fwb-table-cell>
                                        {{ appointment.first_name }}
                                        {{ appointment.middle_name ?? '' }}
                                        {{ appointment.last_name }}
                                    </fwb-table-cell>
                                    <fwb-table-cell>
                                        {{ appointment.email ?? 'N/A' }}
                                    </fwb-table-cell>
                                    <fwb-table-cell>
                                        <fwb-badge
                                            :type="
                                                appointment.status === 'arrived'
                                                    ? 'green'
                                                    : 'yellow'
                                            "
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
                                                color="green"
                                                @click="
                                                    openEmailAppointmentDetails(
                                                        appointment.id,
                                                        appointment.appointment_number,
                                                        appointment.schedule.schedule,
                                                        appointment.email,
                                                    )
                                                "
                                            >
                                                Send Email
                                            </fwb-button>

                                            <fwb-dropdown text="Status" color="green">
                                                <fwb-list-group
                                                    class="w-32 text-sm text-gray-700 dark:text-gray-200"
                                                >
                                                    <fwb-list-group-item
                                                        class="flex justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                        @click="
                                                            updateStatus(appointment.id, 'arrived')
                                                        "
                                                    >
                                                        Arrived
                                                    </fwb-list-group-item>
                                                </fwb-list-group>
                                            </fwb-dropdown>
                                        </div>

                                        <div v-else>
                                            <fwb-button
                                                color="green"
                                                @click="openAppointmentDetails(appointment)"
                                            >
                                                Details
                                            </fwb-button>
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
            </div>
        </div>

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
    </AuthenticatedLayout>
</template>
