<script setup>
    import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Unauthorized" />
    <div class="flex min-h-screen items-center justify-center bg-gray-100">
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white p-8 shadow-lg">
            <div class="text-center">
                <h1 class="mb-4 text-2xl font-bold text-red-600">Unauthorized Access</h1>
                <p class="mb-6 text-gray-700">You don't have permission to access this page.</p>
                <a
                    href="/"
                    class="rounded bg-blue-500 px-4 py-2 font-bold text-white hover:bg-blue-700"
                >
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</template>
