<script setup>
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'

    import AddUserModal from '@/Components/modal/AddUserModal.vue'
    import AdminLayout from '@/Layouts/AdminLayout.vue'

    import { Head, router } from '@inertiajs/vue3'
    import { onMounted, ref } from 'vue'

    import { formatDate } from '@/helpers/formatter'
    import { useToast } from 'primevue/usetoast'
    import { useConfirm } from 'primevue/useconfirm'
    import EditUserModal from '@/Components/modal/EditUserModal.vue'
    import DangerButton from '@/Components/DangerButton.vue'
    import EditButton from '@/Components/EditButton.vue'
    import AddButton from '@/Components/AddButton.vue'
    import SearchInput from '@/Components/SearchInput.vue'

    const props = defineProps({
        users: Array,
        roles: Array,
    })

    const toast = useToast()
    const confirm = useConfirm()
    const showAddUserModal = ref(false)
    const showEditUserModal = ref(false)
    const selectedEditUser = ref(null)

    const handleUserEdit = (userData) => {
        selectedEditUser.value = userData
        showEditUserModal.value = true
    }

    // DELETE USER MUTATION
    const handleDeleteUser = (userID) => {
        confirm.require({
            message: 'Are you sure you want to delete this user?',
            header: 'Confirm Deletion',
            icon: 'pi pi-exclamation-triangle',

            // swap handlers
            acceptLabel: 'Cancel',
            rejectLabel: 'Yes, Delete',

            // swap classes
            acceptClass: 'p-button-secondary p-button-outlined',
            rejectClass: 'p-button-danger',

            // swap logic
            accept: () => {
                //
            },
            reject: () => {
                router.delete(route('admin.user.destroy', userID), {
                    onSuccess: () => {
                        console.log('sddsfsdf')
                        toast.add({
                            severity: 'success',
                            summary: 'Deleted',
                            detail: 'User deleted successfully',
                            life: 1500,
                        })
                    },
                    onError: () => {
                        toast.add({
                            severity: 'error',
                            summary: 'Error',
                            detail: 'Failed to delete user',
                            life: 1500,
                        })
                    },
                })
            },
        })
    }

    const userTableHeaders = ['Name', 'Email', 'Role', 'Account Created', 'Actions']
</script>

<template>
    <Head title="User" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl mb-3 text-gray-600">User Management</h1>

            <!-- SEARCH INPUT -->
            <div class="flex gap-3">
                <AddButton color="green" @click="showAddUserModal = true">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                        ></path>
                    </svg>

                    Create User
                </AddButton>
                <SearchInput route="admin.user" placeholder="Search Name, Email" />
            </div>
        </div>

        <fwb-table hoverable>
            <fwb-table-head>
                <fwb-table-head-cell
                    v-for="(header, index) in userTableHeaders"
                    :key="index"
                    class="bg-green-600 text-white"
                >
                    {{ header }}
                </fwb-table-head-cell>
            </fwb-table-head>

            <fwb-table-body>
                <template v-if="users && users.length > 0">
                    <fwb-table-row v-for="(user, index) in users" :key="index">
                        <fwb-table-cell>{{ user.name }}</fwb-table-cell>
                        <fwb-table-cell>{{ user.email }}</fwb-table-cell>
                        <fwb-table-cell>
                            {{ user.role_name.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) }}
                        </fwb-table-cell>
                        <fwb-table-cell>{{ formatDate(user.created_at, false) }}</fwb-table-cell>
                        <fwb-table-cell class="flex items-center gap-3">
                            <EditButton @click="handleUserEdit(user)">Edit</EditButton>
                            <DangerButton @click="handleDeleteUser(user.id)">Delete</DangerButton>
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

        <AddUserModal v-if="showAddUserModal" @close="showAddUserModal = false" :roles="roles" />
        <EditUserModal
            v-if="showEditUserModal"
            @close="showEditUserModal = false"
            :user="selectedEditUser"
            :roles="roles"
        />
    </AdminLayout>
</template>
