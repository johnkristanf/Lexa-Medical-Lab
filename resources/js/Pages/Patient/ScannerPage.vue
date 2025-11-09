<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { Drawer } from 'primevue'
    import {
        FwbTable,
        FwbTableBody,
        FwbTableCell,
        FwbTableHead,
        FwbTableHeadCell,
        FwbTableRow,
    } from 'flowbite-vue'

    import { reactive, ref } from 'vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import { OPERATION_TYPES } from '@/Enums/Inventory'
    import PatientDetailsModal from '@/Components/modal/PatientDetailsModal.vue'
    import TestModal from '@/Components/modal/TestModal.vue'
    import EmailResultReminder from '@/Components/modal/EmailResultReminder.vue'
    import UpdatePatientDetails from '@/Components/modal/UpdatePatientDetails.vue'
    import AddButton from '@/Components/AddButton.vue'
    import { formatDate } from '@/helpers/formatter'

    const props = defineProps({
        patients: Array,
        inventory_logs: Array,
        testTypesPurpose: Array,
        testTypesRequest: Array,
        testCategory: Array,
        testType: Array,
        patientUpdate: Array,
    })

    const selectedFile = ref(null)
    const previewUrl = ref(null)
    const isScanning = ref(false)
</script>

<template>
    <Head title="Scanner Page" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Image Scan</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Upload Section -->
                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Upload Image for Scanning</h3>

                            <div class="flex flex-col items-center justify-center w-full">
                                <label
                                    for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-green-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-green-50 transition-colors"
                                >
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg
                                            class="w-10 h-10 mb-3 text-green-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                            ></path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-700">
                                            <span class="font-semibold">Click to upload</span>
                                            or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-600">PNG, JPG, JPEG (MAX. 10MB)</p>
                                    </div>
                                    <input id="dropzone-file" type="file" class="hidden" accept="image/*" />
                                </label>
                            </div>

                            <!-- File Info Display -->
                            <div
                                v-if="selectedFile"
                                class="mt-4 p-4 bg-green-50 border border-green-300 rounded-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg
                                            class="w-8 h-8 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            ></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">filename.jpg</p>
                                            <p class="text-xs text-gray-600">2.4 MB</p>
                                        </div>
                                    </div>
                                    <button class="text-red-500 hover:text-red-700 transition-colors">
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div v-if="previewUrl" class="mb-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Image Preview</h3>
                            <div class="flex justify-center p-4 bg-white border border-green-200 rounded-lg">
                                <img src="" alt="Preview" class="max-h-96 rounded-lg shadow-md" />
                            </div>
                        </div>

                        <!-- Scan Button -->
                        <div class="flex justify-center mb-8">
                            <button
                                class="px-8 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors font-medium flex items-center space-x-2"
                                :disabled="!selectedFile || isScanning"
                            >
                                <svg
                                    v-if="!isScanning"
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    ></path>
                                </svg>
                                <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <span>{{ isScanning ? 'Scanning...' : 'Start Scan' }}</span>
                            </button>
                        </div>

                        <!-- Results Section -->
                        <div class="mt-8 border-t pt-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Scan Results</h3>
                            <div class="bg-gray-50 rounded-lg p-6 text-center text-gray-500">
                                <svg
                                    class="w-16 h-16 mx-auto mb-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                                <p class="text-sm">
                                    No scan results yet. Upload and scan an image to see results.
                                </p>
                            </div>

                            <!-- Example of Results Display-->
                            <div class="hidden space-y-4">
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-base font-semibold text-gray-900 mb-2">
                                                Detected Object 1
                                            </h4>
                                            <div class="space-y-1">
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Confidence:</span>
                                                    95.2%
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Category:</span>
                                                    Medical Equipment
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Location:</span>
                                                    Top-left region
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full"
                                        >
                                            High Confidence
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-base font-semibold text-gray-900 mb-2">
                                                Detected Object 2
                                            </h4>
                                            <div class="space-y-1">
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Confidence:</span>
                                                    78.5%
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Category:</span>
                                                    Laboratory Item
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Location:</span>
                                                    Center region
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full"
                                        >
                                            Medium Confidence
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
