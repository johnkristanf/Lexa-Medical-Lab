<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const filterType = ref('all')
const filters = ['all', 'year', 'month', 'day']
const supplies = ref([])

const CHART_COLORS = [
  '#00b4d8',
  '#36a2eb',
  '#ff6384',
  '#ff9f40',
  '#4bc0c0',
]

// 🔹 Fetch from Laravel
const fetchChartData = async () => {
  try {
    const { data } = await axios.get(route('most.used.supply'), {
      params: { filter: filterType.value },
    })

    supplies.value = data ?? []
  } catch (e) {
    console.error(e)
    supplies.value = []
  }
}

onMounted(fetchChartData)
watch(filterType, fetchChartData)

// 🔹 Chart Data (Chart.js style)
const chartData = computed(() => {
  if (!supplies.value.length) {
    return {
      labels: ['No Data'],
      datasets: [
        {
          data: [1],
          backgroundColor: ['#e5e7eb'],
          borderWidth: 0,
        },
      ],
    }
  }

  return {
    labels: supplies.value.map(s => s.name ?? 'Unknown'),
    datasets: [
      {
        data: supplies.value.map(s => Number(s.total_quantity ?? 0)),
        backgroundColor: CHART_COLORS.slice(0, supplies.value.length),
        borderWidth: 0,
      },
    ],
  }
})

// 🔹 Chart Options
const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'right',
      labels: {
        usePointStyle: true,
        font: { size: 14 },
        color: '#374151',
      },
    },
    tooltip: {
      callbacks: {
        label(context) {
          const value = context.raw
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const pct = total ? ((value / total) * 100).toFixed(1) : 0
          return `${context.label}: ${value} (${pct}%)`
        },
      },
    },
  },
}))
</script>

<template>
  <div class="mt-7 mb-12 p-6 max-w-6xl mx-auto rounded-lg shadow-2xl">
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-600">
      Most Used Medical Supplies
    </h2>

    <!-- Filters -->
    <div class="flex justify-center gap-3 mb-4">
      <button
        v-for="type in filters"
        :key="type"
        class="px-3 py-1 rounded-md text-sm capitalize"
        :class="filterType === type
          ? 'bg-green-600 text-white'
          : 'bg-gray-200 text-black'"
        @click="filterType = type"
      >
        {{
          type === 'all'
            ? 'All Time'
            : type === 'year'
              ? 'This Year'
              : type === 'month'
                ? 'This Month'
                : 'Today'
        }}
      </button>
    </div>

    <div style="height: 350px">
      <Pie :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>
