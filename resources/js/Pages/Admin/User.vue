<script setup>
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
        FwbButton,
    } from 'flowbite-vue'

    import AdminLayout from '@/Layouts/AdminLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { ref } from 'vue'

    // Dummy users array (you can replace this later with real data)
    const users = ref([
        // Uncomment this for testing with empty data:
        { name: 'John Doe', email: 'johndoe@example.com', created_at: '05/25/2025 10:30 PM', role: 'Laboratory Technician' }
    ])
</script>

<template>
    <Head title="User" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl mb-3 text-gray-600">User Management</h1>

            <!-- SEARCH INPUT -->
            <div class="flex gap-3">
                <fwb-button color="green">Add User</fwb-button>
                <div class="relative">
                    <div
                        class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                    >
                        <svg
                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 20"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                            />
                        </svg>
                    </div>
                    <input
                        type="search"
                        class="block w-full ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                        placeholder="Search Name, Schedule..."
                        required
                    />
                </div>
            </div>
        </div>

        <fwb-table hoverable>
            <fwb-table-head class="bg-green-600 text-white">
                <fwb-table-head-cell>Name</fwb-table-head-cell>
                <fwb-table-head-cell>Email</fwb-table-head-cell>
                <fwb-table-head-cell>Role</fwb-table-head-cell>
                <fwb-table-head-cell>Account Created</fwb-table-head-cell>
                <fwb-table-head-cell>Actions</fwb-table-head-cell>
            </fwb-table-head>

            <fwb-table-body>
                <template v-if="users.length > 0">
                    <fwb-table-row v-for="(user, index) in users" :key="index">
                        <fwb-table-cell>{{ user.name }}</fwb-table-cell>
                        <fwb-table-cell>{{ user.email }}</fwb-table-cell>
                        <fwb-table-cell>{{ user.role }}</fwb-table-cell>
                        <fwb-table-cell>{{ user.created_at }}</fwb-table-cell>
                        <fwb-table-cell class="flex items-center">
                            <fwb-button size="sm" color="green" class="mr-2">Edit</fwb-button>
                            <fwb-button size="sm" color="red">Delete</fwb-button>
                        </fwb-table-cell>
                    </fwb-table-row>
                </template>
                <template v-else>
                    <fwb-table-row>
                        <fwb-table-cell colspan="3" class="text-center bg-gray-100 text-gray-500">
                            No user records found.
                        </fwb-table-cell>
                    </fwb-table-row>
                </template>
            </fwb-table-body>
        </fwb-table>
    </AdminLayout>
</template>
