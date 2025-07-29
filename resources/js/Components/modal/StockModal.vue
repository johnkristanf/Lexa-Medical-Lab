<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
    DialogDescription,
} from '@headlessui/vue'

import { useForm, router } from '@inertiajs/vue3'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'


// TOAST
const toast = useToast()

// Props
const props = defineProps({
 addStock: Object,
})

// Emits
const emit = defineEmits(['close'])
const closeModal = () => emit('close')

// Form setup
const form = useForm({
 quantity:'',
 critical_stock: '',
})

// Submit handler
        function submitForm() {
        form.post(route('supply.add.stock', props.addStock.id), {
            onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Stock added successfully',
                life: 3000,
            })

            // 🔄 Refresh supplies list
            },
            onError: () => {
            toast.add({
                severity: 'error',
                summary: 'There was an error while adding stock.',
                life: 3000,
            })
            },
        })
        }
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                        <DialogPanel class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                            <DialogTitle as="h1" class="text-2xl font-medium leading-6 text-gray-900">
                                Add Stock to Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add quantity to an existing supply batch
                            </DialogDescription>

                            <div class="mt-8">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div>
                                            <label for="quantity" class="block text-sm font-semibold text-gray-900">
                                                Add Quantity
                                                </label>
                                            <input
                                                id="quantity"
                                                v-model="form.quantity"
                                                type="number"
                                                class="form-input"
                                                required
                                            />
                                            <p v-if="form.errors.quantity" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.quantity }}
                                            </p>
                                        </div>

                                         <div>
                                            <label for="critical_stock" class="block text-sm font-semibold text-gray-900">
                                                Critical Stock
                                                </label>
                                            <input
                                                id="critical_stock"
                                                v-model="form.critical_stock"
                                                type="number"
                                                class="form-input"
                                                required
                                            />
                                            <p v-if="form.errors.critical_stock" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.critical_stock }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="mt-10">
                                        <button
                                            type="submit"
                                            :class="[
                                                'block w-full rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-white',
                                                form.processing ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                            Add Stock
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
