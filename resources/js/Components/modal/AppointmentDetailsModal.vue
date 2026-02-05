<script setup>
import { formatDate } from '@/helpers/formatter'
import { generateRandomNumberString } from '@/helpers/random_num'
import { router } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'

const props = defineProps({
  selectedSchedule: String
})

const emit = defineEmits(['close'])
const closeModal = () => emit('close')
const toast = useToast()

const schedule = props.selectedSchedule ?? new Date()

const sendEmail = () => {
  const appointmentNumber = generateRandomNumberString(7)
  const scheduleFormatted = formatDate(schedule)

  router.post(route('appointment.send'), {
    appointment_number: appointmentNumber,
    schedule: scheduleFormatted,
    message: 'Your appointment has been booked. Please bring your ID and vaccination card.'
  }, {
    onSuccess: () => {
      toast.add({
        severity: 'success',
        summary: 'Email Sent',
        detail: 'Static data email sent successfully.',
        life: 3000
      })
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
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900"
                            >
                                Appointment Details

                                <p class="text-gray-500 text-sm">
                                    Email confirmation for the appointed patient
                                </p>
                            </DialogTitle>

                           <div class="flex justify-between">

                                <div class="flex flex-col gap-6 mt-10">
                                    <h1 class="text-lg flex flex-col">
                                        Appointment Number:
                                        <span class="text-2xl">
                                            - {{ generateRandomNumberString(7) }}
                                        </span>
                                    </h1>

                                    <h1 class="text-lg flex flex-col">
                                        Schedule:
                                        <span class="text-2xl">
                                            - {{ formatDate(selectedSchedule) }}
                                        </span>
                                    </h1>
                                </div>

                                <!-- NOTE CARD -->
                                <fwb-card>
                                    <div class="p-5 space-y-3">
                                        <h5 class="text-xl font-bold text-gray-900">
                                            Please Read Carefully:
                                        </h5>

                                        <p class="text-sm text-gray-700">
                                            Please take note of the
                                            <strong>Appointment Number</strong>.
                                            <br />
                                            You may take a picture or screenshot of the appointment
                                            code.
                                            <br />
                                            Please bring a valid
                                            <strong>Government-issued ID</strong>
                                            and your
                                            <strong>VACCINATION CARD</strong>.
                                        </p>

                                        <h6 class="font-semibold text-gray-800">
                                            Important Reminders:
                                        </h6>
                                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                            <li>Prepare your Appointment Code upon arrival.</li>
                                            <li>Do not share your appointment code with anyone.</li>
                                            <li>Only the security guard or in-charge DRMC personnel will ask for your code.</li>
                                            <li><strong>No face mask, NO ENTRY.</strong></li>
                                            <li>Arrive at the entry point at least <strong>30 minutes before</strong> your scheduled appointment.</li>
                                            <li>Observe social distancing and always wear your face mask.</li>
                                        </ul>

                                        <p class="text-sm text-gray-700">Thank you.</p>
                                    </div>
                                </fwb-card>

                                <div class="mt-8 flex justify-end">
                                    <button
                                        type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent bg-green-200 px-4 py-2 text-sm font-medium text-green-900 hover:bg-green-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2"
                                        @click="sendEmail()"
                                    >
                                        Send
                                    </button>
                                </div>

                        </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
