<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    import {
        FwbBadge,
        FwbDropdown,
        FwbListGroup,
        FwbListGroupItem,
        FwbButton,
    } from 'flowbite-vue'

    import Toast from 'primevue/toast'
    import { useToast } from 'primevue/usetoast'
    import { router } from '@inertiajs/vue3'
    import { formatDate } from '@/helpers/formatter'
    import { onMounted, ref } from 'vue'

    const props = defineProps({
        schedules: Array,
    })

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close', 'addSchedule'])
    const closeModal = () => emit('close')
    const openAddScheduleModal = () => emit('addSchedule')

    // EXPANDED CARDS STATE
    const expandedCards = ref(new Set())

    // TOGGLE CARD EXPANSION
    function toggleCard(scheduleId) {
        if (expandedCards.value.has(scheduleId)) {
            expandedCards.value.delete(scheduleId)
        } else {
            expandedCards.value.add(scheduleId)
        }
    }

    // FORMAT TIME FOR DISPLAY
    function formatTime(timeString) {
        if (!timeString) return ''
        // Convert "07:01:00" to "7:01 AM"
        const time = new Date(`2000-01-01T${timeString}`)
        return time.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        })
    }

    // GET SCHEDULE STATS
    function getScheduleStats(slots) {
        const total = slots?.length || 0
        const available = slots?.filter(slot => slot.status === 'available').length || 0
        return { total, available, unavailable: total - available }
    }

    // SCHEDULE TIME SLOT STATUS UPDATE
    function updateTimeSlotStatus(scheduleId, slotId, status) {
        router.put(
            `/admin/appointment-schedules/slots/${slotId}/status`, // Adjust the route as per your backend
            { status },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Time Slot Status Updated',
                        detail: `Time slot status set to ${status.toUpperCase()}`,
                    })
                },
                onError: (error) => {
                    console.log('Time Slot Update Error:', error)

                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to update time slot status',
                    })
                },
            },
        )
    }

    onMounted(() => {
        console.log("props here: ", props);
    })
</script>

<template>
    <TransitionRoot appear :show="true">
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
                            class="w-full max-w-6xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <DialogTitle
                                        as="h1"
                                        class="text-2xl font-medium leading-6 text-gray-900"
                                    >
                                        Appointment Schedules
                                    </DialogTitle>

                                    <DialogDescription class="text-sm font-medium leading-6 text-gray-400 mt-1">
                                        Manage your appointment schedules and time slots
                                    </DialogDescription>
                                </div>

                                <fwb-button class="flex" color="green" @click="openAddScheduleModal">
                                    Add Schedule
                                </fwb-button>
                            </div>

                            <!-- SCHEDULE CARDS CONTAINER -->
                            <div class="max-h-96 overflow-y-auto space-y-4 pr-2">
                                <!-- SCHEDULE CARDS -->
                                <div
                                    v-for="schedule in schedules"
                                    :key="schedule.id"
                                    class="border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
                                >
                                    <!-- CARD HEADER -->
                                    <div class="p-4 border-b border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        {{ formatDate(schedule.date) }}
                                                    </h3>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <span class="text-sm text-gray-500">
                                                            {{ getScheduleStats(schedule.appointment_slots).total }} time slots
                                                        </span>
                                                        <span class="text-xs text-gray-300">•</span>
                                                        <span class="text-sm text-green-600 font-medium">
                                                            {{ getScheduleStats(schedule.appointment_slots).available }} available
                                                        </span>
                                                        <span class="text-xs text-gray-300">•</span>
                                                        <span class="text-sm text-red-600 font-medium">
                                                            {{ getScheduleStats(schedule.appointment_slots).unavailable }} unavailable
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-3">
                                                <!-- TIME SLOTS PREVIEW -->
                                                <div class="flex space-x-1">
                                                    <fwb-badge
                                                        v-for="slot in schedule.appointment_slots?.slice(0, 4)"
                                                        :key="slot.id"
                                                        :type="slot.status === 'available' ? 'green' : 'red'"
                                                        class="text-xs"
                                                    >
                                                        {{ formatTime(slot.time_slot) }}
                                                    </fwb-badge>
                                                    <span 
                                                        v-if="schedule.appointment_slots?.length > 4"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-full"
                                                    >
                                                        +{{ schedule.appointment_slots.length - 4 }}
                                                    </span>
                                                </div>

                                                <!-- EXPAND/COLLAPSE BUTTON -->
                                                <button
                                                    @click="toggleCard(schedule.id)"
                                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                                >
                                                    <svg 
                                                        :class="{ 'transform rotate-180': expandedCards.has(schedule.id) }"
                                                        class="w-5 h-5 transition-transform duration-200"
                                                        fill="none" 
                                                        stroke="currentColor" 
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EXPANDABLE TIME SLOTS SECTION -->
                                    <div 
                                        v-if="expandedCards.has(schedule.id)"
                                        class="p-4 bg-gray-50"
                                    >
                                        <h4 class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Time Slots Management
                                        </h4>
                                        
                                        <div v-if="schedule.appointment_slots?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                                            <div
                                                v-for="slot in schedule.appointment_slots"
                                                :key="slot.id"
                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
                                            >
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-2 h-2 rounded-full"
                                                             :class="slot.status === 'available' ? 'bg-green-500' : 'bg-red-500'">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-900">
                                                            {{ formatTime(slot.time_slot) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ slot.status.toUpperCase() }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <fwb-dropdown 
                                                    text="•••" 
                                                    color="alternative"
                                                    size="xs"
                                                    class="ml-2"
                                                >
                                                    <fwb-list-group class="text-sm text-gray-700">
                                                        <fwb-list-group-item
                                                            v-if="slot.status === 'available'"
                                                            class="cursor-pointer hover:bg-gray-100 flex items-center"
                                                            @click="updateTimeSlotStatus(schedule.id, slot.id, 'unavailable')"
                                                        >
                                                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Set Unavailable
                                                        </fwb-list-group-item>
                                                        
                                                        <fwb-list-group-item
                                                            v-else
                                                            class="cursor-pointer hover:bg-gray-100 flex items-center"
                                                            @click="updateTimeSlotStatus(schedule.id, slot.id, 'available')"
                                                        >
                                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Set Available
                                                        </fwb-list-group-item>
                                                    </fwb-list-group>
                                                </fwb-dropdown>
                                            </div>
                                        </div>
                                        
                                        <div v-else class="text-center py-8 text-gray-500">
                                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-sm">No time slots available for this date.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- EMPTY STATE -->
                                <div v-if="!schedules?.length" class="text-center py-12 text-gray-500">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No appointment schedules</h3>
                                            <p class="text-gray-500">Get started by creating your first schedule with time slots.</p>
                                        </div>
                                        <fwb-button color="green" @click="openAddScheduleModal">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create First Schedule
                                        </fwb-button>
                                    </div>
                                </div>
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