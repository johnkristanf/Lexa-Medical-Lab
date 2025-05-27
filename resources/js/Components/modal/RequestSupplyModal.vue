<script setup>
    import { generateRandomNumberString } from '@/helpers/random_num'
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
    import { onMounted, ref } from 'vue'

    const toast = useToast()
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')


    // Track selected supplies and quantities
    const selectedSupplies = ref([])

    // Inertia form
    const form = useForm({
        po_number: '',
        to: '',
        items: [
            // array for repeater items
            {
                quantity: 0,
                unit: '',
                item_description: '',
                unit_price: 0,
                total_price: 0,
            },
        ],
    })

    const addItem = () => {
        form.items.push({
            quantity: 0,
            unit: '',
            item_description: '',
            unit_price: 0,
            total_price: 0,
        })
    }

    const removeItem = (index) => {
        form.items.splice(index, 1)
    }

    // Toggle selection of each supply checkbox
    // function toggleSupplySelection(supplyId) {
    //     const exists = selectedSupplies.value.find((s) => s.id === supplyId)

    //     if (exists) {
    //         selectedSupplies.value = selectedSupplies.value.filter((s) => s.id !== supplyId)
    //     } else {
    //         selectedSupplies.value.push({ id: supplyId, quantity: 1 })
    //     }
    // }

    // Sync to form before submit
    function submitForm() {

        form.items.forEach((item) => {
            item.total_price = item.quantity * item.unit_price
        })

        console.log('form data: ', form.data())

        form.post(route('medical.request.create'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Supply Request Submitted!',
                    life: 3000,
                })
                closeModal()
            },
        })
    }

    onMounted(() => {
        form.po_number = generateRandomNumberString(12)
    })
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
                                Request Medical Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add Request Details Here
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <!-- REQUEST FORM -->
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <!-- MEDICAL SUPPLY CHECKBOX AND INPUT QUANTITY -->

                                        <div class="sm:col-span-2">
                                            <label
                                                for="po_number"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                PO Number
                                            </label>
                                            <input
                                                id="po_number"
                                                v-model="form.po_number"
                                                type="text"
                                                class="form-input"
                                                disabled
                                                readonly
                                            />
                                            <p
                                                v-if="form.errors.po_number"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.po_number }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="to"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                To
                                            </label>
                                            <input
                                                id="to"
                                                v-model="form.to"
                                                type="text"
                                                class="form-input"
                                            />
                                            <p
                                                v-if="form.errors.to"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.to }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <h3 class="font-semibold text-gray-800 mb-2">
                                                Supply Items
                                            </h3>
                                            <div
                                                v-for="(item, index) in form.items"
                                                :key="index"
                                                class="mb-6 border p-4 rounded-lg space-y-2"
                                            >
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label
                                                            class="text-sm font-medium text-gray-700"
                                                        >
                                                            Quantity
                                                        </label>
                                                        <input
                                                            type="number"
                                                            v-model.number="item.quantity"
                                                            class="form-input"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm font-medium text-gray-700"
                                                        >
                                                            Unit
                                                        </label>
                                                        <input
                                                            type="text"
                                                            v-model="item.unit"
                                                            class="form-input"
                                                        />
                                                    </div>
                                                    <div class="col-span-2">
                                                        <label
                                                            class="text-sm font-medium text-gray-700"
                                                        >
                                                            Item Description
                                                        </label>
                                                        <input
                                                            type="text"
                                                            v-model="item.item_description"
                                                            class="form-input"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm font-medium text-gray-700"
                                                        >
                                                            Unit Price
                                                        </label>
                                                        <input
                                                            type="number"
                                                            v-model.number="item.unit_price"
                                                            class="form-input"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm font-medium text-gray-700"
                                                        >
                                                            Total Price
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-input"
                                                            :value="item.quantity * item.unit_price"
                                                            readonly
                                                        />
                                                    </div>
                                                </div>

                                                <div class="flex justify-end">
                                                    <button
                                                        type="button"
                                                        @click="removeItem(index)"
                                                        class="text-sm text-red-600 hover:underline"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>

                                            <button
                                                type="button"
                                                @click="addItem"
                                                class="text-sm text-blue-600 hover:underline mt-2"
                                            >
                                                + Add another item
                                            </button>
                                        </div>
                                    </div>

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
                                            :class="[
                                                'block w-full rounded-md px-3.5 py-2.5 mt-3 text-center text-sm font-semibold text-white',
                                                form.processing
                                                    ? 'bg-gray-400'
                                                    : 'bg-gray-900 hover:bg-gray-500 ',
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
