<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import {
        FwbTable,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableBody,
        FwbTableRow,
        FwbTableCell,
    } from 'flowbite-vue'
    import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
    import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/20/solid'
    import { useForm, Head, router } from '@inertiajs/vue3'
    import { onMounted, reactive, ref, watch } from 'vue'
    import RequestSupplyModal from '@/Components/modal/RequestSupplyModal.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import ViewRequestedSupplyModal from '@/Components/modal/ViewRequestedSupplyModal.vue'
    import { REQUEST_STATUS } from '@/Enums/Inventory'
    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import AddButton from '@/Components/AddButton.vue'

    const props = defineProps({
        medical_supply_requests: Object,
    })

    const toast = useToast()

    const modals = reactive({
        showRequestModal: false,
        showSupplyRequestedModal: false,
    })

    const selectedRequestMedicalSupply = ref({})

    const handleShowRequestedMedicalSupply = (data) => {
        selectedRequestMedicalSupply.value = data
        modals.showSupplyRequestedModal = true
    }

    const statuses = [{ name: 'Received', tag: 'received' }]

    const form = useForm({
        request_id: 0,
        status_tag: '',
    })

    const updateStatus = (request_id, status_tag) => {
        form.request_id = request_id
        form.status_tag = status_tag
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

    const tableHeaders = ['PO #', 'To', 'Status', 'Actions']

    // Pagination + search
    const perPage = ref(props.medical_supply_requests.per_page || 10)
    const search = ref('')

    watch(perPage, (value) => {
        router.get(
            route('inventory.supply.request'),
            { perPage: value, page: 1, search: search.value },
            { preserveState: true, replace: true },
        )
    })

    function doSearch(value) {
        search.value = value
        router.get(
            route('inventory.supply.request'),
            { search: value, perPage: perPage.value },
            { preserveState: true, replace: true },
        )
    }

    function goToPage(url) {
        if (!url) return
        router.get(
            url,
            { perPage: perPage.value, search: search.value },
            { preserveState: true, replace: true },
        )
    }

    onMounted(() => {
        console.log('medical_supply_requests: ', props.medical_supply_requests)
    })
</script>

<template>
    <Head title="Supply Request" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Medical Supply Request</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl h-screen sm:px-6 lg:px-8">
                <div class="card p-8 h-full">
                    <!-- TABLE FUNCTIONS -->
                    <div class="w-full flex justify-end gap-3 mb-6">
                        <AddButton color="green" @click="modals.showRequestModal = true">
                            Request Supply
                        </AddButton>

                        <SearchInput
                            v-model="search"
                            @update:value="doSearch"
                            route="inventory.supply.request"
                            placeholder="Search Supplies"
                        />
                    </div>

                    <!-- TABLE -->
                    <FwbTable>
                        <FwbTableHead>
                            <FwbTableHeadCell
                                v-for="(header, index) in tableHeaders"
                                :key="index"
                                class="bg-green-600 text-white"
                            >
                                {{ header }}
                            </FwbTableHeadCell>
                        </FwbTableHead>

                        <FwbTableBody>
                            <FwbTableRow v-for="req in medical_supply_requests.data" :key="req.id">
                                <!-- PO Number -->
                                <FwbTableCell>{{ req.po_number }}</FwbTableCell>

                                <!-- To -->
                                <FwbTableCell>{{ req.to }}</FwbTableCell>

                                <!-- Status -->
                                <FwbTableCell>
                                    <span
                                        class="inline-block px-2 py-1 !text-left text-sm font-bold uppercase rounded-md"
                                        :class="{
                                            'bg-green-100 text-green-800':
                                                req.status === REQUEST_STATUS.RECEIVED,
                                            'bg-yellow-100 text-yellow-800':
                                                req.status === REQUEST_STATUS.PENDING,
                                        }"
                                    >
                                        {{ req.status }}
                                    </span>
                                </FwbTableCell>

                                <!-- Actions -->
                                <FwbTableCell>
                                    <div
                                        v-if="req.status !== REQUEST_STATUS.RECEIVED"
                                        class="flex !text-left items-center gap-3"
                                    >
                                        <!-- Status Menu -->
                                        <Menu as="div" class="relative inline-block text-left">
                                            <div>
                                                <MenuButton
                                                    class="inline-flex w-full justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:opacity-75 focus:outline-none"
                                                >
                                                    Update Status
                                                    <ChevronDownIcon
                                                        class="-mr-1 ml-2 h-5 w-5 text-green-200"
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
                                                                @click="updateStatus(req.id, status.tag)"
                                                            >
                                                                <CheckIcon
                                                                    class="mr-2 h-5 w-5 text-green-400"
                                                                />
                                                                {{ status.name }}
                                                            </button>
                                                        </MenuItem>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>

                                        <!-- View Requested Supply -->
                                        <button
                                            class="px-4 py-2 text-xs font-medium text-green-200 bg-green-600 rounded hover:opacity-75"
                                            @click="handleShowRequestedMedicalSupply(req.requested_supply)"
                                        >
                                            View Requested Supply
                                        </button>
                                    </div>
                                    <div v-else class="!text-left">
                                        <span class="text-green-600">Completed</span>
                                    </div>
                                </FwbTableCell>
                            </FwbTableRow>
                        </FwbTableBody>
                    </FwbTable>

                    <!-- PAGINATION WITH PER PAGE -->
                    <div
                        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4"
                    >
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between w-full">
                            <div class="flex items-center gap-4">
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ medical_supply_requests.from }}</span>
                                    to
                                    <span class="font-medium">{{ medical_supply_requests.to }}</span>
                                    of
                                    <span class="font-medium">{{ medical_supply_requests.total }}</span>
                                    results
                                </p>

                                <div class="flex items-center gap-2">
                                    <label for="perPage" class="text-sm text-gray-700">Per page:</label>
                                    <select
                                        id="perPage"
                                        v-model="perPage"
                                        class="rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500"
                                    >
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <nav
                                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                    aria-label="Pagination"
                                >
                                    <!-- Previous -->
                                    <button
                                        :disabled="!medical_supply_requests.prev_page_url"
                                        @click="goToPage(medical_supply_requests.prev_page_url)"
                                        class="relative inline-flex items-center gap-1 rounded-l-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                        :class="
                                            medical_supply_requests.prev_page_url
                                                ? 'text-gray-700 hover:bg-gray-50'
                                                : 'text-gray-400 cursor-not-allowed opacity-50'
                                        "
                                    >
                                        Previous
                                    </button>

                                    <!-- Page Numbers -->
                                    <button
                                        v-for="link in medical_supply_requests.links.slice(1, -1).slice(0, 5)"
                                        :key="link.label"
                                        @click="link.url && goToPage(link.url)"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300',
                                            link.active
                                                ? 'z-10 bg-green-600 text-white'
                                                : 'text-gray-900 hover:bg-gray-50',
                                        ]"
                                    >
                                        {{ link.label }}
                                    </button>

                                    <!-- Next -->
                                    <button
                                        :disabled="!medical_supply_requests.next_page_url"
                                        @click="goToPage(medical_supply_requests.next_page_url)"
                                        class="relative inline-flex items-center gap-1 rounded-r-md px-3 py-2 text-sm font-medium ring-1 ring-inset ring-gray-300"
                                        :class="
                                            medical_supply_requests.next_page_url
                                                ? 'text-gray-700 hover:bg-gray-50'
                                                : 'text-gray-400 cursor-not-allowed opacity-50'
                                        "
                                    >
                                        Next
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REQUEST SUPPLY MODAL -->
        <RequestSupplyModal v-if="modals.showRequestModal" @close="modals.showRequestModal = false" />

        <!-- VIEW REQUESTED SUPPLY MODAL -->
        <ViewRequestedSupplyModal
            v-if="modals.showSupplyRequestedModal"
            :supplies_requested="selectedRequestMedicalSupply"
            @close="modals.showSupplyRequestedModal = false"
        />

        <Toast />
    </AuthenticatedLayout>
</template>
