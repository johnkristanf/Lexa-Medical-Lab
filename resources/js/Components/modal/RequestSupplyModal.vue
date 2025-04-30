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
    import { ref } from 'vue'

    const toast = useToast()
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    const props = defineProps({
        supplies_dropdown_select: Array,
    })

    console.log("supplies_dropdown_select modal: ", props.supplies_dropdown_select);
    

    // Track selected supplies and quantities
    const selectedSupplies = ref([])

    // Inertia form
    const form = useForm({
        po_number: '',
        to: '',
        supplies: [], // this will hold { id, quantity } items
    })

    // Toggle selection of each supply checkbox
    function toggleSupplySelection(supplyId) {
        const exists = selectedSupplies.value.find((s) => s.id === supplyId)

        if (exists) {
            selectedSupplies.value = selectedSupplies.value.filter((s) => s.id !== supplyId)
        } else {
            selectedSupplies.value.push({ id: supplyId, quantity: 1 })
        }
    }

    // Sync to form before submit
    function submitForm() {
        form.supplies = selectedSupplies.value
        console.log("form data: ", form.data());

        form.post(route('medical.request.create'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Suppli Request Submitted!',
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
                                                class="block text-sm font-semibold text-gray-900 mb-2"
                                            >
                                                Select Medical Supplies
                                            </label>
                                            <div
                                                v-for="supply in props.supplies_dropdown_select"
                                                :key="supply.id"
                                                class="flex flex-col mb-4 border p-3 rounded-lg"
                                            >
                                                <div class="flex items-start space-x-4">
                                                    <input
                                                        type="checkbox"
                                                        :id="'supply-' + supply.id"
                                                        :value="supply.id"
                                                        @change="toggleSupplySelection(supply.id)"
                                                        :checked="
                                                            selectedSupplies.some(
                                                                (s) => s.id === supply.id,
                                                            )
                                                        "
                                                        class="mt-1"
                                                    />

                                                    <label
                                                        :for="'supply-' + supply.id"
                                                        class="text-sm text-gray-800"
                                                    >
                                                        {{ supply.brand_name }} (Available:
                                                        {{ supply.quantity }} {{ supply.unit }})
                                                    </label>
                                                </div>

                                                <!-- Quantity and Unit Inputs -->
                                                <div
                                                    v-if="
                                                        selectedSupplies.some(
                                                            (s) => s.id === supply.id,
                                                        )
                                                    "
                                                    class="mt-2 flex space-x-4 ml-6"
                                                >
                                                    <div class="w-full my-3">
                                                        <label
                                                            class="block text-xs font-medium text-gray-600"
                                                        >
                                                            Quantity
                                                        </label>
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            :max="supply.quantity"
                                                            v-model.number="
                                                                selectedSupplies.find(
                                                                    (s) => s.id === supply.id,
                                                                ).quantity
                                                            "
                                                            class="w-full form-input"
                                                            placeholder="Qty"
                                                        />
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>

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
