<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

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

    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { router } from '@inertiajs/vue3'
    import { formatDate } from '@/helpers/formatter'

    const props = defineProps({
        schedules: Array,
    })

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // SCHEDULE STATUS UPDATE
    function updateStatus(scheduleId, status) {
        router.put(
            `/admin/appointment-schedules/${scheduleId}/status`, // Adjust the route as per your backend
            { status },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: `Status set to ${status.toUpperCase()}`,
                    })
                },
                onError: (error) => {
                    console.log('Schedule Update Error:', error)

                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to update status',
                    })
                },
            },
        )
    }
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-[999]">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900"
                            >
                                Appointment Schedules
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                List of online appointment schedules
                            </DialogDescription>

                            <div class="flex justify-end">
                                <fwb-button color="light" @click="showSchedulesModal = true">
                                    <span>Add Schedule</span>
                                </fwb-button>
                            </div>

                            <!-- TABLE LIST OF SCHEDULES -->
                            <fwb-table class="mt-5" hoverable>
                                <fwb-table-head class="bg-green-600 text-white">
                                    <fwb-table-head-cell>Schedule</fwb-table-head-cell>
                                    <fwb-table-head-cell>Status</fwb-table-head-cell>
                                    <fwb-table-head-cell>Actions</fwb-table-head-cell>
                                </fwb-table-head>

                                <fwb-table-body>
                                    <fwb-table-row v-for="schedule in schedules" :key="schedule.id">
                                        <fwb-table-cell>
                                            {{ formatDate(schedule.schedule) }}
                                        </fwb-table-cell>
                                        <fwb-table-cell>
                                            <fwb-badge
                                                :type="
                                                    schedule.status === 'available'
                                                        ? 'green'
                                                        : 'red'
                                                "
                                            >
                                                {{ schedule.status.toUpperCase() }}
                                            </fwb-badge>
                                        </fwb-table-cell>

                                        <fwb-table-cell>
                                            <fwb-dropdown text="Change Status" color="green">
                                                <fwb-list-group
                                                    class="text-sm text-gray-700 dark:text-gray-200"
                                                >
                                                    <fwb-list-group-item
                                                        v-if="schedule.status === 'available'"
                                                        class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                        @click="
                                                            updateStatus(schedule.id, 'unavailable')
                                                        "
                                                    >
                                                        UNAVAILABLE
                                                    </fwb-list-group-item>

                                                    <fwb-list-group-item
                                                        v-else
                                                        class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                        @click="
                                                            updateStatus(schedule.id, 'available')
                                                        "
                                                    >
                                                        AVAILABLE
                                                    </fwb-list-group-item>
                                                </fwb-list-group>
                                            </fwb-dropdown>
                                        </fwb-table-cell>
                                    </fwb-table-row>
                                </fwb-table-body>
                            </fwb-table>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <!-- TOAST FOR RESPONSE ALERT -->
    <Toast />
</template>
