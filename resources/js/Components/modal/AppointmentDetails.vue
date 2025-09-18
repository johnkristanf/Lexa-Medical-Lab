<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
    } from '@headlessui/vue'

    import Toast from 'primevue/toast'
    import { onMounted, computed } from 'vue'

    const props = defineProps({
        selectedAppointment: Object,
    })

    onMounted(() => {
        console.log("selectedAppointment: ", props.selectedAppointment);
        
    })

    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // Computed properties for better data formatting
    const formattedScheduleDate = computed(() => {
        if (props.selectedAppointment?.schedule?.date) {
            return new Date(props.selectedAppointment.schedule.date).toLocaleString()
        }
        return 'N/A'
    })

    const formattedDateOfBirth = computed(() => {
        if (props.selectedAppointment?.date_of_birth) {
            return new Date(props.selectedAppointment.date_of_birth).toLocaleDateString()
        }
        return 'N/A'
    })

    const totalTestCost = computed(() => {
        if (props.selectedAppointment?.test_types) {
            return props.selectedAppointment.test_types.reduce(
                (total, test) => total + (test.price || 0),
                0,
            )
        }
        return 0
    })

    // const statusColor = computed(() => {
    //     const status = props.selectedAppointment?.status?.toLowerCase()
    //     switch (status) {
    //         case 'arrived':
    //             return 'bg-green-100 text-green-800 border-green-200'
    //         case 'pending':
    //             return 'bg-yellow-100 text-yellow-800 border-yellow-200'
    //         case 'completed':
    //             return 'bg-blue-100 text-blue-800 border-blue-200'
    //         case 'cancelled':
    //             return 'bg-red-100 text-red-800 border-red-200'
    //         default:
    //             return 'bg-gray-100 text-gray-800 border-gray-200'
    //     }
    // })

</script>

<template>
    <TransitionRoot appear :show="true" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-[999]">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
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
                            class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                        >
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <DialogTitle as="h1" class="text-2xl font-bold text-white">
                                            Appointment Details
                                        </DialogTitle>

                                        <p class="text-sm text-green-100 mt-1">
                                            Appointment # {{
                                                selectedAppointment?.appointment_number
                                            }}
                                        </p>
                                    </div>
                                    <button
                                        @click="closeModal"
                                        class="text-white hover:text-green-100 transition-colors p-2 rounded-full hover:bg-green-500/20"
                                    >
                                        <svg
                                            class="w-6 h-6"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="max-h-[70vh] overflow-y-auto">
                                <div class="p-8 space-y-8">
                                    <!-- Patient Information -->
                                    <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                                        <h2
                                            class="text-xl font-semibold text-green-800 mb-4 flex items-center"
                                        >
                                            <svg
                                                class="w-5 h-5 mr-2"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                />
                                            </svg>
                                            Patient Information
                                        </h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-4">
                                                <div>
                                                    <label
                                                        class="text-sm font-medium text-green-700"
                                                    >
                                                        Full Name
                                                    </label>
                                                    <p class="text-gray-900 font-medium">
                                                        {{ selectedAppointment?.first_name }}
                                                        {{ selectedAppointment?.middle_name }}
                                                        {{ selectedAppointment?.last_name }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-sm font-medium text-green-700"
                                                    >
                                                        Email
                                                    </label>
                                                    <p class="text-gray-900">
                                                        {{ selectedAppointment?.email }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-sm font-medium text-green-700"
                                                    >
                                                        Gender
                                                    </label>
                                                    <p class="text-gray-900 capitalize">
                                                        {{ selectedAppointment?.gender }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="space-y-4">
                                                <div>
                                                    <label
                                                        class="text-sm font-medium text-green-700"
                                                    >
                                                        Date of Birth
                                                    </label>
                                                    <p class="text-gray-900">
                                                        {{ formattedDateOfBirth }}
                                                    </p>
                                                </div>
                                                <div class="flex flex-col">
                                                    <label
                                                        class="text-sm font-medium text-green-700"
                                                    >
                                                        Status
                                                    </label>
                                                    <span
                                                        class="py-1 text-sm font-medium capitalize"
                                                    >
                                                        {{ selectedAppointment?.status }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Appointment Schedule -->
                                    <div class="bg-white rounded-xl p-6 border-2 border-green-200">
                                        <h2
                                            class="text-xl font-semibold text-green-800 mb-4 flex items-center"
                                        >
                                            <svg
                                                class="w-5 h-5 mr-2"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            Schedule Information
                                        </h2>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div>
                                                <label class="text-sm font-medium text-green-700">
                                                    Scheduled Date & Time
                                                </label>
                                                <p class="text-gray-900 font-medium">
                                                    {{ formattedScheduleDate }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-green-700">
                                                    Schedule Status
                                                </label>
                                                <span class="inline-block py-1 text-sm capitalize">
                                                    {{ selectedAppointment?.status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Test Types -->
                                    <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                                        <h2
                                            class="text-xl font-semibold text-green-800 mb-4 flex items-center"
                                        >
                                            <svg
                                                class="w-5 h-5 mr-2"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                            Test Types ({{
                                                selectedAppointment?.test_types?.length || 0
                                            }})
                                        </h2>
                                        <div class="space-y-4">
                                            <div
                                                v-for="(
                                                    test, index
                                                ) in selectedAppointment?.test_types"
                                                :key="test.id"
                                                class="bg-white rounded-lg p-4 border border-green-200 hover:shadow-md transition-shadow"
                                            >
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h3
                                                            class="font-semibold text-gray-900 mb-2"
                                                        >
                                                            {{ test.name }}
                                                        </h3>
                                                        <div
                                                            class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm"
                                                        >
                                                            <div>
                                                                <span
                                                                    class="text-green-700 font-medium"
                                                                >
                                                                    Reference Range:
                                                                </span>
                                                                <p class="text-gray-600">
                                                                    {{ test.reference_range }}
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <span
                                                                    class="text-green-700 font-medium"
                                                                >
                                                                    Unit:
                                                                </span>
                                                                <p class="text-gray-600">
                                                                    {{ test.unit || 'N/A' }}
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <span
                                                                    class="text-green-700 font-medium"
                                                                >
                                                                    Category:
                                                                </span>
                                                                <p class="text-gray-600">
                                                                    {{ test.test_category.name }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right ml-4">
                                                        <span
                                                            class="text-2xl font-bold text-green-600"
                                                        >
                                                            ₱{{ test.price }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Cost -->
                                        <div class="mt-6 pt-4 border-t border-green-200">
                                            <div class="flex justify-between items-center">
                                                <span class="text-lg font-semibold text-green-800">
                                                    Total Cost:
                                                </span>
                                                <span class="text-2xl font-bold text-green-600">
                                                    ₱{{ totalTestCost }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <!-- <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                                <div class="flex justify-end space-x-3">
                                    <button
                                        @click="closeModal"
                                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium"
                                    >
                                        Close
                                    </button>
                                    <button
                                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                                    >
                                        Print Details
                                    </button>
                                </div>
                            </div> -->
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <Toast />
</template>
