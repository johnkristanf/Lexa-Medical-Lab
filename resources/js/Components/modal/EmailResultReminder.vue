<script setup>
    import { formatDate } from '@/helpers/formatter'
    import { generateRandomNumberString } from '@/helpers/random_num'
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'

    import { FwbCard } from 'flowbite-vue'
    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { router } from '@inertiajs/vue3'
    import { ref } from 'vue'

    const props = defineProps({
        email: String,
    })

    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')
    const toast = useToast()

    const sendEmail = () => {
        console.log('Sending email...') // ← Add this
        router.post(
            route('result.send'),
            {
                email: props.email,
            },
            {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Email Sent',
                        detail: 'Test result sent successfully.',
                        life: 1500,
                    })

                    setTimeout(() => {
                        closeModal()
                    }, [1500])
                },
                onError: (errors) => {
                    console.error('Error sending email', errors)
                },
            },
        )
    }
</script>

<template>
    <TransitionRoot appear :show="true" as="template">
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
                            <!-- CENTERED CONTENT -->
                            <div class="w-full flex flex-col items-center">
                                <div
                                    class="w-full max-w-xl bg-white shadow-lg rounded-xl border-2 border-green-100 overflow-hidden"
                                >
                                    <!-- Header -->
                                    <div class="bg-green-600 text-white p-6 text-center">
                                        <div
                                            class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3"
                                        >
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                        </div>
                                        <h1 class="text-xl font-bold">Medical Test Result Available</h1>
                                        <p class="text-green-100 text-sm mt-1">Ready for Collection</p>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6 space-y-4">
                                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                                            <h5 class="text-xl font-bold text-green-800 mb-2">
                                                Greetings for the result reminder!
                                            </h5>
                                            <p class="text-green-700 leading-relaxed">
                                                We are pleased to inform you that your medical test result is
                                                now available.
                                                <br class="my-1" />
                                                Kindly proceed to retrieve your result and report to work as
                                                required.
                                            </p>
                                        </div>

                                        <div class="bg-white border border-green-200 p-4 rounded-lg">
                                            <h6 class="font-semibold text-green-800 mb-2 flex items-center">
                                                <svg
                                                    class="w-4 h-4 mr-2"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                Important Instructions
                                            </h6>
                                            <p class="text-gray-700 leading-relaxed">
                                                Please ensure you bring a valid ID when claiming your result,
                                                <br />
                                                instructions provided and follow any additional
                                                <br />
                                                by your employer or supervisor.
                                                <br />
                                                If you have any questions or need assistance, feel free to
                                                contact us.
                                            </p>
                                        </div>

                                        <div class="text-center">
                                            <p class="text-green-700 font-medium">Thank you.</p>
                                        </div>

                                        <!-- Clinic Information -->
                                        <div class="bg-green-600 text-white p-4 rounded-lg">
                                            <div class="text-center">
                                                <h3 class="text-lg font-bold mb-2">
                                                    <svg
                                                        class="w-5 h-5 inline mr-2"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1h-6a1 1 0 01-1-1V8z"
                                                            clip-rule="evenodd"
                                                        ></path>
                                                    </svg>
                                                    Lexa Medical Laboratory
                                                </h3>
                                                <div class="flex items-center justify-center text-green-100">
                                                    <svg
                                                        class="w-4 h-4 mr-2"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                                        ></path>
                                                    </svg>
                                                    <span>+63-917-1234-5678</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col justify-end gap-5">
                                <button
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white hover:opacity-75 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2"
                                    @click="sendEmail()"
                                >
                                    Send
                                </button>

                                <button
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:opacity-75 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2"
                                    @click="closeModal()"
                                >
                                    Cancel
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <Toast />
</template>
