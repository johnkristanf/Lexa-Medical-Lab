<script setup>
    import BusinessLogo from '@/Components/BusinessLogo.vue'
    import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-r from-green-400 to-gray-100 pt-10 sm:justify-center sm:pt-0 px-4"
    >
        <div>
            <Link href="/">
                <BusinessLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md "
        >
            <slot />
        </div>
    </div>
</template>
