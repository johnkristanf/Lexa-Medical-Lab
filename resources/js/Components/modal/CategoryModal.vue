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
    import { generateBatchNumber } from '@/helpers/batch_random_num'

    const toast = useToast()

    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    const form = useForm({
        name: '',
        description: '',
        image: null,
    })

    const handleImage = (e) => {
        const file = e.target.files[0]
        if (file) {
            form.image = file
            console.log('Image selected:', file)
            console.log('File type:', file.type)
            console.log('File size:', file.size)
        }
    }

    // FORM SUBMISSION
    function submitForm() {
        console.log('Form data before submit:', {
            name: form.name,
            description: form.description,
            image: form.image,
        })

        form.post(route('categories.store.data'), {
            forceFormData: true,
            preserveState: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Category Created Successfully',
                    life: 3000,
                })
                closeModal()
            },
            onError: (errors) => {
                console.log('Validation errors:', errors)
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
                                Create Category
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add Supplies Category Description Here
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label
                                                for="lot_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Category Name
                                            </label>
                                            <input
                                                id="names"
                                                v-model="form.name"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.name }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="batch_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Description
                                            </label>
                                            <input
                                                id="description"
                                                v-model="form.description"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.description"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.description }}
                                            </p>
                                        </div>

                                        <!-- Image Upload Section -->
                                        <div class="sm:col-span-2">
                                            <label
                                                for="image"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Category Image
                                            </label>
                                            <div class="mt-2 flex items-center gap-4">
                                                <div class="flex-1">
                                                    <label
                                                        for="image-upload"
                                                        class="flex justify-center w-full px-4 py-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-gray-400 transition-colors"
                                                    >
                                                        <div class="space-y-1 text-center">
                                                            <svg
                                                                class="mx-auto h-12 w-12 text-gray-400"
                                                                stroke="currentColor"
                                                                fill="none"
                                                                viewBox="0 0 48 48"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                />
                                                            </svg>
                                                            <div class="text-sm text-gray-600">
                                                                <span
                                                                    class="font-semibold text-green-600 hover:text-green-500"
                                                                >
                                                                    Upload a file
                                                                </span>
                                                                or drag and drop
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                PNG, JPG, GIF up to 10MB
                                                            </p>
                                                        </div>
                                                        <input
                                                            ref="fileInput"
                                                            id="image-upload"
                                                            type="file"
                                                            class="sr-only"
                                                            accept="image/*"
                                                            @change="handleImage"
                                                        />
                                                    </label>
                                                </div>
                                            </div>
                                            <p v-if="form.errors.image" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.image }}
                                            </p>
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

    <!-- TOAST FOR RESPONSE ALERT -->
    <Toast />
</template>
