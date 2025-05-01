<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head name="Inventory Request" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inventory Medical Supply Requests
            </h2>
        </template>
    </AuthenticatedLayout>
</template>
