<script setup>
    import { useForm } from '@inertiajs/vue3'
    import { useToast } from 'primevue/usetoast'

    import Toast from 'primevue/toast'

    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    const props = defineProps({
        selectedRequestID: Number,
    })


    // EMITS FOR MODAL TOGGLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // TOAST FOR RESPONSES POPUP
    const toast = useToast()

    // INERTIA FORM INIATILIZATION
    const form = useForm({
        release_datetime: '',
        remarks: '',
        request_id: props.selectedRequestID && props.selectedRequestID,
    })

    // FORM SUBMISSION
    const submitForm = () => {
        console.log('form data: ', form.data())

        form.put(route('update.supply.request'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Medical Supply Request Successfully Released',
                    life: 2300,
                })


                setTimeout(() => {
                    closeModal()
                }, 2500)

            },

            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error in Releasing Medical Request',
                    detail: 'Please try again',
                    life: 3000,
                })
            }
        }) 
    }
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild
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
                                Release Medical Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Release Details Below
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="flex flex-col gap-5">
                                        <div>
                                            <label
                                                for="release_datetime"
                                                class="block text-sm font-semibold text-gray-900 mb-1"
                                            >
                                                Release Date & Time
                                            </label>

                                            <input
                                                id="release_datetime"
                                                v-model="form.release_datetime"
                                                type="datetime-local"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.release_datetime"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.release_datetime }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="remarks"
                                                class="block text-sm font-semibold text-gray-900 mb-1"
                                            >
                                                Remarks
                                            </label>

                                            <input
                                                id="remarks"
                                                v-model="form.remarks"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.remarks"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.remarks }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-6">
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
                                            Submit
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

    <!-- RESPONSE TOAST FOMR RELEASE -->
    <Toast />
</template>

<style scoped>
    .form-input {
        @apply block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-1 focus:outline-green-600;
    }
</style>
