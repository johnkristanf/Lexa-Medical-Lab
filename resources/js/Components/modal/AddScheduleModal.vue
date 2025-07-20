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
        status: 'available'
    })

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

        console.log('Submitting Schedule:', formData.value);

        router.post(
            '/add/appointment', // Adjust the route as per your backend
            formData.value,
            {
                preserveScroll: true,
                onSuccess: (page) => {
                    console.log('page', page);
                    if (page.props.flash?.success) {
                          toast.add({
                            severity: 'success',
                            summary: 'Schedule Created Successfully',
                            detail: `Schedule for ${formData.value.date} set to ${formData.value.status.toUpperCase()}`,
                        })


                        setTimeout(() => {
                            closeModal()
                        }, 2000)
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

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400 mb-6">
                                Set appointment availability for a specific date
                            </DialogDescription>

                            <!-- FORM -->
                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- DATE TIME FIELD -->
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date & Time
                                    </label>
                                    <input
                                        id="date"
                                        type="datetime-local"
                                        v-model="formData.date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required
                                    />
                                </div>

                                <!-- STATUS FIELD -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <select
                                        id="status"
                                        v-model="formData.status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    >
                                        <option value="available">Available</option>
                                        <option value="unavailable">Unavailable</option>
                                    </select>
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
                                        Create Schedule
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
