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
    import {generateBatchNumber} from '@/helpers/batch_random_num'
    import { computed } from 'vue'

    // TOAST INITIALIZATION
    const toast = useToast()

    const props = defineProps({
        supplyUpdate: Object,
    })

const remainingSupply = computed(() => {
    const current = parseInt(props.supplyUpdate.quantity) || 0
    const deduct = parseInt(form.quantity) || 0
    return current - deduct
})

    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')


    const form = useForm({
        quantity:'',

    })


    // FORM SUBMISSION
   function submitForm() {
    form.put(route('supply.update', props.supplyUpdate.id), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Deducted from supply successfully',
                life: 3000,
            })

            closeModal();
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
                                Update Medical Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Update Medical Supply Data
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">

                                         <!-- Quantity to Deduct -->
                                <div>
                                    <label
                                        for="quantity"
                                        class="block text-sm font-semibold text-gray-900"
                                    >
                                        Deduct Supply Left
                                    </label>
                                   <input
                                    id="quantity"
                                    v-model="form.quantity"
                                    type="number"
                                    class="form-input"
                                    required
                                />
                                    <p
                                        v-if="form.errors.quantity"
                                        class="text-sm text-red-500 mt-1"
                                    >
                                        {{ form.errors.quantity }}
                                    </p>
                                </div>

                                  <div>
                                <label
                                    for="current_quantity"
                                    class="block text-sm font-semibold text-gray-900"
                                >
                                    Current Supplies Left
                                </label>
                               <input
                                    id="current_quantity"
                                    :value="remainingSupply"
                                    type="number"
                                    disabled
                                    class="form-input bg-gray-100 cursor-not-allowed"
                                />
                                 </div>
                                    </div>

                                    <div class="mt-10">
                                        <button
                                            type="submit"
                                            :class="[
                                                'block w-full rounded-md  px-3.5 py-2.5 text-center text-sm font-semibold text-white ',
                                                form.processing ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-500',
                                            ]"
                                            :disabled="form.processing"
                                        >
                                            Update Supply
                                        </button>

                                        <button
                                            type="button"
                                            @click="closeModal"
                                            :class="[
                                                'block w-full rounded-md  px-3.5 py-2.5 mt-3  text-center text-sm  font-semibold text-white',
                                                form.processing ? 'bg-gray-400' : 'bg-gray-900 hover:bg-gray-500',
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
