<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

    import { Column, DataTable } from 'primevue'

    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import RequestSupplyModal from '@/Components/modal/RequestSupplyModal.vue'
    import { formatDate } from '@/helpers/formatter'
    import SearchInput from '@/Components/SearchInput.vue'
import ViewRequestedSupplyModal from '@/Components/modal/ViewRequestedSupplyModal.vue'

    const props = defineProps({
        medical_supply_requests: Array,
        supplies_dropdown_select: Array,
    })

    console.log('medical_supply_requests: ', props.medical_supply_requests)

    const modals = reactive({
        showRequestModal: false,
        showSupplyRequestedModal: false
    })

    const selectedRequestMedicalSupply = ref({});

    const handleShowRequestedMedicalSupply = (data) => {
        selectedRequestMedicalSupply.value = data;
        modals.showSupplyRequestedModal = true
    }

</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Supply Request
            </h2>
        </template>

        <!-- DATA TABLE FOR SUPPLIES -->
        <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="card p-8">
                    
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-6">
                        <fwb-button color="green" @click="modals.showRequestModal = true">
                            Request Supply
                        </fwb-button>

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
                        <Column field="status" header="Status"></Column>

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
                                <fwb-button color="green" @click="handleShowRequestedMedicalSupply(data.medical_supplies)">
                                    View Requested Supply
                                </fwb-button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- REQUEST SUPPLY MODAL -->
        <RequestSupplyModal
            v-if="modals.showRequestModal"
            :supplies_dropdown_select="supplies_dropdown_select"
            @close="modals.showRequestModal = false"
        />

        <!-- VIEW REQUESTED SUPPLY MODAL -->
        <ViewRequestedSupplyModal
            v-if="modals.showSupplyRequestedModal"
            :supplies_requested="selectedRequestMedicalSupply"
            @close="modals.showSupplyRequestedModal = false"
        />

    </AuthenticatedLayout>
</template>
