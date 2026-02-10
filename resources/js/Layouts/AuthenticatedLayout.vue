<script setup>
    import { computed, onMounted, ref } from 'vue'
    import BusinessLogo from '@/Components/BusinessLogo.vue'
    import Dropdown from '@/Components/Dropdown.vue'
    import DropdownLink from '@/Components/DropdownLink.vue'
    import NavLink from '@/Components/NavLink.vue'
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
    import { Link, usePage, router } from '@inertiajs/vue3'
    import Drawer from 'primevue/drawer'
    import Toast from 'primevue/toast'
    import Dialog from 'primevue/dialog'

    const showingNavigationDropdown = ref(false)
    const visibleRight = ref(false)
    const showLogoutDialog = ref(false)

    const page = usePage()
    const user = computed(() => page.props.auth?.user ?? {})
    const permissions = computed(() => user.value?.permissions ?? {})

    const notifications = computed(() => page.props.notifications ?? { lowStock: 0, nearlyExpired: 0 })

    const navigationLinks = computed(() => [
        // ADMIN ONLY
        { name: 'Dashboard', route_name: 'admin.dashboard', permitted: permissions.value?.is_admin },

        // MEDICAL STAFF
        {
            name: 'Appointments',
            route_name: 'medical.appointments',
            permitted: permissions.value?.can_manage_medical,
        },
        { name: 'Queue', route_name: 'patient.queue', permitted: permissions.value?.can_manage_medical },
        {
            name: 'Patients',
            route_name: 'patient.details.create',
            permitted: permissions.value?.can_manage_medical,
        },
        {
            name: 'Test Catalog',
            route_name: 'test.category.create',
            permitted: permissions.value?.can_manage_medical,
        },

        // LABORATORY SUPPLY DASHBOARD
        {
            name: 'Dashboard',
            route_name: 'inventory.dashboard',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },

        // INVENTORY / SUPPLIES
        {
            name: 'Inventory',
            route_name: 'inventory.supplies',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },
        {
            name: 'Supplies',
            route_name: 'supplies.create.page',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },
        {
            name: 'Supply Requests',
            route_name: 'inventory.supply.request',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },
        {
            name: 'Stock',
            route_name: 'medical.stock.create',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },
        {
            name: 'Category',
            route_name: 'category.supplies.create',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },
        {
            name: 'Archived Supplies',
            route_name: 'archive.supplies.create',
            permitted: permissions.value?.can_manage_inventory_supplies,
        },

        // TESTS
        {
            name: 'Laboratory Test',
            route_name: 'test.details.create',
            permitted: permissions.value?.can_manage_medical,
        },
    ])

    // Handle dashboard menu clicks
    const handleDashboardMenuClick = (section) => {
        console.log('section:', section)

        const currentRoute = route().current()

        // Navigate to patient details page with scroll parameter
        if (currentRoute !== 'inventory.dashboard') {
            router.visit(route('inventory.dashboard'), {
                data: { scrollTo: section },
                preserveState: true,
                preserveScroll: false,
            })
        } else {
            // If already on the page, emit custom event
            window.dispatchEvent(
                new CustomEvent('scroll-to-section', {
                    detail: { section },
                }),
            )
        }
    }

    const markNotificationsAsRead = () => {
        router.post(
            route('notifications.markAsRead'),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    visibleRight.value = false
                },
            },
        )
    }

    const confirmLogout = () => {
        showLogoutDialog.value = true
    }

    const handleLogout = () => {
        showLogoutDialog.value = false
        router.post(route('logout'))
    }

    onMounted(() => {
        console.log('User Data: ', page.props.auth?.user)
        console.log('Notifications: ', page.props.notifications)
    })
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('medical.appointments')">
                                    <BusinessLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <template
                                    v-for="link in navigationLinks.filter((link) => link.permitted)"
                                    :key="link.route_name"
                                >
                                    <div v-if="link.name === 'Dashboard'" class="relative group mt-5">
                                        <NavLink
                                            :href="route(link.route_name)"
                                            :active="route().current(link.route_name)"
                                            class="inline-flex items-center"
                                        >
                                            {{ link.name }}
                                        </NavLink>
                                        <!-- Hoverable Dropdown -->
                                        <div
                                            class="absolute hidden group-hover:block mt-2 w-48 p-5 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                        >
                                            <div class="py-1 flex flex-col gap-2">
                                                <h1
                                                    class="hover:opacity-75 hover:cursor-pointer"
                                                    @click="handleDashboardMenuClick('lowStock')"
                                                >
                                                    Nearly Out of Stock
                                                </h1>
                                                <h1
                                                    class="hover:opacity-75 hover:cursor-pointer"
                                                    @click="handleDashboardMenuClick('nearlyExpired')"
                                                >
                                                    Nearly Expired
                                                </h1>
                                                <h1
                                                    class="hover:opacity-75 hover:cursor-pointer"
                                                    @click="handleDashboardMenuClick('medicalSupplies')"
                                                >
                                                    Medical Supplies
                                                </h1>
                                                <h1
                                                    class="hover:opacity-75 hover:cursor-pointer"
                                                    @click="handleDashboardMenuClick('averagePatient')"
                                                >
                                                    Average Patient
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                    <NavLink
                                        v-else
                                        :href="route(link.route_name)"
                                        :active="route().current(link.route_name)"
                                    >
                                        {{ link.name }}
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="hidden sm:ms-6 sm:flex sm:items-center space-x-4">
                            <!-- Notification Bell -->
                            <button @click="visibleRight = true" class="relative">
                                <i class="pi pi-bell text-2xl text-black"></i>
                                <span
                                    v-if="notifications.lowStock > 0 || notifications.nearlyExpired > 0"
                                    class="absolute -top-1 -right-1 bg-red-600 text-white text-[6px] font-bold px-1.5 py-0.5 rounded-full"
                                >
                                    {{ notifications.lowStock + notifications.nearlyExpired }}
                                </span>
                            </button>

                            <!-- Drawer -->
                            <Drawer v-model:visible="visibleRight" header="Notifications" position="right">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm text-gray-600">Your notifications</span>

                                    <button
                                        v-if="notifications.lowStock > 0 || notifications.nearlyExpired > 0"
                                        @click="markNotificationsAsRead"
                                        class="text-xs text-blue-600 hover:underline"
                                    >
                                        Mark all as read
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <p v-if="notifications.lowStock > 0" class="text-sm">
                                        ⚠️ {{ notifications.lowStock }} item(s) are
                                        <b>low on stock</b>
                                        .
                                    </p>

                                    <p v-if="notifications.nearlyExpired > 0" class="text-sm">
                                        ⏳ {{ notifications.nearlyExpired }} item(s) are
                                        <b>nearly expired</b>
                                        .
                                    </p>

                                    <p
                                        v-if="
                                            notifications.lowStock === 0 && notifications.nearlyExpired === 0
                                        "
                                        class="text-gray-500 text-sm"
                                    >
                                        ✅ No notifications
                                    </p>
                                </div>
                            </Drawer>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 hover:text-gray-700"
                                            >
                                                {{ $page.props.auth.user.name }}
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
                                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
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

                        <!-- Mobile Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500"
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

                <!-- Responsive Nav -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            v-for="link in navigationLinks.filter((link) => link.permitted)"
                            :key="link.route_name"
                            :href="route(link.route_name)"
                            :active="route().current(link.route_name)"
                        >
                            {{ link.name }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
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

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
                <Toast />
            </main>
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
