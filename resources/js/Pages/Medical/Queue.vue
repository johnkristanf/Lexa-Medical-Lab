<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Column, DataTable, SelectButton } from 'primevue'
    import Popover from 'primevue/popover'
    import { ref, watch, computed } from 'vue'

    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
        FwbButton,
        FwbDropdown,
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

    // SELECTED QUEUES FOR BULK ACTIONS
    const selectedQueues = ref([])

    const selectAll = computed({
        get: () => {
            return props.queues?.length > 0 && selectedQueues.value.length === props.queues.length
        },
        set: (value) => {
            if (value) {
                selectedQueues.value = props.queues.map((q) => q.id)
            } else {
                selectedQueues.value = []
            }
        },
    })

    // CLEAR SELECTED QUEUES WHEN TAB CHANGES
    watch(
        () => props.queues,
        () => {
            selectedQueues.value = []
        },
        { deep: true },
    )

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

    const processBulk = () => {
        let updated_status_id = status.value.id + 1 // 1 -> 2, 2 -> 3

        router.put(
            route('medical.queue.update'),
            {
                queue_id: selectedQueues.value,
                status_id: updated_status_id,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Bulk status updated successfully!')
                    selectedQueues.value = [] // clear selections
                },
                onError: (errors) => {
                    console.error('Failed to update bulk status:', errors)
                },
            },
        )
    }

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

    const queueTableHeaders = ['Queue Number', 'Priority Label', 'Actions']
</script>

<template>
    <Head title="Patient Queue" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Patient Queue</h2>
        </template>

        <div class="card p-8 max-w-6xl mx-auto">
            <div class="flex items-center mb-6">
                <!-- Bulk Actions -->
                <div class="flex-1">
                    <FwbDropdown
                        v-if="selectedQueues.length > 0 && status.id < 3"
                        :text="status.id === 1 ? 'Bulk Serving' : 'Bulk Served'"
                        placement="bottom-start"
                    >
                        <ul class="py-1 text-sm text-gray-700">
                            <li
                                @click="processBulk"
                                class="cursor-pointer px-4 py-2 hover:bg-gray-100 font-medium text-green-600"
                            >
                                {{ status.id === 1 ? 'Mark as Serving' : 'Mark as Served' }} ({{
                                    selectedQueues.length
                                }})
                            </li>
                        </ul>
                    </FwbDropdown>
                </div>

                <div class="flex justify-center flex-1">
                    <SelectButton
                        v-model="status"
                        :options="props.queue_statuses"
                        optionLabel="name"
                        dataKey="id"
                        class="custom-select-button"
                    />
                </div>

                <div class="flex-1"></div>
            </div>
            <fwb-table>
                <fwb-table-head>
                    <fwb-table-head-cell class="bg-green-600 text-white w-4">
                        <div class="flex items-center justify-center">
                            <input
                                v-if="status.id < 3"
                                type="checkbox"
                                v-model="selectAll"
                                class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                            />
                        </div>
                    </fwb-table-head-cell>
                    <fwb-table-head-cell
                        v-for="(header, index) in queueTableHeaders"
                        :key="index"
                        class="bg-green-600 text-white"
                    >
                        {{ header }}
                    </fwb-table-head-cell>
                </fwb-table-head>

                <fwb-table-body>
                    <fwb-table-row v-for="queue in props.queues" :key="queue.id">
                        <fwb-table-cell class="w-4">
                            <div class="flex items-center justify-center">
                                <input
                                    v-if="status.id < 3"
                                    type="checkbox"
                                    v-model="selectedQueues"
                                    :value="queue.id"
                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                />
                            </div>
                        </fwb-table-cell>
                        <fwb-table-cell>{{ queue.queue_number }}</fwb-table-cell>
                        <fwb-table-cell>
                            {{ queue.priority_types?.name }} ({{ queue.priority_types?.code }})
                        </fwb-table-cell>

                        <fwb-table-cell class="!text-left">
                            <div v-if="queue.status_id < 3" class="flex justify-start">
                                <!-- DROPDOWN FOR STATUS UPDATE -->
                                <FwbDropdown text="Update Status" placement="bottom-start">
                                    <ul class="py-1 text-sm text-gray-700 text-left">
                                        <li
                                            v-for="queueStatus in props.queue_statuses.filter(
                                                (s) => s.id === queue.status_id + 1,
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

                            <div v-else class="flex justify-start">
                                <span
                                    class="inline-block px-2 py-1 text-sm font-bold rounded-md bg-green-100 text-green-800"
                                >
                                    Served
                                </span>
                            </div>
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
