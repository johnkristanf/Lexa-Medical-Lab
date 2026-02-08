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
        // testTypesRequest: Array,
        patientID: Number,
        testCategory: Array,
        testType: Array,
    })

    // INERTIA FORM INIATILIZATION
    const form = useForm({
        referer_fullname: '',
        doctor_license_no: '',
        test_schedule: '',
        total_price: '',
        purpose_id: '',
        patient_id: props.patientID,
        category_id: '',
        selected_test_types: [],
    })

    // filtered by category
    const filteredTestTypes = computed(() => {
        const selectedId = form.category_id
        if (!selectedId) return []
        const selectedCategory = props.testCategory.find((category) => category.id === selectedId)
        return selectedCategory ? selectedCategory.test_types : []
    })

    const selectedCategory = computed(() => {
        if (!form.category_id) return null
        return props.testCategory.find((category) => category.id === form.category_id) || null
    })

    // Total price: base price from selected category (once when at least one test type selected), with 20% discount if eligible
    const totalPrice = computed(() => {
        const discountEligible = props.patientPriorityType && discountedCode.includes(props.patientPriorityType.code)
        const category = selectedCategory.value
        const hasSelection = form.selected_test_types.length > 0
        if (!category || !hasSelection) return '0.00'
        let price = Number(category.price || 0)
        if (discountEligible) {
            price = price * 0.8 // Apply 20% discount
        }
        return price.toFixed(2)
    })

    const isDiscounted = computed(() =>
        props.patientPriorityType && discountedCode.includes(props.patientPriorityType.code),
    )

    const allSelectedInCategory = computed(() => {
        const types = filteredTestTypes.value
        if (!types.length) return false
        const selected = new Set(form.selected_test_types)
        return types.every((t) => selected.has(t.id))
    })

    function toggleSelectAll() {
        const ids = filteredTestTypes.value.map((t) => t.id)
        if (!ids.length) return
        if (allSelectedInCategory.value) {
            form.selected_test_types = form.selected_test_types.filter((id) => !ids.includes(id))
        } else {
            const combined = new Set([...form.selected_test_types, ...ids])
            form.selected_test_types = [...combined]
        }
    }

    function submitForm() {
        if (form.selected_test_types.length === 0) {
            toast.add({
                severity: 'warn',
                summary: 'Please select at least one test type.',
                life: 3000,
            })
            closeModal()
            return // <- Don't proceed if no test types selected
        }

        form.selected_test_types = form.selected_test_types.map(Number)
        form.total_price = totalPrice.value

        console.log('Submitting form data:', form.data())
        form.post(route('test.submit'), {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Medical Test Submitted Successfully',
                    life: 1500,
                })

                setTimeout(() => {
                    closeModal()
                }, [1500])
            },
            onError: (errors) => {
                toast.add({
                    severity: 'error',
                    summary: 'Form has validation errors.',
                    life: 3000,
                })
                console.error(errors)
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
                                Conduct Test
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Provide test details below
                            </DialogDescription>

                            <div class="isolate px-6 lg:px-8 mt-10">
                                <form @submit.prevent="submitForm" class="max-w-xl">
                                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                        <div>
                                            <label
                                                for="referer_full_name"
                                                class="block text-sm font-semibold text-gray-900"
                                            >
                                                Referer Full Name
                                            </label>

                                            <select
                                                id="referer_full_name"
                                                v-model="form.referer_fullname"
                                                class="form-input w-full"
                                                required
                                            >
                                                <option value="" disabled class="text-black">
                                                    Select Referer
                                                </option>
                                                <option
                                                    value="Sharmlane Faith Patches,RMT"
                                                    class="text-black"
                                                >
                                                    Sharmlane Faith Patches,RMT
                                                </option>
                                                <option
                                                    value="Jane R. Moldez, RMT"
                                                    class="text-black"
                                                >
                                                    Jane R. Moldez, RMT
                                                </option>
                                                <option
                                                    value="Jill R. Albino, RMT"
                                                    class="text-black"
                                                >
                                                    Jill R. Albino, RMT
                                                </option>
                                            </select>

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
                                        <!--
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
                                        </div> -->

                                        <div class="sm:col-span-2">
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
                                                class="form-input text-center"
                                                required
                                            />
                                            <p
                                                v-if="form.errors.test_schedule"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.test_schedule }}
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
                                                v-if="form.errors.purpose_id"
                                                class="text-sm text-red-500 mt-1"
                                            >
                                                {{ form.errors.purpose_id }}
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
                                                id="category_id"
                                                v-model.number="form.category_id"
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
                                                <label
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    Select Test Type
                                                </label>
                                                <p v-if="selectedCategory" class="text-sm text-gray-600">
                                                    Category price: ₱{{ selectedCategory.price }}
                                                    <span v-if="isDiscounted" class="ml-1 text-green-600 font-medium">(20% discount applied at total)</span>
                                                </p>

                                                <div class="mb-3">
                                                    <button
                                                        type="button"
                                                        @click="toggleSelectAll"
                                                        class="text-sm text-green-600 hover:text-green-800 font-medium"
                                                    >
                                                        {{ allSelectedInCategory ? 'Deselect all' : 'Select all' }}
                                                    </button>
                                                </div>

                                                <div class="space-y-2 mt-2">
                                                    <div
                                                        v-for="type in filteredTestTypes"
                                                        :key="type.id"
                                                        :value="Number(type.id)"
                                                        class="flex items-center"
                                                    >
                                                        <label class="inline-flex items-center">
                                                            <input
                                                                type="checkbox"
                                                                :value="type.id"
                                                                v-model.number="
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
                                                <div class="mt-4 text-right text-sm font-semibold text-gray-900 flex items-center justify-end gap-2 flex-wrap">
                                                    <span>Total Price: ₱{{ totalPrice }}</span>
                                                    <span v-if="isDiscounted && form.selected_test_types.length" class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs">20% Discount Applied</span>
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
                                            Save
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
