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

    const props = defineProps({
        category: Object,
    })

    // INERTIA FORM INITIALIZATION
    const form = useForm({
        name: props.category.name,
        price: props.category.price,
    })

    // FORM SUBMISSION
    function submitForm() {
        form.put(route('test.category.update', props.category.id), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Update Successful',
                    detail: 'Test category updated successfully',
                    life: 3000,
                })

                closeModal()
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
                            class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle as="h1" class="text-2xl font-medium leading-6 text-gray-900">
                                Edit Test Category
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400 mt-2">
                                Update the test category details below
                            </DialogDescription>

                            <div class="isolate mt-8">
                                <form @submit.prevent="submitForm">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label
                                                for="name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Name
                                            </label>
                                            <input
                                                id="name"
                                                v-model="form.name"
                                                type="text"
                                                class="form-input mt-1"
                                            />
                                            <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.name }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="price"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Price
                                            </label>
                                            <input
                                                id="price"
                                                v-model="form.price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="form-input mt-1"
                                            />
                                            <p v-if="form.errors.price" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.price }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end gap-3">
                                        <button
                                            type="button"
                                            @click="closeModal"
                                            class="inline-flex justify-center rounded-md border border-transparent bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                                            :disabled="form.processing"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            :class="[
                                                'inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
                                                form.processing
                                                    ? 'bg-gray-400 cursor-not-allowed'
                                                    : 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
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

<style scoped>
    .form-input {
        @apply block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-green-600;
    }
</style>
