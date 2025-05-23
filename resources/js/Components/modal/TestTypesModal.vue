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
    import { ref, nextTick } from 'vue'

    // TOAST INITIALIZATION
    const toast = useToast()

    // Reference to the first input
    const nameInput = ref(null)

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    //props
    const props = defineProps({
        category: Object,
    })



    // INERTIA FORM INIATILIZATION

    const form = useForm({
        name: '',
        reference_range: '',
        unit: '',
        price: '',
        test_category_id: props.category.id,
    })


    // FORM SUBMISSION
    function submitForm() {
        form.post(route('test.types.submit'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Test Types Addition Successful',
                    life: 3000,
                })
                 form.reset()
                     nextTick(() => {
                nameInput.value?.focus()
            })
            },
        }) // replace with your actual route
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
                                Add Test Type
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add Test Type Data Here
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <p class="text-sm text-green-600 mb-4" v-if="form.wasSuccessful">
                                    Test type added successfully. You can submit another Test Type.
                                </p>
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">

                                       <div>
                                            <label
                                                for="patient_id"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Test Type Name
                                            </label>
                                            <input
                                                id="name"
                                                ref="nameInput"
                                                v-model="form.name"
                                                type="text"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.name"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.name }}
                                            </p>
                                        </div>


                                        <div>
                                            <label
                                                for="first_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Reference Range
                                            </label>
                                            <input
                                                id="reference_range"
                                                v-model="form.reference_range"
                                                type="text"
                                                 ref="nameInput"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.reference_range"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.reference_range }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="middle_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Unit (Optional)
                                            </label>
                                            <input
                                                id="unit"
                                                v-model="form.unit"
                                                type="text"
                                                 ref="nameInput"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.unit"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.unit }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="last_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Price
                                            </label>
                                            <input
                                                id="price"
                                                v-model="form.price"
                                                type="number"
                                                 ref="nameInput"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.price"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.price }}
                                            </p>
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
                                            Add Test Type
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

<style scoped>
    .form-input {
        @apply block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-green-600;
    }
</style>
