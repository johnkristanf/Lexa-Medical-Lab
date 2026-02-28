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

    const props = defineProps({
        supply: {
            type: Object,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
        },
    })

    const toast = useToast()
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    const form = useForm({
        participants: props.supply.participants || '',
        brand_name: props.supply.brand_name || '',
        unit: props.supply.unit || '',
        manufacture_date: props.supply.manufacture_date || '',
        expiration_date: props.supply.expiration_date || '',
        lot_number: props.supply.lot_number || '',
        category_id: props.supply.category_id || '',
    })

    function submitForm() {
        form.put(route('supply.edit', props.supply.id), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Medical Supply Updated Successfully',
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
                                Edit Medical Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Modify medical supply details below. Stock/Quantity cannot be changed here.
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <!-- Item Description -->
                                        <div>
                                            <label
                                                for="participants"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Item Description
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

                                        <!-- Brand Name -->
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

                                        <!-- Unit -->
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

                                        <!-- Category -->
                                        <div>
                                            <label
                                                for="category_id"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Category
                                            </label>
                                            <select
                                                id="category_id"
                                                v-model="form.category_id"
                                                class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-[42px]"
                                                required
                                            >
                                                <option disabled value="">-- Select Category --</option>
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

                                        <!-- Manufacturing Date -->
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
                                                required
                                            />
                                            <p
                                                v-if="form.errors.manufacture_date"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.manufacture_date }}
                                            </p>
                                        </div>

                                        <!-- Expiration Date -->
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
                                                required
                                            />
                                            <p
                                                v-if="form.errors.expiration_date"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.expiration_date }}
                                            </p>
                                        </div>

                                        <!-- Lot Number -->
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
                                                'block w-full rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-white ',
                                                form.processing
                                                    ? 'bg-gray-400'
                                                    : 'bg-green-600 hover:bg-green-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                            Update Supply
                                        </button>

                                        <button
                                            type="button"
                                            @click="closeModal"
                                            class="block w-full rounded-md px-3.5 py-2.5 mt-3 text-center text-sm font-semibold text-white bg-gray-900 hover:bg-gray-500"
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
