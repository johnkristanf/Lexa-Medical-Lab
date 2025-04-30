<script setup>
    import { formatDate, getStatusClasses } from '@/helpers/formatter'
    import GuestLayout from '@/Layouts/GuestLayout.vue'
    import { Card } from 'primevue'
    import { computed, onMounted } from 'vue'
    import { router } from '@inertiajs/vue3'

    // Props
    const props = defineProps({
        queues: Array,
    })

    console.log('Dashboard Queue: ', props.queues)

    const sortedQueues = computed(() => {
        return [...props.queues].sort((a, b) => {
            // First sort by priority_level (ascending)
            if (a.priority_types.priority_level !== b.priority_types.priority_level) {
                return a.priority_types.priority_level - b.priority_types.priority_level
            }
            // For patients with same priority level, sort by creation time (first come, first served)
            return new Date(a.created_at) - new Date(b.created_at)
        })
    })

    onMounted(() => {
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
            }
        })
    })
</script>

<template>
    <GuestLayout :noMaxWidth="true" dynamicBgColor="bg-transparent">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-16">
            <Card
                v-for="queue in sortedQueues"
                style="width: 20rem; overflow: hidden; height: 23rem"
            >
                <!-- BUSSINESS LOGO IMAGE HEADER -->
                <template #header>
                    <div class="p-8">
                        <img alt="user header" src="/img/lexa-logo-full.png" />
                    </div>
                </template>

                <template #title>
                    <div class="flex flex-col gap-1">
                        <!-- QUEUE NUMBER -->
                        <h1>{{ queue.queue_number }}</h1>

                        <!-- PRIORITY LEVEL -->
                        <h1>Priority Level: {{ queue.priority_types.priority_level }}</h1>
                    </div>
                </template>

                <!-- PATIENT NAME -->
                <template #subtitle>
                    <h1 class="text-2xl">{{ queue.name }}</h1>
                </template>

                <template #content>
                    <!-- PRIORITY TYPE NAME AND CODE -->
                    <h1>{{ queue.priority_types.name }} ({{ queue.priority_types.code }})</h1>

                    <!-- CREATION DATE -->
                    <h1>{{ formatDate(queue.created_at) }}</h1>

                    <!-- QUEUE STATUS -->
                    <h1
                        class="mt-6 p-2 rounded-md text-sm font-semibold w-full text-center"
                        :class="getStatusClasses(queue.queue_status.tag)"
                    >
                        {{ queue.queue_status.name.toUpperCase() }}
                    </h1>
                </template>
            </Card>
        </div>
    </GuestLayout>
</template>
