<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Column, DataTable, SelectButton } from 'primevue'
    import Popover from 'primevue/popover'
    import { ref, watch } from 'vue'

    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
        FwbButton,
        FwbDropdown
    } from 'flowbite-vue'

    // QUEUE DATA PROPS
    const props = defineProps({
        queue_statuses: Array,
        queues: Array,
    })

    console.log('Queues Status: ', props.queue_statuses[0])

    // SELECTED STATUS REFERENCE VALUE - Make sure it's a single object
    const status = ref(props.queue_statuses[0])

    // Reference for the popover
    const popoverRef = ref(null)

    // WATCH THE STATUS CHANGE ON SELECT BUTTTON
    watch(status, (newStatus) => {
        console.log('newStatus: ', newStatus)

        // Make sure we're passing a single ID
        if (newStatus && newStatus.id) {
            router.visit(route('patient.queue'), {
                method: 'get',
                data: { status_id: newStatus.id },
                preserveState: true,
                preserveScroll: true,
            })
        } else {
            console.error('Invalid status selected', newStatus)
        }
    })

    // POPOVER TOGGLE
    const toggle = (event, popoverInstance) => popoverInstance.toggle(event)

    const updateStatus = ({ queue_id, updated_status_id, popoverInstance }) => {
        console.log('queue_id: ', queue_id)
        console.log('updated_status_id: ', updated_status_id)

        // CLOSE THE POPOVER WHEN SELECTING SOMETHING
        if (popoverInstance) {
            popoverInstance.hide()
        }

        router.put(
            route('medical.queue.update'),
            {
                queue_id,
                status_id: updated_status_id,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Status updated successfully!')
                },
                onError: (errors) => {
                    console.error('Failed to update status:', errors)
                },
            },
        )
    }



    const queueTableHeaders = [
        'Queue Number',
        'Priority Label',
        'Actions'
    ]
</script>

<template>
    <Head title="Patient Queue" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Patient Queue</h2>
        </template>

        <div class="card p-8">
            <div class="flex justify-center mb-6">
                <SelectButton
                    v-model="status"
                    :options="props.queue_statuses"
                    optionLabel="name"
                    dataKey="id"
                    class="custom-select-button"
                />
            </div>
            <fwb-table>
                <fwb-table-head>
                    <fwb-table-head-cell
                        v-for="(header, index) in queueTableHeaders"
                        :key="index"
                        class="bg-green-600 text-white"
                    >
                        {{ header }}
                    </fwb-table-head-cell>
                </fwb-table-head>

                <fwb-table-body>
                    <fwb-table-row
                        v-for="queue in props.queues"
                        :key="queue.id"
                    >
                        <fwb-table-cell>{{ queue.queue_number }}</fwb-table-cell>
                        <fwb-table-cell>
                        {{ queue.priority_types?.name }} ({{ queue.priority_types?.code }})
                        </fwb-table-cell>

                        <fwb-table-cell>
                        <div v-if="queue.status_id < 3">
                            <!-- DROPDOWN FOR STATUS UPDATE -->
                            <FwbDropdown text="Update Status" placement="bottom-start">
                            <ul class="py-1 text-sm text-gray-700">
                                <li
                                v-for="queueStatus in props.queue_statuses.filter(
                                    (s) => s.id === queue.status_id + 1
                                )"
                                :key="queueStatus.id"
                                @click="
                                    updateStatus({
                                    queue_id: queue.id,
                                    updated_status_id: queueStatus.id,
                                    })
                                "
                                class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                >
                                {{ queueStatus.name }}
                                </li>
                            </ul>
                            </FwbDropdown>
                        </div>

                        <span
                            v-else
                            class="inline-block px-2 py-1 text-sm font-bold rounded-md bg-green-100 text-green-800"
                        >
                            This Patient is already Served
                        </span>
                        </fwb-table-cell>
                    </fwb-table-row>
                </fwb-table-body>
            </fwb-table>
        </div>
    </AuthenticatedLayout>
</template>

<style>
    /* Global style for all DataTable headers */
    .custom-datatable .p-datatable-thead > tr > th {
        background-color: #16a34a; /* Green color */
        color: white;
        font-weight: bold;
        padding: 1rem;
    }

    /* Optional hover effect */
    .custom-datatable .p-datatable-thead > tr > th:hover {
        opacity: 0.85;
    }


    .custom-select-button .p-selectbutton .p-button.p-highlight {
        background-color: #16a34a !important; /* Example: Tailwind green-600 */
        color: white !important;
        border-color: #15803d !important; /* Optional: dark green */
    }


</style>
