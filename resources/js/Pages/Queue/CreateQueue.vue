<script setup>
    import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'
    import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
    import InputLabel from '@/Components/InputLabel.vue'
    import TextInput from '@/Components/TextInput.vue'
    import GuestLayout from '@/Layouts/GuestLayout.vue'
    import { Head, useForm, router } from '@inertiajs/vue3'
    import PrimaryButton from '@/Components/PrimaryButton.vue'
    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { nextTick, ref, watch } from 'vue'
    import { formatDate } from '@/helpers/formatter'

    // TOAST INITIALIZATION
    const toast = useToast()

    const showSuccessQueueInsertion = (successMsg) => {
        toast.add({
            severity: 'success',
            summary: successMsg,
            detail: 'You may now proceed to the line',
            life: 3000,
        })
    }

    // COMPONENT QUEUE PROPS DATA
    const props = defineProps({
        priority_types: Array,
        queue_number: Number,
    })

    const responseQueueData = ref({
        queue_number: '',
        waiting_count: '',
        created_at: '',
    })

    // FORM INITIALIZATION
    const form = useForm({
        patient_name: '',
        priority_type: props.priority_types[0],
        queue_number: '',
    })

    // QUEUE NUMBER FORMAT BASED OF PRIORITY TYPE
    function formatQueueNumber(code, number) {
        return `${code}-${number}`
    }

    // WATCH THE VALUE CHANGES IN PRIORITY TYPE TO FORMAT THE QUEUE NUMBER ACCODINGLY
    watch(
        () => form.priority_type,
        (newPriorityType) => {
            console.log('DOUBLE CHECK NATO HAA...')

            console.log('newPriorityType: ', newPriorityType)

            router.get(route('queue.create'), newPriorityType, {
                preserveState: true,
                preserveScroll: true,
                only: ['queue_number'], // make sure 'queue' contains the updated queue number from backend
                onSuccess: (page) => {
                    const updatedQueueNumber = page.props.queue_number
                    console.log('updatedQueueNumber: ', updatedQueueNumber)

                    form.queue_number = formatQueueNumber(newPriorityType.code, updatedQueueNumber)
                },
            })
        },
    )

    // PRINT THE QUEUE TICKET
    const printTicket = () => {
        console.log('responseQueueData: ', responseQueueData)

        const ticketContent = document.getElementById('ticket-to-print')

        const printWindow = window.open('', '_blank', 'width=1000,height=1200')

        if (!printWindow) {
            console.error('Failed to open print window. Pop-up blocker enabled?')
            toast.add({
                severity: 'error',
                summary: 'Print Error',
                detail: 'Could not open print window. Please check if pop-up blocker is enabled.',
                life: 5000,
            })
            return
        }

        // Add print-specific CSS
        printWindow.document.write(`
                    <html>
                    <head>
                        <title>Queue Ticket</title>
                        <style>
                            @page {
                                size: 80mm 150mm; /* Standard thermal receipt size */
                                margin: 0;
                            }
                            body {
                                margin: 0;
                                padding: 8px;
                                width: 76mm; /* Slightly less than page size to account for printer margins */
                                font-family: Arial, sans-serif;
                            }
                            .ticket {
                                text-align: center;
                                padding: 5px;
                            }
                            .header {
                                font-size: 14px;
                                font-weight: bold;
                                margin-bottom: 5px;
                            }
                            .queue-number {
                                font-size: 32px;
                                font-weight: bold;
                                margin: 10px 0;
                            }
                            .info {
                                font-size: 12px;
                                margin-bottom: 5px;
                            }
                            .footer {
                                font-size: 10px;
                                margin-top: 10px;
                            }
                        </style>
                    </head>
                    <body>
                        ${ticketContent.innerHTML}
                    </body>
                    </html>
                `)

        // Wait for content to load then print
        printWindow.document.close()
        printWindow.onload = function () {
            printWindow.focus()
            printWindow.print()

            // Close after printing (or on print cancel)
            printWindow.onafterprint = function () {
                printWindow.close()
            }
        }

        // Auto-close if print dialog is canceled (timeout backup)
        setTimeout(() => {
            if (printWindow && !printWindow.closed) {
                printWindow.close()
            }
        }, 5000)
    }

    // HANDLE THE QUEUE CREATION SUBMISSION FORM
    const onSubmit = () => {
        form.post(route('queue.store'), {
            onSuccess: async (response) => {

                console.log('Full response:', response)

                const queueData = response?.props?.flash?.queueData
                const successMsg = response?.props?.flash?.success
                
                console.log('queueData: ', queueData)

                showSuccessQueueInsertion(successMsg)

                responseQueueData.value = {
                    queue_number: queueData.queue_number,
                    waiting_count: queueData.waiting_count.toString(),
                    created_at: queueData.created_at,
                }

                // CALL THE PRINT TICKET FUNCTION
                await nextTick()
                printTicket()


                // CLEAR FIELDS
                form.patient_name = ''
                form.queue_number = ''
            },
            onError: (errors) => {
                console.log('Validation errors:', errors)
            },
        })
    }
