<script setup>
    import { ref, computed, onMounted } from 'vue'
    import BusinessLogo from '@/Components/BusinessLogo.vue'
    import Dropdown from '@/Components/Dropdown.vue'
    import DropdownLink from '@/Components/DropdownLink.vue'
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
    import { Link, usePage, router } from '@inertiajs/vue3'
    import SideBar from '@/Components/SideBar.vue'
    import Drawer from 'primevue/drawer'
    import ConfirmPopup from 'primevue/confirmpopup'
    import Toast from 'primevue/toast'
    import Dialog from 'primevue/dialog'

    const showingNavigationDropdown = ref(false)
    const visibleRight = ref(false)
    const showLogoutDialog = ref(false)

    const page = usePage()

    // User
    const user = computed(() => page.props.auth?.user ?? {})

    // Notifications
    const notifications = computed(() => page.props.notifications ?? { lowStock: 0, nearlyExpired: 0 })

    const confirmLogout = () => {
        showLogoutDialog.value = true
    }

    const handleLogout = () => {
        router.post(route('logout'))
    }

    onMounted(() => {
        console.log('Admin User Data: ', page.props.auth?.user)
        console.log('Notifications: ', page.props.notifications)
    })
</script>

<template>
    <div>
        <div class="min-h-screen bg-white">
            <nav class="sticky top-0 z-[10px] border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('admin.dashboard')">
                                    <BusinessLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center gap-4">
                            <!-- 🔔 Notification Bell -->
                            <button @click="visibleRight = true" class="relative">
                                <i class="pi pi-bell text-7xl text-black"></i>
                                <span
                                    v-if="
                                        notifications.lowStock?.length > 0 ||
                                        notifications.nearlyExpired?.length > 0
                                    "
                                    class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[8px] font-bold px-1 py-0.1 rounded-full"
                                >
                                    {{
                                        (notifications.lowStock?.length || 0) +
                                        (notifications.nearlyExpired?.length || 0)
                                    }}
                                </span>
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ user.name }}
                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('admin.profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <button
                                            @click="confirmLogout"
                                            class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                        >
                                            Log Out
                                        </button>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('admin.profile.edit')">Profile</ResponsiveNavLink>
                            <button
                                @click="confirmLogout"
                                class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                            >
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Drawer for Notifications -->
            <Drawer v-model:visible="visibleRight" header="Notifications" position="right">
                <div class="space-y-4">
                    <!-- Low Stock Items -->
                    <div v-if="notifications.lowStock?.length > 0" class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="pi pi-exclamation-triangle text-orange-500"></i>
                            Low Stock Items
                        </h3>
                        <div
                            v-for="item in notifications.lowStock"
                            :key="item.id"
                            class="p-3 bg-orange-50 border-l-4 border-orange-400 rounded"
                        >
                            <p class="text-sm font-medium text-gray-800">{{ item.brand_name }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                Current:
                                <span class="font-semibold text-orange-600">
                                    {{ item.quantity }} {{ item.unit }}
                                </span>
                                / Critical:
                                <span class="font-semibold">{{ item.critical_stock }} {{ item.unit }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Nearly Expired Items -->
                    <div v-if="notifications.nearlyExpired?.length > 0" class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="pi pi-clock text-yellow-500"></i>
                            Nearly Expired Batches
                        </h3>
                        <div
                            v-for="batch in notifications.nearlyExpired"
                            :key="batch.id"
                            class="p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded"
                        >
                            <p class="text-sm font-medium text-gray-800">{{ batch.supply_name }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                Batch:
                                <span class="font-semibold">{{ batch.batch_number }}</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                Expires:
                                <span class="font-semibold text-yellow-600">{{ batch.expiration_date }}</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                Quantity:
                                <span class="font-semibold">{{ batch.quantity }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <p
                        v-if="
                            (!notifications.lowStock || notifications.lowStock.length === 0) &&
                            (!notifications.nearlyExpired || notifications.nearlyExpired.length === 0)
                        "
                        class="text-gray-500 text-sm flex items-center gap-2"
                    >
                        <i class="pi pi-check-circle text-green-500"></i>
                        No notifications
                    </p>
                </div>
            </Drawer>

            <!-- ADMIN SIDEBAR -->
            <SideBar />

            <!-- Page Content -->
            <div class="p-4 sm:ml-64">
                <div class="p-4 rounded-lg dark:border-gray-700">
                    <main>
                        <slot />
                        <ConfirmPopup />
                        <Toast />
                    </main>
                </div>
            </div>
        </div>

        <!-- Logout Confirmation Dialog -->
        <Dialog v-model:visible="showLogoutDialog" modal header="Confirm Logout" :style="{ width: '25rem' }">
            <div class="flex items-center gap-4 mb-4">
                <i class="pi pi-exclamation-triangle text-3xl text-yellow-500"></i>
                <p class="text-gray-700">Are you sure you want to logout?</p>
            </div>
            <template #footer>
                <button
                    @click="showLogoutDialog = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    Cancel
                </button>
                <button
                    @click="handleLogout"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Logout
                </button>
            </template>
        </Dialog>
    </div>
</template>
