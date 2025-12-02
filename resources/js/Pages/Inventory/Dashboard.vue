<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head } from '@inertiajs/vue3'
    import { reactive } from 'vue'
    import { onMounted } from 'vue'
    import { useToast } from 'primevue/usetoast'
    import MostUsedSupplies from '@/Components/MostUsedSupplies.vue'
    import PatientAverage from '@/Components/PatientAverage.vue'

    // Props from controller
    const props = defineProps({
        supplies: Array,
        nearlyExpired: Array,
        patient_analytics: Array
    })

    const toast = useToast()

    onMounted(() => {
        if (props.supplies.length > 0) {
            toast.add({
                severity: 'warn',
                summary: 'Critical Stock Alert',
                detail: `${props.supplies.length} item(s) below critical stock.`,
                life: 5000,
            })
        }

        console.log("PATIENT:", props.patient_analytics);

    })

    onMounted(() => {
        if (props.nearlyExpired.length > 0) {
            toast.add({
                severity: 'warn',
                summary: 'Expiration Alert',
                detail: `${props.nearlyExpired.length} item(s) nearly expired.`,
                life: 5000,
            })
        }
    })

    // Toggles
    const toggles = reactive({
        showAddSupplyModal: false,
        showInventoryDrawer: false,
    })

    // Utility
    const daysLeft = (expiration) => {
        const now = new Date()
        const exp = new Date(expiration)
        const diffTime = exp.getTime() - now.getTime()
        const diffDays = Math.ceil(diffTime / (1000 * 3600 * 24))
        return diffDays
    }

    const formatDate = (date) => {
        const d = new Date(date)
        return d.toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        })
    }
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="flex flex-col lg:flex-row gap-6 w-[90%] mx-auto mt-[3%]">
            <!-- ✅ Low/Critical Stock -->
            <div class="flex-1 text-black p-6 rounded-lg shadow-2xl">
                <h2 class="text-gray-600 text-center mb-4 text-xl font-bold">
                    Nearly Out of Stock Medical Supplies
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="border-b border-white py-2">Brand Name</th>
                                <th class="border-b border-white py-2">Current Stock</th>
                                <th class="border-b border-white py-2">Critical Stock</th>
                                <th class="border-b border-white py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="supply in props.supplies" :key="supply.id">
                                <td class="py-2 px-4">{{ supply.brand_name }}</td>
                                <td class="py-2 px-4">{{ supply.quantity }}</td>
                                <td class="py-2 px-4">{{ supply.stocks?.[0]?.critical_stock ?? 'N/A' }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        :class="{
                                            'bg-[#d90429] text-white':
                                                supply.quantity <= (supply.stocks?.[0]?.critical_stock ?? 10),
                                            'bg-[#ffba08] text-black':
                                                supply.quantity > (supply.stocks?.[0]?.critical_stock ?? 10),
                                        }"
                                        class="px-2 py-1 rounded text-xs"
                                    >
                                        {{
                                            supply.quantity <= (supply.stocks?.[0]?.critical_stock ?? 10)
                                                ? 'Critical'
                                                : 'Low'
                                        }}
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="props.supplies.length === 0">
                                <td colspan="4" class="py-2 text-center text-gray-600">No low stock items</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✅ Nearly Expired Items -->
            <div class="flex-1 text-black p-6 rounded-lg shadow-2xl">
                <h2 class="text-gray-600 text-center mb-4 text-xl font-bold">
                    Nearly Expired Medical Supplies
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="border-b border-white py-2">Brand Name</th>
                                <th class="border-b border-white py-2">Expiration Date</th>
                                <th class="border-b border-white py-2">Batch Number</th>
                                <th class="border-b border-white py-2">Days Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="batch in props.nearlyExpired" :key="batch.id">
                                <td class="py-2 px-4">{{ batch.medical_supply?.brand_name ?? 'N/A' }}</td>
                                <td class="py-2 px-4">{{ formatDate(batch.expiration_date) }}</td>
                                <td class="py-2 px-4">{{ batch.batch_number }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        :class="{
                                            'bg-[#d90429] text-white': daysLeft(batch.expiration_date) <= 30,
                                            'bg-[#ffba08] text-black': daysLeft(batch.expiration_date) > 30,
                                        }"
                                        class="px-2 py-1 rounded text-xs"
                                    >
                                        {{ daysLeft(batch.expiration_date) }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="props.nearlyExpired.length === 0">
                                <td colspan="4" class="py-2 text-center text-gray-600">
                                    No nearly expired items
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ✅ Analytics side-by-side -->
        <div class="w-[90%] mx-auto mt-10 flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <MostUsedSupplies />
            </div>
            <div class="flex-1">
                <PatientAverage :patient_analytics="props.patient_analytics" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
    .custom-datatable ::v-deep(.p-datatable-thead > tr > th) {
        background-color: #208b3a;
        color: white;
    }

    .custom-datatable ::v-deep(.p-datatable-tbody > tr > td) {
        background-color: #ffffff;
        color: #374151;
    }
</style>
