<script setup>
    import { formatDate, formatDateWithoutTime, getStatusClasses } from '@/helpers/formatter'
    import GuestLayout from '@/Layouts/GuestLayout.vue'
    import { Card } from 'primevue'
    import { computed, onMounted } from 'vue'
    import { router } from '@inertiajs/vue3'

    // Props
    const props = defineProps({
        queues: Array,
    })

    // // SORT PATIENT BY PRIORITY LEVEL
    // const sortedQueues = computed(() => {
    //     return [...props.queues].sort((a, b) => {
    //         // First sort by priority_level (ascending)
    //         if (a.priority_types.priority_level !== b.priority_types.priority_level) {
    //             return a.priority_types.priority_level - b.priority_types.priority_level
    //         }
    //         // For patients with same priority level, sort by creation time (first come, first served)
    //         return new Date(a.created_at) - new Date(b.created_at)
    //     })
    // })

    onMounted(() => {
        console.log('Queues: ', props.queues)
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Echo connected successfully to the server.')
        })

        // LISTEN TO QUEUE EVENT
        window.Echo.channel('queues').listen('.update.queue', (e) => {
            console.log('Update Queue ID:', e.updatedQueueID)

            if (e.updatedQueueID) {
                router.get(
                    route('queue.dashboard'),
                    {},
                    {
                        preserveState: true,
                        preserveScroll: true,
                        only: ['queues'],
                    },
                )

                const audio = new Audio('/sounds/new_queue.mp3')
                audio.play().catch((error) => {
                    console.error('Failed to play audio:', error)
                })
            }
        })
    })
</script>

<template>
    <GuestLayout :noMaxWidth="true" dynamicBgColor="bg-transparent">
        <div class="w-full flex justify-center items-center">
            <div class="flex flex-col items-center w-full mb-8">
                <div
                    class="mt-2 px-4 py-2 rounded-md bg-blue-50 text-blue-800 text-center text-lg font-medium shadow-sm max-w-xl"
                >
                    <p class="text-gray-900 text-2xl mb-2">Important Notice:</p>
                    Patients with an appointment number are always prioritized and skip ahead in the queue.
                    Walk-ins that are regular patients will be called in order after all scheduled
                    appointments.
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-16">
            <Card v-for="queue in props.queues" style="width: 20rem; overflow: hidden; height: 15rem">
                <!-- BUSSINESS LOGO IMAGE HEADER -->
                <!-- <template #header>
                    <div class="p-8">
                        <img alt="Company Logo" src="/img/lexa-logo-full.png" />
                    </div>
                </template> -->

                <template #title>
                    <div class="flex flex-col gap-1 items-center justify-center">
                        <h1 class="text-6xl font-bold">{{ queue.queue_number }}</h1>
                        <!-- Display appointment number if available and it's an appointment -->
                        <div
                            v-if="queue.is_appointment && queue.appointment_number"
                            class="mt-2 text-sm text-indigo-600 font-semibold"
                        >
                            Appointment #: {{ queue.appointment_number }}
                        </div>
                    </div>
                </template>

                <template #content>
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <!-- PRIORITY TYPE NAME AND CODE -->
                        <h1>{{ queue.priority_types.name }} ({{ queue.priority_types.code }})</h1>

                        <!-- CREATION DATE -->
                        <h1>{{ formatDateWithoutTime(queue.created_at) }}</h1>

                        <!-- QUEUE STATUS -->
                        <h1
                            class="mt-6 p-2 rounded-md text-sm font-semibold w-full text-center"
                            :class="getStatusClasses(queue.queue_status.tag)"
                        >
                            {{ queue.queue_status.name.toUpperCase() }}
                        </h1>
                    </div>
                </template>
            </Card>
        </div>
    </GuestLayout>
</template>
