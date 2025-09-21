<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

    import { Column, DataTable } from 'primevue'
    import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
    import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/20/solid'
    import { useForm } from '@inertiajs/vue3'
    import { Head } from '@inertiajs/vue3'
    import { FwbButton } from 'flowbite-vue'
    import { onMounted, reactive, ref } from 'vue'
    import RequestSupplyModal from '@/Components/modal/RequestSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import ViewRequestedSupplyModal from '@/Components/modal/ViewRequestedSupplyModal.vue'
    import { REQUEST_STATUS } from '@/Enums/Inventory'

    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
import AddButton from '@/Components/AddButton.vue'

    const props = defineProps({
        medical_supply_requests: Array,
    })

    const toast = useToast()

    // MODAL STATE HOLDER
    const modals = reactive({
        showRequestModal: false,
        showSupplyRequestedModal: false,
    })

    // SHOWING REQUESTED MEDICAL SUPPLY TOGGLER AND DATA HANDLER
    const selectedRequestMedicalSupply = ref({})

    const handleShowRequestedMedicalSupply = (data) => {
        selectedRequestMedicalSupply.value = data
        modals.showSupplyRequestedModal = true
    }

    const statuses = [{ name: 'Received', tag: 'received' }]

    // STATUS UPDATE FORM
    const form = useForm({
        request_id: 0,
        status_tag: '',
    })

    const updateStatus = (request_id, status_tag) => {
        console.log('request_id: ', request_id)
        console.log('status_tag: ', status_tag)

        form.request_id = request_id
        form.status_tag = status_tag

        console.log('form data status: ', form.data())

        form.post(route('update.request.status'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Status Updated',
                    life: 3000,
                })
            },
        })
    }

    onMounted(() => {
        console.log('medical_supply_requests: ', props.medical_supply_requests)
    })
</script>

<template>
<Head title="Supply Request" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Medical Supply Request
            </h2>
        </template>

        <!-- DATA TABLE FOR SUPPLIES -->
        <div>
            <div class="mx-auto max-w-7xl h-screen sm:px-6 lg:px-8">
                <div class="card p-8 h-full">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-6">
                        <AddButton color="green" @click="modals.showRequestModal = true">
                            Request Supply
                        </AddButton>

                        <!-- SEARCH INPUT -->
                        <SearchInput />
                    </div>

                    <DataTable
                        :value="props.medical_supply_requests"
                        class="custom-datatable"
                        scrollable
                        scrollHeight="flex"
                    >
                        <Column field="po_number" header="PO #"></Column>
                        <Column field="to" header="To"></Column>

                        <Column field="status" header="Status">
                            <template #body="{ data }">
                                <span
                                    class="inline-block px-2 py-1 text-sm font-bold uppercase rounded-md"
                                    :class="{
                                        'bg-green-100 text-green-800':
                                            data.status === REQUEST_STATUS.RECEIVED,
                                        'bg-yellow-100 text-yellow-800':
                                            data.status === REQUEST_STATUS.PENDING,
                                    }"
                                >
                                    {{ data.status }}
                                </span>
                            </template>
                        </Column>

                        <Column header="Actions">
                            <template #body="{ data }">
                                <div v-if="data.status != REQUEST_STATUS.RECEIVED" class="flex items-center gap-3">
                                    <!-- MENU DROPDOWN FOR STATUS UPDATE -->
                                    <Menu as="div" class="relative inline-block text-left">
                                        <div>
                                            <MenuButton
                                                class="inline-flex w-full justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:opacity-75 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
                                            >
                                                Update Status
                                                <ChevronDownIcon
                                                    class="-mr-1 ml-2 h-5 w-5 text-green-200 hover:text-green-100"
                                                    aria-hidden="true"
                                                />
                                            </MenuButton>
                                        </div>

                                        <transition
                                            enter-active-class="transition duration-100 ease-out"
                                            enter-from-class="transform scale-95 opacity-0"
                                            enter-to-class="transform scale-100 opacity-100"
                                            leave-active-class="transition duration-75 ease-in"
                                            leave-from-class="transform scale-100 opacity-100"
                                            leave-to-class="transform scale-95 opacity-0"
                                        >
                                            <MenuItems
                                                class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                                            >
                                                <div class="px-1 py-1">
                                                    <MenuItem
                                                        v-for="(status, index) in statuses"
                                                        :key="index"
                                                        v-slot="{ active }"
                                                    >
                                                        <button
                                                            :class="[
                                                                active
                                                                    ? 'bg-green-500 text-white'
                                                                    : 'text-gray-900',
                                                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                                            ]"
                                                            @click="
                                                                () =>
                                                                    updateStatus(
                                                                        data.id,
                                                                        status.tag,
                                                                    )
                                                            "
                                                        >
                                                            <CheckIcon
                                                                class="mr-2 h-5 w-5 text-green-400"
                                                                aria-hidden="true"
                                                            />
                                                            {{ status.name }}
                                                        </button>
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>

                                    <AddButton
                                        @click="
                                            handleShowRequestedMedicalSupply(data.requested_supply)
                                        "
                                    >
                                        View Requested Supply
                                    </AddButton>
                                </div>

                                <div v-else>
                                    <h1 class="text-green-600"></h1>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- REQUEST SUPPLY MODAL -->
        <RequestSupplyModal
            v-if="modals.showRequestModal"
            @close="modals.showRequestModal = false"
        />

        <!-- VIEW REQUESTED SUPPLY MODAL -->
        <ViewRequestedSupplyModal
            v-if="modals.showSupplyRequestedModal"
            :supplies_requested="selectedRequestMedicalSupply"
            @close="modals.showSupplyRequestedModal = false"
        />

        <Toast />
    </AuthenticatedLayout>
</template>

<!-- <style scoped>
    .p-datatable {
        background-color: green;
    }
</style> -->
