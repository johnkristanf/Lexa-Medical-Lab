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
    import { watch } from 'vue'

    // TOAST INITIALIZATION
    const toast = useToast()

    const showSuccessQueueInsertion = () => {
        toast.add({
            severity: 'success',
            summary: 'Queue Insertion Successful',
            detail: 'You may now proceed to the line',
            life: 3000,
        })
    }

    const props = defineProps({
        priority_types: Array,
        queue_number: Number,
    })

    // console.log('Priority Types: ', props.priority_types)
    // console.log('Queue number: ', props.queue_number)

    // PRIORITY TYPE DATA
    // const priorityTypes = [
    //     { id: 1, name: 'Person with Disability (PWD)' },
    //     { id: 2, name: 'Senior Citizen' },
    //     { id: 3, name: 'Pregnant Woman' },
    //     { id: 4, name: 'Regular Patient' },
    // ]

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
            console.log("DOUBLE CHECK NATO HAA...");
            
            console.log('newPriorityType: ', newPriorityType)

            router.get(route('queue.create'), newPriorityType, {
                preserveState: true,
                preserveScroll: true,
                only: ['queue_number'], // make sure 'queue' contains the updated queue number from backend
                onSuccess: (page) => {
                    const updatedQueueNumber = page.props.queue_number;
                    console.log("updatedQueueNumber: ", updatedQueueNumber);
                    
                    form.queue_number = formatQueueNumber(
                        newPriorityType.code,
                        updatedQueueNumber,
                    )
                },
            })
        },
    )

    // HANDLE THE QUEUE CREATION SUBMISSION FORM
    const onSubmit = () => {
        form.post(route('queue.store'), {
            onSuccess: () => {
                showSuccessQueueInsertion()

                // CLEAR FIELDS
                form.patient_name = ''
                form.queue_number = ''

                console.log('Successfully created queue!')
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
                Print Queue Ticket
            </PrimaryButton>
        </form>

        <!-- SUCCESSFULL QUEUE INSERTION ALERT -->
        <Toast />
    </GuestLayout>
</template>
