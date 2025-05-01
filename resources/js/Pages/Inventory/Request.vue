<script setup>
    import SearchInput from '@/Components/SearchInput.vue'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import ViewRequestedSupplyModal from '@/Components/modal/ViewRequestedSupplyModal.vue'
    import { reactive, ref } from 'vue'
    import ReleaseRequestModal from '@/Components/modal/ReleaseRequestModal.vue'
    import { formatDate } from '@/helpers/formatter'
    import { REQUEST_STATUS } from '@/Enums/Inventory'

    const props = defineProps({
        medical_supply_requests: Array,
    })

    // MODAL TOGGLERS
    const modals = reactive({
        showReleaseSupplyModal: false,
        showSupplyRequestedModal: false,
    })

    const selectedRequestMedicalSupply = ref(null)
    const selectedRequestID = ref(null)

    const handleViewSupplyRequest = (data) => {
        selectedRequestMedicalSupply.value = data
        modals.showSupplyRequestedModal = true
    }

    // Handle update supply request - similar approach
    const handleUpdateSupplyRequest = (requestID) => {
        selectedRequestID.value = requestID
        modals.showReleaseSupplyModal = true
    }
</script>

<template>
    <Head name="Inventory Request" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inventory Medical Supply Requests
            </h2>
        </template>

        <!-- DATA TABLE FOR SUPPLIES -->
        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-6">
                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable
                        :value="props.medical_supply_requests"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="po_number" header="PO #"></Column>
                        <Column field="to" header="To"></Column>

                        <Column field="status" header="Status">
                            <template #body="{ data }">
                                <span
                                    class="inline-block px-2 py-1 text-sm font-bold uppercase rounded-md"
                                    :class="{
                                        'bg-green-100 text-green-800':
                                            data.status === REQUEST_STATUS.RELEASE,
                                        'bg-yellow-100 text-yellow-800':
                                            data.status === REQUEST_STATUS.PENDING,
                                    }"
                                >
                                    {{ data.status }}
                                </span>
                            </template>
                        </Column>

                        <Column field="remarks" header="Remarks">
                            <template #body="{ data }">
                                {{ data.remarks ? data.remarks : 'N/A' }}
                            </template>
                        </Column>

                        <Column field="release_datetime" header="Release Date Time">
                            <template #body="{ data }">
                                {{
                                    data.release_datetime
                                        ? formatDate(data.release_datetime)
                                        : 'N/A'
                                }}
                            </template>
                        </Column>

                        <Column header="Actions">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-2">
                                    <div
                                        v-if="data.status === REQUEST_STATUS.RELEASE"
                                        class="text-sm text-green-900 italic"
                                    >
                                        Request Medical Supply already Released
                                    </div>

                                    <div v-else class="flex items-center gap-2">
                                        <fwb-button
                                            class="bg-gray-900 hover:bg-gray-500"
                                            @click="handleViewSupplyRequest(data.medical_supplies)"
                                        >
                                            View Requested Supply
                                        </fwb-button>

                                        <fwb-button
                                            color="green"
                                            @click="handleUpdateSupplyRequest(data.id)"
                                        >
                                            Release
                                        </fwb-button>
                                    </div>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- VIEW REQUESTED SUPPLY MODAL -->
        <ViewRequestedSupplyModal
            v-if="modals.showSupplyRequestedModal"
            :supplies_requested="selectedRequestMedicalSupply"
            @close="modals.showSupplyRequestedModal = false"
        />

        <!-- RELEASE REQUESTED SUPPLY MODAL -->
        <ReleaseRequestModal
            v-if="modals.showReleaseSupplyModal"
            :selectedRequestID="selectedRequestID"
            @close="modals.showReleaseSupplyModal = false"
        />
    </AuthenticatedLayout>
</template>
