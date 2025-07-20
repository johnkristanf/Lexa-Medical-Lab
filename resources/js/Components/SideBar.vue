<script setup>
    import { usePage, Link } from '@inertiajs/vue3'
    import { computed } from 'vue'
    import { CalendarIcon, UserGroupIcon, UserIcon } from '@heroicons/vue/24/outline'

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
            page_url: '/admin/dashboard',
            permitted: permissions.value?.is_admin,
            icon: UserIcon,
        },

        {
            name: 'Patients',
            route_name: 'admin.patients',
            page_url: '/admin/patients',

            permitted: permissions.value?.is_admin,
            icon: UserGroupIcon,
        },
        {
            name: 'Appointments',
            route_name: 'admin.appointments',
            page_url: '/admin/appointments',
            permitted: permissions.value?.is_admin,
            icon: CalendarIcon,
        },
        {
            name: 'User',
            route_name: 'admin.user',
            page_url: '/admin/user',
            permitted: permissions.value?.is_admin,
            icon: UserIcon,
        },
    ])
</script>

<template>
    <button
        data-drawer-target="default-sidebar"
        data-drawer-toggle="default-sidebar"
        aria-controls="default-sidebar"
        type="button"
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
        class="fixed top-18 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidebar"
    >
        <div
            class="h-full flex flex-col justify-between px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800"
        >
            <ul class="space-y-2 font-medium">
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
            <div class="p-4 mb-16 rounded-lg bg-green-50 dark:bg-green-900" role="alert">
                <p class="mb-3 text-sm text-green-800 dark:text-green-400">
                    Please ensure all patient records are up to date. Accurate data is critical for
                    effective diagnosis and treatment.
                </p>
                <Link
                    class="text-sm text-green-800 underline font-medium hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    :href="route('admin.patients')"
                >
                    Review patient records
                </Link>
            </div>
        </div>
    </aside>

    <!-- <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div
                    class="flex items-center justify-center h-24 rounded-sm bg-gray-50 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center h-24 rounded-sm bg-gray-50 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center h-24 rounded-sm bg-gray-50 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
            </div>
            <div
                class="flex items-center justify-center h-48 mb-4 rounded-sm bg-gray-50 dark:bg-gray-800"
            >
                <p class="text-2xl text-gray-400 dark:text-gray-500">
                    <svg
                        class="w-3.5 h-3.5"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 18 18"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 1v16M1 9h16"
                        />
                    </svg>
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
            </div>
            <div
                class="flex items-center justify-center h-48 mb-4 rounded-sm bg-gray-50 dark:bg-gray-800"
            >
                <p class="text-2xl text-gray-400 dark:text-gray-500">
                    <svg
                        class="w-3.5 h-3.5"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 18 18"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 1v16M1 9h16"
                        />
                    </svg>
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
                <div
                    class="flex items-center justify-center rounded-sm bg-gray-50 h-28 dark:bg-gray-800"
                >
                    <p class="text-2xl text-gray-400 dark:text-gray-500">
                        <svg
                            class="w-3.5 h-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 1v16M1 9h16"
                            />
                        </svg>
                    </p>
                </div>
            </div>
        </div>
    </div> -->
</template>
