<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    import { useForm } from '@inertiajs/vue3'
    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { ref, nextTick } from 'vue'

    // TOAST INITIALIZATION
    const toast = useToast()

    // Reference to the first input
    const nameInput = ref(null)

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    //props
    const props = defineProps({
        category: Object,
    })

    // Local state for current item being added
    const currentItem = ref({
        name: '',
        reference_range: '',
        unit: '',
        price: '',
    })

    // Array to hold all test types
    const testTypes = ref([])

    // INERTIA FORM INITIALIZATION
    const form = useForm({
        test_types: [],
        test_category_id: props.category.id,
    })

    // ADD ITEM TO LIST
    function addItem() {
        // Validate current item
        if (
            !currentItem.value.name ||
            !currentItem.value.reference_range ||
            !currentItem.value.unit ||
            !currentItem.value.price
        ) {
            toast.add({
                severity: 'warn',
                summary: 'Missing Fields',
                detail: 'Please fill in all fields before adding',
                life: 3000,
            })
            return
        }

        // Add to array
        testTypes.value.push({ ...currentItem.value })

        // Reset current item
        currentItem.value = {
            name: '',
            reference_range: '',
            unit: '',
            price: '',
        }

        // Focus back on name input
        nextTick(() => {
            nameInput.value?.focus()
        })

        toast.add({
            severity: 'success',
            summary: 'Item Added',
            detail: 'Test type added to list',
            life: 2000,
        })
    }

    // REMOVE ITEM FROM LIST
    function removeItem(index) {
        testTypes.value.splice(index, 1)
    }

    // FORM SUBMISSION
    function submitForm() {
        if (testTypes.value.length === 0) {
            toast.add({
                severity: 'warn',
                summary: 'No Items',
                detail: 'Please add at least one test type',
                life: 3000,
            })
            return
        }

        // Prepare arrays for submission
        form.test_types = testTypes.value.map((item) => ({
            name: item.name,
            reference_range: item.reference_range,
            unit: item.unit,
            price: item.price,
        }))

        form.post(route('test.types.submit'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Test Types Addition Successful',
                    detail: `${testTypes.value.length} test type(s) added`,
                    life: 3000,
                })
                // Reset everything
                testTypes.value = []
                currentItem.value = {
                    name: '',
                    reference_range: '',
                    unit: '',
                    price: '',
                }
                form.reset()
                nextTick(() => {
                    nameInput.value?.focus()
                })
            },
        })
    }
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
                            class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-4 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle class="text-xl font-semibold text-gray-900">
                                Add Test Types
                            </DialogTitle>
                            <DialogDescription class="text-sm text-gray-400">
                                Add multiple test types and submit them all at once
                            </DialogDescription>

                            <div class="isolate px-4 mt-4">
                                <p class="text-sm text-green-600 mb-4" v-if="form.wasSuccessful">
                                    Test types submitted successfully!
                                </p>

                                <!-- Current Item Form -->
                                <div class="w-full mx-auto space-y-4 mb-6">
                                    <h3 class="text-md font-semibold text-gray-700">Add New Test Type</h3>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                for="name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Name
                                            </label>
                                            <input
                                                id="name"
                                                ref="nameInput"
                                                v-model="currentItem.name"
                                                type="text"
                                                class="form-input"
                                            />
                                        </div>

                                        <div>
                                            <label
                                                for="reference_range"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Reference Range
                                            </label>
                                            <textarea
                                                id="reference_range"
                                                v-model="currentItem.reference_range"
                                                rows="1"
                                                class="form-input"
                                            ></textarea>
                                        </div>

                                        <div>
                                            <label
                                                for="unit"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Unit
                                            </label>
                                            <input
                                                id="unit"
                                                v-model="currentItem.unit"
                                                type="text"
                                                class="form-input"
                                            />
                                        </div>

                                        <div>
                                            <label
                                                for="price"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Price
                                            </label>
                                            <input
                                                id="price"
                                                v-model="currentItem.price"
                                                type="number"
                                                class="form-input"
                                            />
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        @click="addItem"
                                        class="w-full rounded-md px-3 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-500"
                                    >
                                        + Add to List
                                    </button>
                                </div>

                                <!-- List of Added Items -->
                                <div v-if="testTypes.length > 0" class="mb-6">
                                    <h3 class="text-md font-semibold text-gray-700 mb-3">
                                        Test Types to Submit ({{ testTypes.length }})
                                    </h3>
                                    <div class="space-y-2 max-h-64 overflow-y-auto">
                                        <div
                                            v-for="(item, index) in testTypes"
                                            :key="index"
                                            class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200"
                                        >
                                            <div class="flex-1 grid grid-cols-4 gap-2 text-sm">
                                                <div>
                                                    <span class="font-semibold text-gray-600">Name:</span>
                                                    <span class="ml-1">{{ item.name }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-600">Range:</span>
                                                    <span class="ml-1">{{ item.reference_range }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-600">Unit:</span>
                                                    <span class="ml-1">{{ item.unit }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-600">Price:</span>
                                                    <span class="ml-1">{{ item.price }}</span>
                                                </div>
                                            </div>
                                            <button
                                                @click="removeItem(index)"
                                                class="ml-4 text-red-600 hover:text-red-800 font-semibold"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="space-y-2">
                                    <button
                                        type="button"
                                        @click="submitForm"
                                        :class="[
                                            'w-full rounded-md px-3 py-2 text-sm font-semibold text-white',
                                            form.processing || testTypes.length === 0
                                                ? 'bg-gray-400'
                                                : 'bg-green-600 hover:bg-green-500',
                                        ]"
                                        :disabled="form.processing || testTypes.length === 0"
                                    >
                                        Submit All Test Types ({{ testTypes.length }})
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        :class="[
                                            'w-full rounded-md px-3 py-2 text-sm font-semibold text-white',
                                            form.processing ? 'bg-gray-400' : 'bg-gray-900 hover:bg-gray-700',
                                        ]"
                                        :disabled="form.processing"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <!-- TOAST FOR RESPONSE ALERT -->
    <Toast />
</template>

<style scoped>
    .form-input {
        @apply block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-green-600;
    }
</style>
