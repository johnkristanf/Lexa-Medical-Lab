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

    // TOAST
    const toast = useToast()

    // Props
    const props = defineProps({
        supply: Object,
    })

    // Emits
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // Form setup
    const form = useForm({
        critical_stock: props.supply?.stocks?.[0]?.critical_stock ?? '',
    })

    // Submit handler
    function submitForm() {
        form.put(route('supply.update.critical.stock', props.supply.id), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Critical stock updated successfully',
                    life: 3000,
                })
                closeModal()
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'There was an error while updating critical stock.',
                    life: 3000,
                })
            },
        })
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
                            class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all flex flex-col"
                        >
                            <DialogTitle as="h1" class="text-2xl font-medium leading-6 text-gray-900">
                                Update Critical Stock
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Set the critical stock level for this supply
                            </DialogDescription>

                            <div class="mt-8 flex-1">
                                <form @submit.prevent="submitForm" class="w-full">
                                    <div class="w-full">
                                        <label
                                            for="critical_stock"
                                            class="block text-sm font-semibold text-gray-900"
                                        >
                                            Critical Stock
                                        </label>
                                        <input
                                            id="critical_stock"
                                            v-model="form.critical_stock"
                                            type="number"
                                            min="0"
                                            class="form-input mt-1 w-full block"
                                        />
                                        <p
                                            v-if="form.errors.critical_stock"
                                            class="text-sm text-red-500 mt-1"
                                        >
                                            {{ form.errors.critical_stock }}
                                        </p>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="mt-10">
                                        <button
                                            type="submit"
                                            :class="[
                                                'block w-full rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-white',
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
                                            class="block w-full mt-3 rounded-md bg-gray-800 hover:bg-gray-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white"
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

    <Toast />
</template>
