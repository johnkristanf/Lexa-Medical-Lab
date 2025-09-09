<script setup>
import ApexCharts from 'vue3-apexcharts'
import { ref, computed } from 'vue'

const props = defineProps({
  inventory_logs: {
    type: Array,
    default: () => [],
  },
})

const filterType = ref('month') // all, year, month, day

// ✅ Filter logs by date (with created_at formatted as Y-m-d from Laravel)
const filteredLogs = computed(() => {
  const today = new Date()
  const todayStr = today.toISOString().slice(0, 10)       // "2025-09-03"
  const monthStr = today.toISOString().slice(0, 7)        // "2025-09"
  const yearStr = today.getFullYear().toString()          // "2025"

  return props.inventory_logs.filter(log => {
    const date = log.created_at // already "YYYY-MM-DD" from backend

    if (filterType.value === 'year') {
      return date.startsWith(yearStr) // ✅ matches "2025"
    }
    if (filterType.value === 'month') {
      return date.slice(0, 7) === monthStr // ✅ matches "2025-09"
    }
    if (filterType.value === 'day') {
      return date === todayStr // ✅ matches "2025-09-03"
    }
    return true // all time
  })
})

//  Group usage by medical supply
const usageData = computed(() => {
  const usageMap = {}

  filteredLogs.value.forEach(log => {
    if (log.operation_type?.toLowerCase() === 'deducted') {
      const item = log.medical_supplies?.brand_name || 'Unknown Item'
      const qty = log.total_quantity ?? log.quantity ?? 0
      usageMap[item] = (usageMap[item] || 0) + qty
    }
  })

  return usageMap
})

//  Chart data
const series = computed(() => Object.values(usageData.value))
const labels = computed(() => Object.keys(usageData.value))

const chartOptions = computed(() => ({
  labels: labels.value,
  legend: { position: 'bottom' },
  dataLabels: { enabled: true },
  colors: ['#00b4d8', '#36a2eb', '#ff6384', '#ff9f40', '#4bc0c0'],
}))
</script>

<template>
  <div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4 text-center text-black">
      Most Used Medical Supplies
    </h2>

    <!-- Filter Buttons -->
    <div class="flex justify-center gap-3 mb-4">
      <button
        v-for="type in ['all', 'year', 'month', 'day']"
        :key="type"
        class="px-3 py-1 rounded-md text-sm capitalize"
        :class="filterType === type ? 'bg-blue-600 text-white' : 'bg-gray-200 text-black'"
        @click="filterType = type"
      >
        {{
          type === 'all' ? 'All Time'
          : type === 'year' ? 'This Year'
          : type === 'month' ? 'This Month'
          : 'Today'
        }}
      </button>
    </div>

    <!-- Pie Chart -->
    <apexchart
      type="pie"
      height="350"
      :options="chartOptions"
      :series="series"
    />

    <!-- Fallback message -->
    <p v-if="!series.length" class="text-center text-gray-500 mt-3">
      No deduction data available.
    </p>
  </div>
</template>
