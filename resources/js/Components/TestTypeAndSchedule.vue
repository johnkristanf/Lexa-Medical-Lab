<script setup>
    import Select from 'primevue/select'

    import {
        FwbAccordion,
        FwbAccordionPanel,
        FwbAccordionHeader,
        FwbAccordionContent,
        FwbButton,
        FwbBadge,
    } from 'flowbite-vue'

    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    import { ref, computed, watch, onMounted } from 'vue'
    import { formatDate } from '@/helpers/formatter'
import { loadPatientCodeWithDiscount } from '@/helpers/random_num'

    const props = defineProps({
        test_categories: Array,
        appointment_schedules: Array,
        form: Object,
    })


    onMounted(() => {
        console.log('test_categories 123: ', props.test_categories)
    })

    // Modal state
    const isScheduleModalOpen = ref(false)
    const selectedSchedule = ref(null)
    const selectedTimeSlot = ref(null)

    // Store selected test type IDs
    const selectedTypeIds = ref([])

    // Flatten all test types to look up prices easily
    const allTestTypes = computed(() =>
        props.test_categories.flatMap((category) => category.test_types || []),
    )

    function categoryTypeIds(category) {
        return (category.test_types || []).map((t) => t.id)
    }

    function allSelectedInCategory(category) {
        const ids = categoryTypeIds(category)
        if (!ids.length) return false
        const selected = new Set(selectedTypeIds.value)
        return ids.every((id) => selected.has(id))
    }

    function toggleSelectAllInCategory(category) {
        const ids = categoryTypeIds(category)
        if (!ids.length) return
        if (allSelectedInCategory(category)) {
            selectedTypeIds.value = selectedTypeIds.value.filter((id) => !ids.includes(id))
        } else {
            const combined = new Set([...selectedTypeIds.value, ...ids])
            selectedTypeIds.value = [...combined]
        }
    }

    // Total price: each category has one price; include it once if user selects any test type in that category
    const discountedCode = loadPatientCodeWithDiscount();

    const totalPrice = computed(() => {
        const priorityTypeCode = props.form?.priority_type?.code
        const hasDiscount = discountedCode.includes(priorityTypeCode)
        const selectedIds = new Set(selectedTypeIds.value)
        const total = props.test_categories.reduce((sum, category) => {
            const hasSelectedTypeInCategory = (category.test_types || []).some((t) =>
                selectedIds.has(t.id),
            )
            if (!hasSelectedTypeInCategory) return sum
            let price = Number(category.price)
            if (hasDiscount) {
                price = price * 0.8 // 20% discount
            }
            return sum + price
        }, 0)
        return total.toFixed(2)
    })

    // Whether the current total is discounted (for UI indicator)
    const isDiscounted = computed(() => {
        const priorityTypeCode = props.form?.priority_type?.code
        return priorityTypeCode && discountedCode.includes(priorityTypeCode)
    })

    // Format time for display
    function formatTime(timeString) {
        if (!timeString) return ''
        const time = new Date(`2000-01-01T${timeString}`)
        return time.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        })
    }

    // Get available slots for a schedule
    function getAvailableSlots(schedule) {
        return schedule.appointment_slots?.filter((slot) => slot.status === 'available') || []
    }

    // Open schedule selection modal
    function openScheduleModal() {
        isScheduleModalOpen.value = true
    }

    // Close schedule selection modal
    function closeScheduleModal() {
        isScheduleModalOpen.value = false
    }

    // Select a time slot
    function selectTimeSlot(schedule, slot) {
        console.log("schedule: ", schedule);
        console.log("slot: ", slot);

        selectedSchedule.value = schedule
        selectedTimeSlot.value = slot

        // Update form data
        props.form.selected_schedule_id = schedule.id
        props.form.selected_time_slot_id = slot.id

        closeScheduleModal()
    }

    // Get selected schedule display text
    const selectedScheduleText = computed(() => {
        if (selectedSchedule.value && selectedTimeSlot.value) {
            return `${formatDate(selectedSchedule.value.date, false)} at ${formatTime(selectedTimeSlot.value.time_slot)}`
        }
        return 'Select Schedule & Time'
    })

    // Watch every new test types checked, to be inserted in form data
    watch(selectedTypeIds, (newVal) => {
        props.form.selected_type_ids = newVal
    })

   

</script>

