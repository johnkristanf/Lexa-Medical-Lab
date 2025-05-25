<script setup>
    import { ROLE } from '@/Enums/Role'
    import AdminLayout from '@/Layouts/AdminLayout.vue'
    import { Head, usePage } from '@inertiajs/vue3'

    const page = usePage()
    console.log('user name: ', page.props.auth.user)
   
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
