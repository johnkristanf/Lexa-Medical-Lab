<script setup>
    import { formatDate } from '@/helpers/formatter'
    import { generateRandomNumberString } from '@/helpers/random_num'
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
    } from '@headlessui/vue'

    import { FwbCard } from 'flowbite-vue'
    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { router } from '@inertiajs/vue3'
    import { ref } from 'vue'

        // const props = defineProps({
        // selectedSchedule: String
        // })

        const emit = defineEmits(['close'])
        const closeModal = () => emit('close')
        const toast = useToast()
        // const schedule = props.selectedSchedule ?? new Date()
        // const appointmentNumber = ref (generateRandomNumberString(7))

        // const scheduleFormatted = formatDate(schedule)

      const sendEmail = () => {
    console.log('Sending email...') // ← Add this
    router.post(route('result.send'), {
        message: 'Your test result is ready.'
    }, {
        onSuccess: () => {
            console.log('Email sent.') // ← Add this
            toast.add({
                severity: 'success',
                summary: 'Email Sent',
                detail: 'Test result sent successfully.',
                life: 3000
            })
        },
        onError: (errors) => {
            console.error('Error sending email', errors)
        }
    })
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
                            <div class="flex flex-col items-center ">
                                <fwb-card class="w-full max-w-xl shadow-md rounded-xl">
                                    <div class="p-5 space-y-3">
                                        <h5 class="text-xl font-bold text-gray-900">
                                            Greetings for the result reminder!
                                        </h5>
                                        <p class="text-base text-gray-700">
                                            We are pleased to inform you that your medical test result is now available.
                                            <br />
                                            Kindly proceed to retrieve your result and report to work as required.
                                            <br />
                                        </p>

                                           <p class="text-base text-gray-700">
                                           Please ensure you bring a valid ID when claiming your result,
                                           <br/>
                                           instructions provided and follow any additional
                                            <br />
                                            by your employer or supervisor.
                                            <br />
                                            If you have any questions or need assistance, feel free to contact us.
                                        </p>
                                        <p class="text-base text-gray-700">Thank you.</p>
                                         <h3>Clinic Name:
                                            <br/>
                                            Lexa Medical Laboratory
                                            <br/>
                                            <p>Contact:</p>
                                            +63-917-1234-5678
                                         </h3>

                                    </div>
                                </fwb-card>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent bg-green-200 px-4 py-2 text-sm font-medium text-green-900 hover:bg-green-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2"
                                    @click="sendEmail()"
                                >
                                    Send
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
