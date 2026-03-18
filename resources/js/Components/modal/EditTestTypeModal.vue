<script setup>
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'

    import { useForm } from '@inertiajs/vue3'
    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    //props
    const props = defineProps({
        testType: Object,
    })

    // INERTIA FORM INITIALIZATION
    const form = useForm({
        name: props.testType.name,
        reference_range: props.testType.reference_range || '',
        unit: props.testType.unit || '',
    })

    // FORM SUBMISSION
    function submitForm() {
        if (!form.name) {
            toast.add({
                severity: 'warn',
                summary: 'Name is required',
                detail: 'Please fill the details of the name field',
                life: 3000,
            })
            return
        }

        form.put(route('test.types.update', props.testType.id), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Update Successful',
                    detail: 'Test type updated successfully',
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
                            class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle class="text-xl font-semibold text-gray-900 mb-4">
                                Edit Test Type
                            </DialogTitle>

                            <div class="isolate space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-900">
                                        Name
                                    </label>
                                    <input id="name" v-model="form.name" type="text" class="form-input" />
                                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div>
                                    <label
                                        for="reference_range"
                                        class="block text-sm font-semibold text-gray-900"
                                    >
                                        Reference Range (optional)
                                    </label>
                                    <textarea
                                        id="reference_range"
                                        v-model="form.reference_range"
                                        rows="2"
                                        class="form-input"
                                    ></textarea>
                                </div>

                                <div>
                                    <label for="unit" class="block text-sm font-semibold text-gray-900">
                                        Unit (optional)
                                    </label>
                                    <input id="unit" v-model="form.unit" type="text" class="form-input" />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button
                                    type="button"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                                    @click="closeModal"
                                    :disabled="form.processing"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2"
                                    @click="submitForm"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
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
