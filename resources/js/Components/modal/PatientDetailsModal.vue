<script setup>
    import { generateRandomNumberString } from '@/helpers/random_num'
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
    import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'
    import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
    import { computed, onMounted, watch } from 'vue'

    const props = defineProps({
        priority_types: {
            type: Array,
            default: () => [],
        },
    })

    onMounted(() => {
        console.log('PRIORITY TYPES SA MODAL:: ', props.priority_types)
    })

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // INERTIA FORM INIATILIZATION
    const form = useForm({
        patient_id: props.flash?.patient_id ?? '',
        first_name: '',
        middle_name: '',
        last_name: '',
        gender: '',
        date_of_birth: '',
        address: '',
        contact_number: '',
        email: '',
        priority_type: null,
    })

    // Computed available priority types based on gender
    const filteredPriorityTypes = computed(() => {
        if (form.gender === 'MALE') {
            // Filter out 'Pregnant Women' (code: PW)
            return props.priority_types.filter((pt) => pt.code !== 'PW')
        }
        return props.priority_types
    })

    // Watch for gender changes and auto reset priority_type if needed
    watch(
        () => form.gender,
        (newGender) => {
            if (newGender === 'MALE' && form.priority_type && form.priority_type.code === 'PW') {
                // Set to null if currently set to Pregnant Women (PW)
                form.priority_type = null
            }
        },
    )

    // Remove the second watch as it might auto-select a value

    // FORM SUBMISSION
    function submitForm() {
        console.log('Submitting form with values:', { ...form })
        form.post(route('patient.details.submit'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Patient Added Successful',
                    life: 3000,
                })

                closeModal()
            },
        }) // replace with your actual route
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
                            class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle as="h1" class="text-2xl font-medium leading-6 text-gray-900">
                                Add Patient Details
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add Patient Details Description Here
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label
                                                for="first_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                First Name
                                            </label>
                                            <input
                                                id="address"
                                                v-model="form.first_name"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.first_name"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.first_name }}
                                            </p>
                                        </div>

                                        <!-- <div>
                                            <label
                                                for="patient_id"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Patient ID
                                            </label>
                                            <input
                                                randomly
                                                disabled
                                                id="patient_id"
                                                v-model="form.patient_id"
                                                type="text"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.patient_id"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.patient_id }}
                                            </p>
                                        </div> -->

                                        <div>
                                            <label
                                                for="middle_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Middle Name (optional)
                                            </label>
                                            <input
                                                id="middle_name"
                                                v-model="form.middle_name"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.middle_name"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.middle_name }}
                                            </p>
                                        </div>

                                        <!-- GENDER moved here -->
                                        <div>
                                            <label
                                                for="gender"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Sex
                                            </label>
                                            <select
                                                id="gender"
                                                v-model="form.gender"
                                                class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-black"
                                            >
                                                <option value="" disabled class="text-black">-----</option>
                                                <option value="MALE" class="text-black">MALE</option>
                                                <option value="FEMALE" class="text-black">FEMALE</option>
                                            </select>
                                            <p v-if="form.errors.gender" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.gender }}
                                            </p>
                                        </div>

                                        <!-- LAST NAME moved after gender -->
                                        <div>
                                            <label
                                                for="last_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Last Name
                                            </label>
                                            <input
                                                id="last_name"
                                                v-model="form.last_name"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p v-if="form.errors.last_name" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.last_name }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="date_of_birth"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Date of Birth
                                            </label>
                                            <input
                                                id="date_of_birth"
                                                v-model="form.date_of_birth"
                                                type="date"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.date_of_birth"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.date_of_birth }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="address"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Address
                                            </label>
                                            <input
                                                id="address"
                                                v-model="form.address"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p v-if="form.errors.address" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.address }}
                                            </p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                for="contact_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Contact Number
                                            </label>

                                            <input
                                                id="contact_number"
                                                v-model="form.contact_number"
                                                type="text"
                                                inputmode="numeric"
                                                pattern="[0-9]*"
                                                maxlength="11"
                                                placeholder="09XXXXXXXXX"
                                                class="form-input"
                                                @input="
                                                    form.contact_number = form.contact_number
                                                        .replace(/\D/g, '')
                                                        .slice(0, 11)
                                                "
                                            />

                                            <p
                                                v-if="form.errors.contact_number"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.contact_number }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="contact_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Email
                                            </label>
                                            <input
                                                id="email"
                                                v-model="form.email"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.email }}
                                            </p>
                                        </div>

                                        <!-- LIST OF PATIENT TYPES -->
                                        <div class="sm:col-span-2">
                                            <label
                                                for="contact_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Patient Type
                                            </label>

                                            <Listbox v-model="form.priority_type">
                                                <div class="relative mt-1">
                                                    <ListboxButton
                                                        class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm"
                                                    >
                                                        <span class="block truncate">
                                                            {{
                                                                form.priority_type
                                                                    ? form.priority_type.name
                                                                    : '-----'
                                                            }}
                                                        </span>
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
                                                                v-for="priority in filteredPriorityTypes"
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
                                                                            selected
                                                                                ? 'font-medium'
                                                                                : 'font-normal',
                                                                            'block truncate',
                                                                        ]"
                                                                    >
                                                                        {{ priority.name }}
                                                                    </span>
                                                                    <span
                                                                        v-if="selected"
                                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-green-600"
                                                                    >
                                                                        <CheckIcon
                                                                            class="h-5 w-5"
                                                                            aria-hidden="true"
                                                                        />
                                                                    </span>
                                                                </li>
                                                            </ListboxOption>
                                                        </ListboxOptions>
                                                    </transition>
                                                </div>
                                            </Listbox>
                                        </div>
                                    </div>

                                    <div class="mt-10">
                                        <button
                                            type="submit"
                                            :class="[
                                                'block w-full rounded-md  px-3.5 py-2.5 text-center text-sm font-semibold text-white ',
                                                form.processing
                                                    ? 'bg-gray-400'
                                                    : 'bg-green-600 hover:bg-green-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                            Add Patient
                                        </button>

                                        <button
                                            type="button"
                                            @click="closeModal"
                                            :class="[
                                                'block w-full rounded-md  px-3.5 py-2.5 mt-3  text-center text-sm  font-semibold text-white',
                                                form.processing
                                                    ? 'bg-gray-400'
                                                    : 'bg-gray-900 hover:bg-gray-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
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
