<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Column, DataTable, Drawer } from 'primevue'
    import { FwbButton } from 'flowbite-vue'
    import { reactive, ref } from 'vue'
    import AddSupplyModal from '@/Components/modal/AddSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'


    const props = defineProps({
        testDetails: Array,
        testType: Array,

    })

    const patientID = ref(null);


    // const toggles = reactive({
    //     showAddSupplyModal: false,
    //     showInventoryDrawer: false,
    // })


    //  const togglesTestModal = reactive({
    //     showTestModal: false,
    //     showInventoryDrawer: false,
    // })

    // const showTestModal = (patient_id) => {
    //     patientID.value = patient_id,
    //     togglesTestModal.showTestModal = true;
    //     console.log('sa patient ni',patientID.value);

    // }





    const sampleOperationType = 'added'
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
               Test Details
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

                    <DataTable
                        :value="props.testDetails"
                        tableStyle="min-width: 50rem"
                        class="custom-datatable"
                    >
                        <Column field="referer_fullname" header="Referrer Full Name"></Column>
                        <Column field="doctor_license_no" header="Doctor License No#"></Column>
                        <Column field="reason_for_test" header="Reason for Test"></Column>
                        <Column field="test_schedule" header="Test Schedule"></Column>
                        <Column field="total_price" header="Total Price"></Column>
                        <Column field="created_at" header="Created At">
                        <template #body="slotProps">
                            {{
                            new Intl.DateTimeFormat('en-PH', {
                                timeZone: 'Asia/Manila',
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                 hour: '2-digit',
                                 minute: '2-digit',
                                 hour12: true
                            }).format(new Date(slotProps.data.created_at))
                            }}
                        </template>
                        </Column>
                      <Column header="Actions">
                        <template #body="slotProps">
                            <a
                                :href="route('print.test.details', slotProps.data.id)"
                                target="_blank"
                                style="background-color: green; color: white; border: none; padding: 0.2rem 0.8rem; border-radius: 4px; text-decoration: none;"
                            >
                                Print
                            </a>
                        </template>
                    </Column>
                    </DataTable>

                </div>
            </div>
        </div>

        <!-- ADD SUPLY MODAL -->



        <!-- DRAWER FOR INVENTORY LOGS -->

    </AuthenticatedLayout>
</template>

