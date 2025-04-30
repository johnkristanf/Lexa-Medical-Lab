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


    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')


    // INERTIA FORM INIATILIZATION
    const form = useForm({
        participants: '',
        brand_name: '',
        unit: '',
        quantity: '',
        manufacture_date: '',
        expiration_date: '',
        lot_number: '',
    })

    // FORM SUBMISSION
    function submitForm() {
        form.post(route('supply.add'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Medical Supply Addition Successful',
                    life: 3000,
                })

                closeModal();
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
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900"
                            >
                                Add Medical Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add Medical Supply Description Here
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div>
                                            <label
                                                for="participants"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Participants
                                            </label>
                                            <input
                                                id="participants"
                                                v-model="form.participants"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.participants"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.participants }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="brand_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Brand Name
                                            </label>
                                            <input
                                                id="brand_name"
                                                v-model="form.brand_name"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.brand_name"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.brand_name }}
                                            </p>
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
                                                v-model="form.unit"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.unit"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.unit }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="quantity"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Supplies Left
                                            </label>
                                            <input
                                                id="quantity"
                                                v-model="form.quantity"
                                                type="number"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.quantity"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.quantity }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="manufacture_date"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Manufacturing Date
                                            </label>
                                            <input
                                                id="manufacture_date"
                                                v-model="form.manufacture_date"
                                                type="date"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.manufacture_date"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.manufacture_date }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="expiration_date"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Expiration Date
                                            </label>
                                            <input
                                                id="expiration_date"
                                                v-model="form.expiration_date"
                                                type="date"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.expiration_date"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.expiration_date }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="lot_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Lot Number
                                            </label>
                                            <input
                                                id="lot_number"
                                                v-model="form.lot_number"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.lot_number"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.lot_number }}
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
                                            Add Supply
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
