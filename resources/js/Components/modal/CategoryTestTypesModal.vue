<script setup>
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'
    import { ref } from 'vue'
    import TestTypesModal from '@/Components/modal/TestTypesModal.vue'
    import EditTestTypeModal from '@/Components/modal/EditTestTypeModal.vue'

    const props = defineProps({
        category: Object,
    })

    const emit = defineEmits(['close'])
    const closeModal = () => {
        if (showAddModal.value || editingTestType.value) return
        emit('close')
    }

    const showAddModal = ref(false)
    const editingTestType = ref(null)

    const editTestType = (test) => {
        editingTestType.value = test
    }
</script>

<template>
    <TransitionRoot appear :show="true">
        <Dialog as="div" @close="closeModal" class="relative z-10">
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
                            class="w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <DialogTitle class="text-xl font-semibold text-gray-900">
                                        Test Types for {{ category?.name }}
                                    </DialogTitle>
                                    <DialogDescription class="text-sm text-gray-500 mt-1">
                                        View and manage test types under this category.
                                    </DialogDescription>
                                </div>
                                <button
                                    type="button"
                                    @click="showAddModal = true"
                                    class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                                >
                                    + Add New
                                </button>
                            </div>

                            <div class="mt-4 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th
                                                scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                            >
                                                Name
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Reference Range
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                                Unit
                                            </th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 text-left">
                                        <tr v-for="test in category?.test_types" :key="test.id">
                                            <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"
                                            >
                                                {{ test.name }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ test.reference_range || '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ test.unit || '-' }}
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6"
                                            >
                                                <button
                                                    type="button"
                                                    @click="editTestType(test)"
                                                    class="text-green-600 hover:text-green-900 font-semibold"
                                                >
                                                    Edit
                                                    <span class="sr-only">, {{ test.name }}</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!category?.test_types || category?.test_types.length === 0">
                                            <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                                                No test types found for this category.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button
                                    type="button"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                                    @click="closeModal"
                                >
                                    Close
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
            <TestTypesModal v-if="showAddModal" :category="category" @close="showAddModal = false" />
            <EditTestTypeModal
                v-if="editingTestType"
                :testType="editingTestType"
                @close="editingTestType = null"
            />
        </Dialog>
    </TransitionRoot>
</template>
