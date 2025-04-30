<script setup>
    import BusinessLogo from '@/Components/BusinessLogo.vue'
    import { Link } from '@inertiajs/vue3'
    import { computed } from 'vue'

    // Define props
    const props = defineProps({
        noMaxWidth: {
            type: Boolean,
            default: false, // Default is still having sm:max-w-md
        },

        dynamicBgColor: {
            type: String,
            default: 'bg-white', // Default is still having sm:max-w-md
        },
    })
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-r from-green-400 to-gray-100 pt-10 sm:justify-center sm:pt-0 px-4"
    >
        <div class="pt-10">
            <Link href="/">
                <BusinessLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden px-6 py-4"
            :class="[dynamicBgColor, { 'sm:max-w-md': !noMaxWidth }]"
        >
            <slot />
        </div>
    </div>
</template>