<template>
    <fwb-accordion class="p-5">
        <div class="flex justify-between items-center mb-8">
            <div class="flex flex-col">
                <h1 class="text-3xl font-bold">Schedule</h1>
                <p class="text-gray-500 text-sm">Please choose type according to your needs</p>
            </div>

            <div class="w-1/3">
                <label class="block text-sm text-gray-900 mb-2">Pick a Schedule & Time</label>

                <fwb-button
                    @click="openScheduleModal"
                    color="alternative"
                    class="w-full"
                >
                    <span class="truncate">{{ selectedScheduleText }}</span>
                </fwb-button>

                <!-- Selected Schedule Info -->
                <div
                    v-if="selectedSchedule && selectedTimeSlot"
                    class="mt-2 p-2 bg-green-50 border border-green-200 rounded-md"
                >
                    <div class="flex items-center text-sm text-green-800">
                        <svg
                            class="w-4 h-4 mr-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            ></path>
                        </svg>
                        Schedule confirmed
                    </div>
                </div>
            </div>
        </div>

        <fwb-accordion-panel v-for="category in test_categories" :key="category.id">
            <fwb-accordion-header>
                {{ category.name }} (₱{{ category.price }})
            </fwb-accordion-header>
            <fwb-accordion-content>
                <div v-if="category.test_types && category.test_types.length">
                    <div class="mb-3">
                        <button
                            type="button"
                            @click="toggleSelectAllInCategory(category)"
                            class="text-sm text-green-600 hover:text-green-800 font-medium"
                        >
                            {{ allSelectedInCategory(category) ? 'Deselect all' : 'Select all' }}
                        </button>
                    </div>
                    <div
                        v-for="type in category.test_types"
                        :key="type.id"
                        class="flex items-center mb-2"
                    >
                        <input
                            type="checkbox"
                            :id="'type-' + type.id"
                            :value="type.id"
                            v-model="selectedTypeIds"
                            class="mr-2"
                        />
                        <label :for="'type-' + type.id" class="text-gray-700">
                            {{ type.name }}
                        </label>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500 italic">No test types available.</p>
            </fwb-accordion-content>
        </fwb-accordion-panel>

        <!-- Total Price Display -->
        <div class="mt-4 text-right font-semibold text-lg text-green-700 flex items-center justify-end gap-2 flex-wrap">
            <span>Total Price: ₱{{ totalPrice }}</span>
            <fwb-badge v-if="isDiscounted && selectedTypeIds.length" type="green" class="shrink-0">
                20% Discount Applied
            </fwb-badge>
        </div>
    </fwb-accordion>

    <!-- SCHEDULE SELECTION MODAL -->
    <TransitionRoot appear :show="isScheduleModalOpen">
        <Dialog as="div" @close="closeScheduleModal" class="relative z-[999]">
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
                            class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900 mb-2"
                            >
                                Select Appointment Schedule
                            </DialogTitle>

                            <DialogDescription
                                class="text-sm font-medium leading-6 text-gray-400 mb-6"
                            >
                                Choose your preferred date and available time slot
                            </DialogDescription>

                            <!-- SCHEDULE CARDS -->
                            <div class="max-h-96 overflow-y-auto space-y-4 pr-2">
                                <div
                                    v-for="schedule in appointment_schedules"
                                    :key="schedule.id"
                                    class="border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
                                >
                                    <!-- DATE HEADER -->
                                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ formatDate(schedule.date, false) }}
                                                </h3>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="text-sm text-gray-500">
                                                        {{
                                                            getAvailableSlots(schedule).length
                                                        }}
                                                        available slots
                                                    </span>
                                                    <span
                                                        v-if="
                                                            getAvailableSlots(schedule).length > 0
                                                        "
                                                        class="text-xs text-gray-300"
                                                    >
                                                        •
                                                    </span>
                                                    <span
                                                        v-if="
                                                            getAvailableSlots(schedule).length > 0
                                                        "
                                                        class="text-sm text-green-600"
                                                    >
                                                        Ready to book
                                                    </span>
                                                </div>
                                            </div>
                                            <fwb-badge
                                                v-if="getAvailableSlots(schedule).length === 0"
                                                type="red"
                                            >
                                                FULLY BOOKED
                                            </fwb-badge>
                                        </div>
                                    </div>

                                    <!-- TIME SLOTS -->
                                    <div class="p-4">
                                        <div
                                            v-if="getAvailableSlots(schedule).length > 0"
                                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"
                                        >
                                            <button
                                                v-for="slot in getAvailableSlots(schedule)"
                                                :key="slot.id"
                                                @click="selectTimeSlot(schedule, slot)"
                                                class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                            >
                                                <div class="flex items-center space-x-2">
                                                    <svg
                                                        class="w-4 h-4 text-green-500"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        ></path>
                                                    </svg>
                                                    <span class="font-semibold text-gray-900">
                                                        {{ formatTime(slot.time_slot) }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-green-600 mt-1">
                                                    Available
                                                </span>
                                            </button>
                                        </div>

                                        <!-- NO AVAILABLE SLOTS -->
                                        <div v-else class="text-center py-8 text-gray-500">
                                            <svg
                                                class="w-8 h-8 mx-auto text-gray-400 mb-2"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                ></path>
                                            </svg>
                                            <p class="text-sm">
                                                No available time slots for this date.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- EMPTY STATE -->
                                <div
                                    v-if="!appointment_schedules?.length"
                                    class="text-center py-12 text-gray-500"
                                >
                                    <div class="flex flex-col items-center space-y-4">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center"
                                        >
                                            <svg
                                                class="w-8 h-8 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">
                                                No schedules available
                                            </h3>
                                            <p class="text-gray-500">
                                                Please check back later for available appointment
                                                slots.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL ACTIONS -->
                            <div class="flex justify-end mt-6">
                                <fwb-button color="alternative" @click="closeScheduleModal">
                                    Cancel
                                </fwb-button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
