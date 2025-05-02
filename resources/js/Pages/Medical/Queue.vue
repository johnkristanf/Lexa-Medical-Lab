<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { Column, DataTable, SelectButton } from 'primevue'
    import Popover from 'primevue/popover'
    import { ref, watch } from 'vue'

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
            <DataTable :value="props.queues" tableStyle="min-width: 50rem" class="custom-datatable">
                <Column field="queue_number" header="Queue Number"></Column>
                <Column field="name" header="Name"></Column>
                <Column header="Priority Label">
                    <template #body="{ data }">
                        {{ data.priority_types?.name }} ({{ data.priority_types?.code }})
                    </template>
                </Column>

                <Column header="Priority Level">
                    <template #body="{ data }">
                        {{ data.priority_types?.priority_level }}
                    </template>
                </Column>

                <Column header="Actions">
                    <template #body="{ data }">

                        
                        <div v-if="data.status_id < 3">
                            <!-- CHANGE STATUS BUTTON -->
                            <h1
                                @click="(event) => toggle(event, $refs['popover_' + data.id])"
                                class="min-w-48 text-green-600 hover:cursor-pointer hover:underline"
                            >
                                Update Status
                            </h1>

                            <!-- POPOVER FOR CHANGING STATUS -->
                            <Popover :ref="'popover_' + data.id">
                                <div class="flex flex-col gap-4">
                                    <div>
                                        <span class="font-medium block mb-2">Queue Status</span>
                                        <ul class="list-none p-0 m-0 flex flex-col">
                                            <li
                                                v-for="queueStatus in props.queue_statuses.filter(
                                                    (s) => s.id === data.status_id + 1,
                                                )"
                                                :key="queueStatus.id"
                                                class="flex items-center gap-2 px-2 py-3 hover:bg-emphasis cursor-pointer rounded-border"
                                                @click="
                                                    updateStatus({
                                                        queue_id: data.id,
                                                        updated_status_id: queueStatus.id,
                                                        popoverInstance:
                                                            $refs['popover_' + data.id],
                                                    })
                                                "
                                            >
                                                <div>
                                                    <span class="font-medium">
                                                        {{ queueStatus.name }}
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </Popover>
                        </div>

                        <span
                            v-else
                            class="inline-block px-2 py-1 text-sm font-bold rounded-md bg-green-100 text-green-800"
                        >
                           This Patient is already Served
                        </span>
                    </template>
                </Column>
            </DataTable>
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
