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
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { onMounted, reactive, ref } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import { formatDate } from '@/helpers/formatter'
    import SchedulesModal from '@/Components/modal/SchedulesModal.vue'
    import AddScheduleModal from '@/Components/modal/AddScheduleModal.vue'
    import EmailAppointmentDetails from '@/Components/modal/EmailAppointmentDetails.vue'
    import { router } from '@inertiajs/vue3'


const showAddScheduleModal = ref(false)
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
                // Immediately update the status locally so UI changes
                const appointment = props.appointments.find((a) => a.id === id)
                if (appointment) {
                    appointment.status = status
                }
            },
        }
    )
}

    const handleShowAddSchedule = () => {
        console.log('Opening Add Schedule Modal')
        showAddScheduleModal.value = true;
        showSchedulesModal.value = false;
    }



</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
               Appointments
            </h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-4">

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable :value="appointments" tableStyle="min-width: 100%" class="custom-datatable">

                        <Column header="Full Name">
                    <template #body="slotProps">
                        {{ slotProps.data.first_name }} {{ slotProps.data.middle_name ?? '' }} {{ slotProps.data.last_name }}
                    </template>
                    </Column>

                    <Column header="Email">
                    <template #body="slotProps">
                        {{ slotProps.data.email ?? 'N/A' }}
                    </template>
                    </Column>

                    <Column header="Status">
                    <template #body="slotProps">
                        <fwb-badge :type="slotProps.data.status === 'arrived' ? 'green' : 'yellow'">
                        {{ slotProps.data.status.toUpperCase() }}
                        </fwb-badge>
                    </template>
                    </Column>

                    <Column header="Schedule">
                    <template #body="slotProps" >
                        {{
                        slotProps.data.schedule?.schedule
                            ? formatDate(slotProps.data.schedule.schedule)
                            : 'Not scheduled'
                        }}
                    </template>
                    </Column>

                    <Column header="Actions">
                    <template #body="slotProps">
                        <div v-if="slotProps.data.status == 'pending'" class="flex gap-2 items-center">
                        <fwb-button color="light" @click="openEmailAppointmentDetails(slotProps.data.schedule.schedule)">
                            Send Email
                        </fwb-button>

                        <fwb-dropdown text="Status" color="green">
                            <fwb-list-group class="w-32 text-sm text-gray-700 dark:text-gray-200">
                            <fwb-list-group-item
                                class="flex justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                @click="updateStatus(slotProps.data.id, 'arrived')"
                            >
                                Arrived
                            </fwb-list-group-item>
                            </fwb-list-group>
                        </fwb-dropdown>
                        </div>
                    </template>
                    </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

