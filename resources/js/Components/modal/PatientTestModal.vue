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
    } from 'flowbite-vue'

    import { useForm } from '@inertiajs/vue3'

    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { onMounted, ref } from 'vue'

    // PROPS
    const props = defineProps({
        testID: {
            type: [String, Number],
            required: true,
        },
        patientID: {
            type: [String, Number],
            required: true,
        },
    })

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // INERTIA FORM INITIALIZATION
    const form = useForm({
        test_results: [], // will hold { test_type_id, result } pairs
    })

    // FORM SUBMISSION
    function submitForm() {
        console.log('Form Data:', form)

        // Optional: do basic validation check
        if (form.test_results.length === 0) {
            toast.add({
                severity: 'warn',
                summary: 'No Test Results',
                detail: 'Please add at least one result before submitting.',
                life: 3000,
            })
            return
        }

        form.patch(route('test.update', [props.patientID, props.testID]), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Patient Test Results Updated',
                    life: 1500,
                })

                setTimeout(() => {
                    closeModal()
                }, [1500])
            },
        })
    }

    const testDetail = ref(null)

    function fetchTestById(patientID, testID) {
        axios.get(route('test.details', [patientID, testID])).then((response) => {
            console.log('response.data: ', response.data)
            testDetail.value = response.data

            // POPULATE DATA
            form.test_results = response.data.test_types.map((test) => ({
                test_type_id: test.id,
                result: test.pivot.results ?? '',
                name: test.name,
                reference_range: test.reference_range,
                unit: test.unit,
            }))
        })
    }

    // FETCH TEST DETAILS
    onMounted(() => {
        fetchTestById(props.patientID, props.testID)
    })

    const testResultsTableHeaders = ['Test Name', 'Results', 'Reference Range', 'Unit']
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-10">
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
                            class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle
                                v-if="testDetail"
                                as="h1"
                                class="font-medium leading-6 text-gray-900 flex justify-between items-center"
                            >
                                <h1 class="text-2xl">Test Result</h1>

                                <a
                                    :href="route('print.test.details', props.testID)"
                                    target="_blank"
                                    class="bg-green-600 rounded-md p-2 text-white"
                                >
                                    Print Details
                                </a>
                            </DialogTitle>

                            <DialogDescription
                                v-if="testDetail"
                                class="text-sm font-medium leading-6 text-gray-400"
                            >
                                Provide test result details below
                            </DialogDescription>

                            <div v-if="testDetail" class="mt-8 grid grid-cols-2 gap-4">
                                <div>
                                    Name: {{ testDetail.first_name }} {{ testDetail.middle_name }}
                                    {{ testDetail.last_name }}
                                </div>
                                <div>Patient ID: {{ testDetail.patient_id }}</div>
                            </div>

                            <div v-if="testDetail" class="w-full mt-8 isolate">
                                <form @submit.prevent="submitForm">
                                    <fwb-table hoverable>
                                        <!-- Table Head -->
                                        <fwb-table-head class="bg-green-600 text-white">
                                            <fwb-table-head-cell
                                                v-for="(header, index) in testResultsTableHeaders"
                                                :key="index"
                                                class="bg-green-600 text-white"
                                            >
                                                {{ header }}
                                            </fwb-table-head-cell>
                                        </fwb-table-head>

                                        <!-- Table Body -->
                                        <fwb-table-body>
                                            <fwb-table-row
                                                v-for="(test, index) in form.test_results"
                                                :key="test.test_type_id"
                                            >
                                                <!-- Test Name -->
                                                <fwb-table-cell>
                                                    {{ test.name }}
                                                </fwb-table-cell>

                                                <!-- Result Input -->
                                                <fwb-table-cell>
                                                    <input
                                                        :id="'result_' + test.test_type_id"
                                                        v-model="form.test_results[index].result"
                                                        type="text"
                                                        class="form-input w-full"
                                                    />
                                                    <p
                                                        v-if="form.errors[`test_results.${index}.result`]"
                                                        class="text-sm text-red-500 mt-1"
                                                    >
                                                        {{ form.errors[`test_results.${index}.result`] }}
                                                    </p>
                                                </fwb-table-cell>

                                                <!-- Reference Range -->
                                                <fwb-table-cell class="!text-left">
                                                    {{ test.reference_range }}
                                                </fwb-table-cell>

                                                <fwb-table-cell class="!text-left">
                                                    {{ test.unit ? test.unit : 'N/A' }}
                                                </fwb-table-cell>
                                            </fwb-table-row>
                                        </fwb-table-body>
                                    </fwb-table>

                                    <!-- Actions -->
                                    <div class="flex justify-end items-center gap-3 mt-8">
                                        <button
                                            type="button"
                                            class="bg-gray-900 rounded-md px-4 py-2 text-white"
                                            @click="closeModal"
                                        >
                                            Cancel
                                        </button>

                                        <button
                                            type="submit"
                                            class="bg-green-600 rounded-md px-4 py-2 text-white"
                                        >
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <h1 v-else class="text-2xl font-semibold text-center">Loading Test Details...</h1>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <!-- TOAST FOR RESPONSE ALERT -->
    <Toast />
</template>
