<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { router } from '@inertiajs/vue3'
    import { formatDate } from '@/helpers/formatter'
    import { ref } from 'vue'

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // FORM DATA
    const formData = ref({
        date: '',
        timeSlots: [{ time: '', status: 'available' }],
    })

    // ADD NEW TIME SLOT
    function addTimeSlot() {
        formData.value.timeSlots.push({
            time: '',
            status: 'available',
        })
    }

    // REMOVE TIME SLOT
    function removeTimeSlot(index) {
        if (formData.value.timeSlots.length > 1) {
            formData.value.timeSlots.splice(index, 1)
        }
    }

    // FORM SUBMISSION
    function submitForm() {
        if (!formData.value.date) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Please select a date and time',
            })
            return
        }

        console.log('Submitting Schedule:', formData.value)

        router.post(
            '/add/appointment', // Adjust the route as per your backend
            formData.value,
            {
                preserveScroll: true,
                onSuccess: (page) => {
                    console.log('page', page)
                    if (page.props.flash?.success) {
                        toast.add({
                            severity: 'success',
                            summary: 'Schedule Created Successfully',
                            life: 1500,
                        })

                        setTimeout(() => {
                            closeModal()
                        }, 1500)
                    }
                },
                onError: (error) => {
                    console.log('Schedule Creation Error:', error)

                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to create schedule',
                    })
                },
            },
        )
    }
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-[999]">
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
                            class="w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900 mb-2"
                            >
                                Create Schedule
                            </DialogTitle>

                            <DialogDescription
                                class="text-sm font-medium leading-6 text-gray-400 mb-6"
                            >
                                Set appointment availability for a specific date
                            </DialogDescription>

                            <!-- FORM -->
                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- DATE TIME FIELD -->
                                <div>
                                    <label
                                        for="date"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Date
                                    </label>
                                    <input
                                        id="date"
                                        type="date"
                                        v-model="formData.date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required
                                        :min="(() => {
                                            const today = new Date();
                                            today.setHours(0,0,0,0);
                                            return today.toISOString().split('T')[0];
                                        })()"
                                    />
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Time Slots
                                        </label>
                                        <button
                                            type="button"
                                            @click="addTimeSlot"
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 border border-green-300 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        >
                                            <svg
                                                class="w-3 h-3 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 4v16m8-8H4"
                                                ></path>
                                            </svg>
                                            Add Time Slot
                                        </button>
                                    </div>

                                    <div
                                        class="space-y-3 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3 bg-gray-50"
                                    >
                                        <div
                                            v-for="(slot, index) in formData.timeSlots"
                                            :key="index"
                                            class="flex items-center space-x-3 p-3 bg-white rounded-md shadow-sm border border-gray-200"
                                        >
                                            <!-- TIME INPUT -->
                                            <div class="flex-1">
                                                <label
                                                    :for="`time-${index}`"
                                                    class="block text-xs font-medium text-gray-600 mb-1"
                                                >
                                                    Time
                                                </label>
                                                <input
                                                    :id="`time-${index}`"
                                                    type="time"
                                                    v-model="slot.time"
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                    required
                                                />
                                            </div>

                                            <!-- STATUS SELECT -->
                                            <div class="flex-1">
                                                <label
                                                    :for="`status-${index}`"
                                                    class="block text-xs font-medium text-gray-600 mb-1"
                                                >
                                                    Status
                                                </label>
                                                <select
                                                    :id="`status-${index}`"
                                                    v-model="slot.status"
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                >
                                                    <option value="available">Available</option>
                                                    <option value="unavailable">Unavailable</option>
                                                </select>
                                            </div>

                                            <!-- REMOVE BUTTON -->
                                            <div class="flex-shrink-0">
                                                <button
                                                    type="button"
                                                    @click="removeTimeSlot(index)"
                                                    :disabled="formData.timeSlots.length === 1"
                                                    class="p-1 text-red-600 hover:text-red-800 disabled:text-gray-400 disabled:cursor-not-allowed focus:outline-none"
                                                    :title="
                                                        formData.timeSlots.length === 1
                                                            ? 'Cannot remove the last time slot'
                                                            : 'Remove time slot'
                                                    "
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FORM ACTIONS -->
                                <div class="flex justify-end space-x-3 pt-4">
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <!-- TOAST FOR RESPONSE ALERT -->
    <Toast />
</template>
