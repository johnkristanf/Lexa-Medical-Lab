<script setup>
    import { usePage, Link } from '@inertiajs/vue3'
    import { computed, ref } from 'vue'
    import { CalendarIcon, UserGroupIcon, UserIcon } from '@heroicons/vue/24/outline'

    const isSideBarOpen = ref(false)
    const page = usePage()
    const user = computed(() => page.props.auth?.user ?? {})
    const permissions = computed(() => user.value?.permissions ?? {})

    const isActive = (routeName) => {
        return route().current(routeName)
    }

    const adminNavLinks = computed(() => [
        {
            name: 'Dashboard',
            route_name: 'admin.dashboard',
            permitted: permissions.value?.is_admin,
            icon: UserIcon,
        },

        {
            name: 'Patients',
            route_name: 'admin.patients',

            permitted: permissions.value?.is_admin,
            icon: UserGroupIcon,
        },

        {
            name: 'Inventory',
            route_name: 'admin.inventory',
            permitted: permissions.value?.is_admin,
            icon: CalendarIcon,
        },

        {
            name: 'Appointments',
            route_name: 'admin.appointments',
            permitted: permissions.value?.is_admin,
            icon: CalendarIcon,
        },

        {
            name: 'User',
            route_name: 'admin.user',
            permitted: permissions.value?.is_admin,
            icon: UserIcon,
        },
    ])
</script>

<template>
    <button
        type="button"
        @click="isSideBarOpen = !isSideBarOpen"
        class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
    >
        <span class="sr-only">Open sidebar</span>
        <svg
            class="w-6 h-6"
            aria-hidden="true"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                clip-rule="evenodd"
                fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
            ></path>
        </svg>
    </button>

    <aside
        id="default-sidebar"
        :class="[
            'fixed top-11 left-0 z-40 w-64 h-screen transition-transform border border-r-2',
            isSideBarOpen ? 'translate-x-0 mt-5' : '-translate-x-full sm:translate-x-0 mt-5',
        ]"
        aria-label="Sidebar"
    >
        <div
            class="h-full flex flex-col justify-between px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800"
        >
            <ul class="space-y-2 font-medium">
                <!-- CLOSE SIDEBAR BUTTON -->
                <button
                    type="button"
                    @click="isSideBarOpen = !isSideBarOpen"
                    class="p-2 mb-1 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg
                        class="w-6 h-6"
                        aria-hidden="true"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10
               8.586l4.293-4.293a1 1 0 111.414
               1.414L11.414 10l4.293 4.293a1 1
               0 01-1.414 1.414L10 11.414l-4.293
               4.293a1 1 0 01-1.414-1.414L8.586
               10 4.293 5.707a1 1 0 010-1.414z"
                        />
                    </svg>
                </button>

                <li v-for="link in adminNavLinks" :key="link.route_name">
                    <Link
                        v-if="link.permitted"
                        :href="route(link.route_name)"
                        :class="[
                            'flex items-center p-2 rounded-lg group transition-colors',
                            isActive(link.route_name)
                                ? 'bg-green-600 text-white'
                                : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700',
                        ]"
                    >
                        <component
                            :is="link.icon"
                            :class="[
                                'w-5 h-5 transition duration-75',
                                isActive(link.route_name)
                                    ? 'text-white'
                                    : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white',
                            ]"
                        />

                        <span class="ms-3">{{ link.name }}</span>
                    </Link>
                </li>
            </ul>

            <!-- CTA -->
            <div class="p-4 mb-20 rounded-lg bg-green-600">
                <p class="mb-3 text-sm text-white">
                    Please ensure all patient records are up to date. Accurate data is critical for effective
                    diagnosis and treatment.
                </p>
                <Link
                    class="text-sm underline font-medium text-white hover:opacity-75"
                    :href="route('admin.patients')"
                >
                    Review patient records
                </Link>
            </div>
        </div>
    </aside>
</template>
