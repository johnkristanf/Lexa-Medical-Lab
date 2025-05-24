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
    import { onMounted, computed } from 'vue'

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    const props = defineProps({
        testTypesPurpose: Array,
        testTypesRequest: Array,
        patientID: Number,
        testCategory: Array,
        testType: Array,
    })

    // INERTIA FORM INIATILIZATION
    const form = useForm({
        referer_fullname: '',
        doctor_license_no: '',
        reason_for_test: '',
        test_schedule: '',
        total_price: '',
        request_id: '',
        purpose_id: '',
        patient_id: props.patientID,
        category_id: '',
        selected_test_types: [], // ← must be an array, not a string or object
    })

    // filtered by category
    const filteredTestTypes = computed(() => {
        const selectedId = form.category_id

        console.log('selectedId: ', selectedId)
        console.log('type of selectedId: ', typeof selectedId)

        if (!selectedId) return []
        const selectedCategory = props.testCategory.find((category) => category.id === selectedId)
        return selectedCategory ? selectedCategory.test_types : []
    })

    // FORM SUBMISSION
    function submitForm() {
        console.log('sa form ni:', form.data())

        console.log('filteredTestTypes: ', filteredTestTypes)

        form.post(route('test.submit'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Medical Supply Addition Successful',
                    life: 3000,
                })

                closeModal()
            },
        }) // replace with your actual route
    }

    onMounted(() => {
        console.log('sa category ni: ', props.testCategory)
        console.log('All Test Types:', props.testType)
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
                                Add Test
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Add Test Description Here
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div>
                                            <label
                                                for="patient_id"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Referer Full Name
                                            </label>
                                            <input
                                                id="referer_full_name"
                                                v-model="form.referer_fullname"
                                                type="text"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.referer_fullname"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.referer_fullname }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="first_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Doctor License No. (Optional)
                                            </label>
                                            <input
                                                id="doctor_license_no"
                                                v-model="form.doctor_license_no"
                                                type="text"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.doctor_license_no"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.doctor_license_no }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="middle_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Reason For Test
                                            </label>
                                            <input
                                                id="reason_for_test"
                                                v-model="form.reason_for_test"
                                                type="text"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.reason_for_test"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.reason_for_test }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="last_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Test Schedule
                                            </label>
                                            <input
                                                id="test_schedule"
                                                v-model="form.test_schedule"
                                                type="date"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.test_schedule"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.test_schedule }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="last_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Total Price
                                            </label>
                                            <input
                                                id="total_price"
                                                v-model="form.total_price"
                                                type="number"
                                                class="form-input"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.total_price"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.total_price }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                for="test_request"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Test Request
                                            </label>
                                            <select
                                                id="test_requestname"
                                                v-model="form.request_id"
                                                class="form-input"
                                            >
                                                <option value="" disabled>
                                                    -- Select Request Type --
                                                </option>
                                                <option
                                                    v-for="typeRequest in testTypesRequest"
                                                    :key="typeRequest.id"
                                                    :value="typeRequest.id"
                                                >
                                                    {{ typeRequest.test_requestname }}
                                                </option>
                                            </select>
                                            <p
                                                v-if="form.errors.test_requestname"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.test_requestname }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="test_purpose"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Test Purpose
                                            </label>
                                            <select
                                                id="test_purposename"
                                                v-model="form.purpose_id"
                                                class="form-input w-full"
                                            >
                                                <option value="" disabled class="text-center">
                                                    -- Select Test Purpose --
                                                </option>
                                                <option
                                                    v-for="type in testTypesPurpose"
                                                    :key="type.id"
                                                    :value="type.id"
                                                >
                                                    {{ type.test_purposename }}
                                                </option>
                                            </select>
                                            <p
                                                v-if="form.errors.test_purposename"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.test_purposename }}
                                            </p>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label
                                                for="test_purpose"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Test Category
                                            </label>
                                            <select
                                                id="test_purposename"
                                                v-model="form.category_id"
                                                class="form-input w-full"
                                            >
                                                <option value="" disabled class="text-center">
                                                    -- Select Test Category --
                                                </option>
                                                <option
                                                    v-for="type_category in testCategory"
                                                    :key="type_category.id"
                                                    :value="type_category.id"
                                                >
                                                    {{ type_category.name }}
                                                </option>
                                            </select>
                                            <p
                                                v-if="form.errors.category_id"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.category_id }}
                                            </p>
                                        </div>

                                        <!-- checkbox when selecting a category -->
                                        <div class="sm:col-span-2" v-if="filteredTestTypes.length">
                                            <div class="space-y-2 mt-2">
                                                <div
                                                    class="sm:col-span-2"
                                                    v-if="filteredTestTypes.length"
                                                >
                                                    <label
                                                        class="block text-sm font-semibold text-gray-900"
                                                    >
                                                        Select Test Type
                                                    </label>
                                                    <div class="space-y-2 mt-2">
                                                        <div
                                                            v-for="type in filteredTestTypes"
                                                            :key="type.id"
                                                        >
                                                            <label class="inline-flex items-center">
                                                                <input
                                                                    type="checkbox"
                                                                    :value="type.id"
                                                                    v-model="
                                                                        form.selected_test_types
                                                                    "
                                                                    class="form-checkbox"
                                                                />
                                                                <span
                                                                    class="ml-2 text-sm text-gray-700"
                                                                >
                                                                    {{ type.name }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                            Add Test
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

<style scoped>
    .form-input {
        @apply block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-green-600;
    }
</style>
