<script setup>
    import { formatDate } from '@/helpers/formatter'
    import {
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
    } from '@headlessui/vue'

    const emit = defineEmits(['close'])
    const closeModal = () => emit('close')

    const props = defineProps({
        supplies_requested: Object,
    })

    console.log('supplies_requested modal: ', props.supplies_requested)
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
                            class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle
                                as="h1"
                                class="text-2xl font-medium leading-6 text-gray-900"
                            >
                                Requested Medical Supply
                            </DialogTitle>

                            <DialogDescription class="text-sm font-medium leading-6 text-gray-400">
                                Request Details Below
                            </DialogDescription>

                            <div class="flex flex-col gap-5 isolate lg:px-8 mt-5">
                                <div
                                    v-for="supplies in props.supplies_requested"
                                    class="border-2 border-gray-300 p-3 rounded-md"
                                >
                                    <div class="flex items-center justify-between text-md">
                                        <h1 >
                                            Brand Name: {{ supplies.brand_name }}
                                        </h1>

                                        <h1 class="text-gray-400">
                                            {{ formatDate(supplies.pivot.created_at) }}
                                        </h1>
                                    </div>

                                    <p class="text-sm text-gray-500">
                                        - {{ supplies.pivot.quantity }} {{ supplies.unit }}
                                    </p>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<style scoped>
    .form-input {
        @apply block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-green-600;
    }
</style>
