<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    import { useToast } from 'primevue/usetoast'
    import { router } from '@inertiajs/vue3'
    import { ref } from 'vue'

    // PROPS DATA
    defineProps({
        roles: Array,
    })

    // TOAST INITIALIZATION
    const toast = useToast()

    // EMITS FOR MODAL HANDLING
    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    // FORM DATA
    const formData = ref({
        name: '',
        email: '',
        role: '',
        password: '',
    })

    // FORM VALIDATION
    function validateForm() {
        if (!formData.value.name.trim()) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Please enter a name',
                life: 1500,
            })
            return false
        }

        if (!formData.value.email.trim()) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Please enter an email address',
                life: 1500,
            })
            return false
        }

        // Basic email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (!emailRegex.test(formData.value.email)) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Please enter a valid email address',
                life: 1500,
            })
            return false
        }

        if (!formData.value.role) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Please select a role',
                life: 1500,
            })
            return false
        }

        if (!formData.value.password.trim()) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Please enter a password',
                life: 1500,
            })
            return false
        }

        if (formData.value.password.length < 6) {
            toast.add({
                severity: 'warn',
                summary: 'Validation Error',
                detail: 'Password must be at least 6 characters long',
                life: 1500,
            })
            return false
        }

        return true
    }

    // FORM SUBMISSION
    function submitForm() {
        if (!validateForm()) {
            return
        }

        console.log('Submitting User:', formData.value)

        router.post(route('admin.user.add'), formData.value, {
            preserveScroll: true,
            onSuccess: (page) => {
                console.log('page', page)
                if (page.props.flash?.success) {
                    toast.add({
                        severity: 'success',
                        summary: 'User Created Successfully',
                        detail: `User ${page.props.flash?.user.name} has been added`,
                        life: 1500,
                    })

                    // Reset form
                    formData.value = {
                        name: '',
                        email: '',
                        role: '',
                        password: '',
                    }

                    setTimeout(() => {
                        closeModal()
                    }, 1500)
                }
            },
            onError: (error) => {
                console.log('User Creation Error:', error)

                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to create user. Please check your input and try again.',
                    life: 1500,
                })
            },
        })
    }
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-[999]">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <div class="border-b border-gray-100 pb-4 mb-6">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-6 h-6 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <DialogTitle
                                            as="h1"
                                            class="text-2xl font-semibold leading-6 text-gray-900"
                                        >
                                            Add New User
                                        </DialogTitle>
                                        <DialogDescription
                                            class="text-sm leading-6 text-gray-500 mt-1"
                                        >
                                            Create a new user account with role-based access
                                        </DialogDescription>
                                    </div>
                                </div>
                            </div>

                            <!-- USER FORM -->
                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- NAME FIELD -->
                                <div>
                                    <label
                                        for="name"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Full Name
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <input
                                            id="name"
                                            type="text"
                                            v-model="formData.name"
                                            placeholder="Enter full name"
                                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                            required
                                        />
                                    </div>
                                </div>

                                <!-- EMAIL FIELD -->
                                <div>
                                    <label
                                        for="email"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Email Address
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                                                ></path>
                                            </svg>
                                        </div>
                                        <input
                                            id="email"
                                            type="text"
                                            v-model="formData.email"
                                            placeholder="Enter email address"
                                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                            required
                                        />
                                    </div>
                                </div>

                                <!-- ROLE FIELD -->
                                <div>
                                    <label
                                        for="role"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Role
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <select
                                            id="role"
                                            v-model="formData.role"
                                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 appearance-none bg-white"
                                            required
                                        >
                                            <option value="">Select a role</option>
                                            <option
                                                v-for="role in roles"
                                                :key="role.id"
                                                :value="role.id"
                                            >
                                                {{
                                                    role.name
                                                        .replace(/_/g, ' ')
                                                        .replace(/\b\w/g, (l) => l.toUpperCase())
                                                }}
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- PASSWORD FIELD -->
                                <div>
                                    <label
                                        for="password"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Password
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <input
                                            id="password"
                                            type="password"
                                            v-model="formData.password"
                                            placeholder="Enter password"
                                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                            required
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Password must be at least 6 characters long
                                    </p>
                                </div>

                                <!-- FORM ACTIONS -->
                                <div
                                    class="flex justify-end space-x-3 pt-6 border-t border-gray-100"
                                >
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-6 py-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 flex items-center"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
