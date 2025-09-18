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

    // TOAST INITIALIZATION
    const toast = useToast()

    const props = defineProps({
        patientUpdate: Object,
    })


    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

//     function generateRandomString(length) {
//     const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
//     let result = '';
//     for (let i = 0; i < length; i++) {
//         const randomIndex = Math.floor(Math.random() * characters.length);
//         result += characters[randomIndex];
//     }
//     return result;
// }



    // INERTIA FORM INIATILIZATION
      const form = useForm({
             patient_id: props.patientUpdate?.patient_id || '',
            first_name: props.patientUpdate?.first_name || '',
            middle_name: props.patientUpdate?.middle_name || '',
            last_name: props.patientUpdate?.last_name || '',
            gender: props.patientUpdate?.gender || '',
            date_of_birth: props.patientUpdate?.date_of_birth || '',
            address: props.patientUpdate?.address || '',
            contact_number: props.patientUpdate?.contact_number || '',
            email: props.patientUpdate?.email || '',
        })


    // FORM SUBMISSION
      const submitForm = () => {
    if (!props.patientUpdate?.id) {

    }

    form.put(route('patient.update', props.patientUpdate.id), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Patient successfully updated!',
                life: 3000,
            })
            closeModal()
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Failed to update patient.',
                life: 3000,
            })
        }
    })
}


</script>

<template >
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
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900"
                            >
                                Update Patient Details
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Modify Patient Details below
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div>
                                        <label
                                            for="first_name"
                                            class="block text-sm font-semibold text-gray-900"
                                        >
                                            First Name
                                        </label>
                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            class="form-input"
                                            required
                                        />
                                        <p
                                            v-if="form.errors.first_name"
                                            class="text-sm text-red-500 mt-1"
                                        >
                                            {{ form.errors.first_name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="patient_id"
                                            class="block text-sm font-semibold text-gray-900"
                                        >
                                            Patient ID
                                        </label>
                                        <input
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
                                    </div>

                                    <div>
                                        <label
                                            for="middle_name"
                                            class="block text-sm font-semibold text-gray-900"
                                        >
                                            Middle Name
                                        </label>
                                        <input
                                            id="middle_name"
                                            v-model="form.middle_name"
                                            type="text"
                                            class="form-input"
                                            required
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
                                            <option value="" disabled class="text-black">Select gender</option>
                                            <option value="MALE" class="text-black">MALE</option>
                                            <option value="FEMALE" class="text-black">FEMALE</option>
                                        </select>
                                        <p
                                            v-if="form.errors.gender"
                                            class="text-sm text-red-500 mt-1"
                                        >
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
                                            required
                                        />
                                        <p
                                            v-if="form.errors.last_name"
                                            class="text-sm text-red-500 mt-1"
                                        >
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
                                            <p
                                                v-if="form.errors.address"
                                                class="text-sm text-red-500 mt-1"
                                            >
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
                                                class="form-input"
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
                                            <p
                                                v-if="form.errors.email"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.email }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-10">
                                        <button
                                            type="submit"
                                            :class="[
                                                'block w-full rounded-md  px-3.5 py-2.5 text-center text-sm font-semibold text-white ',
                                                form.processing ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                           Save
                                        </button>

                                        <button
                                            type="button"
                                            @click="closeModal"
                                            :class="[
                                                'block w-full rounded-md  px-3.5 py-2.5 mt-3  text-center text-sm  font-semibold text-white',
                                                form.processing ? 'bg-gray-400' : 'bg-gray-900 hover:bg-gray-500',
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
