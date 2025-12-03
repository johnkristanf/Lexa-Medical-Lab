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

    const props = defineProps({
        categories: {
            type: Array,
            required: true,
        },
    })

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
        sku: '',
        lot_number: '',
        batch_number: generateBatchNumber(),
        category_id: '',
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
                                                Description
                                            </label>
                                            <input
                                                id="participants"
                                                v-model="form.participants"
                                                type="text"
                                                class="form-input"
                                                required
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
                                                required
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
                                                required
                                            />
                                            <p v-if="form.errors.unit" class="text-sm text-red-500 mt-1">
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
                                                required
                                            />
                                            <p v-if="form.errors.quantity" class="text-sm text-red-500 mt-1">
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
                                                for="category_id"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Category
                                            </label>
                                            <select
                                                id="category_id"
                                                v-model="form.category_id"
                                                class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base"
                                            >
                                                <option disabled value="" class="text-center">
                                                    -- Select Category --
                                                </option>
                                                <option
                                                    v-for="category in props.categories"
                                                    :key="category.id"
                                                    :value="category.id"
                                                >
                                                    {{ category.name }}
                                                </option>
                                            </select>

                                            <p
                                                v-if="form.errors.category_id"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.category_id }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="lot_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                SKU
                                            </label>
                                            <input
                                                id="lot_number"
                                                v-model="form.sku"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p v-if="form.errors.sku" class="text-sm text-red-500 mt-1">
                                                {{ form.errors.sku }}
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

                                        <div class="sm:col-span-2">
                                            <label
                                                for="lot_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Batch Number
                                            </label>
                                            <input
                                                id="batch_number"
                                                v-model="form.batch_number"
                                                type="text"
                                                class="form-input"
                                                readonly
                                            />
                                            <p
                                                v-if="form.errors.batch_number"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.batch_number }}
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