</script>

<template>
    <GuestLayout>
        <Head title="Queue Create" />

        <form @submit.prevent="onSubmit" class="flex flex-col justify-between gap-6 h-80 pt-5">
            <!-- PATIENT NAME INPUT -->
            <div>
                <InputLabel for="patient_name" value="Patient Name" />

                <TextInput
                    id="patient_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.patient_name"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <!-- LIST FOR PRIORITY TYPES -->
            <div>
                <InputLabel for="priority_type" value="Priority Type" />

                <Listbox v-model="form.priority_type">
                    <div class="relative mt-1">
                        <ListboxButton
                            class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm"
                        >
                            <span class="block truncate">{{ form.priority_type.name }}</span>
                            <span
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                            >
                                <ChevronUpDownIcon
                                    class="h-5 w-5 text-gray-400"
                                    aria-hidden="true"
                                />
                            </span>
                        </ListboxButton>

                        <transition
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <ListboxOptions
                                class="absolute mt-1 max-h-60 z-50 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                            >
                                <ListboxOption
                                    v-slot="{ active, selected }"
                                    v-for="priority in props.priority_types"
                                    :key="priority.name"
                                    :value="priority"
                                    as="template"
                                >
                                    <li
                                        :class="[
                                            active
                                                ? 'bg-green-100 text-green-900'
                                                : 'text-gray-900',
                                            'relative cursor-default select-none py-2 pl-10 pr-4',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                selected ? 'font-medium' : 'font-normal',
                                                'block truncate',
                                            ]"
                                        >
                                            {{ priority.name }}
                                        </span>
                                        <span
                                            v-if="selected"
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-green-600"
                                        >
                                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                        </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>
            </div>

            <!-- AUTOMATIC QUEUE NUMBER -->
            <div>
                <InputLabel for="queue_number" value="Queue Number" />

                <TextInput
                    id="queue_number"
                    type="text"
                    class="mt-1 block w-full opacity-75 focus:border-none focus:outline-none"
                    v-model="form.queue_number"
                    required
                    autofocus
                    readonly
                    disabled
                    autocomplete="off"
                />
            </div>

            <!-- SUBMIT BUTTON -->
            <PrimaryButton
                type="submit"
                class="w-full flex justify-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Get Queue Ticket
            </PrimaryButton>
        </form>

        <!-- SUCCESSFULL QUEUE INSERTION ALERT -->
        <Toast />

        <!-- HIDDEN QUEUE TICKET LAYOUT -->
        <div id="ticket-to-print" class="hidden">
            <div class="ticket">
                <div class="header">Welcome</div>
                <div class="subheader">Your queuing number</div>
                <div class="queue-number">{{ responseQueueData?.queue_number }}</div>
                <div class="info">
                    Currently waiting:
                    <span class="waiting-count">{{ responseQueueData?.waiting_count }}</span>
                </div>
                <div class="info date-time">{{ formatDate(responseQueueData?.created_at) }}</div>
                <div class="footer">Keep quiet and be patient please</div>
                <div class="footer">Thanks for your support</div>
            </div>
        </div>
    </GuestLayout>
</template>
