<script setup>
    import { ref, computed } from 'vue'
    import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
    import { Pie } from 'vue-chartjs'

    ChartJS.register(ArcElement, Tooltip, Legend)

    const props = defineProps({
        patient_analytics: {
            type: Array,
            default: () => [],
        },
    })

    const filters = ['all', 'regular', 'pwd', 'senior', 'pregnant']
    const filterType = ref('all')

    const codeToLabel = {
        RP: 'Regular Patient',
        PWD: 'PWD',
        SC: 'Senior Citizen',
        PW: 'Pregnant Women',
    }

    const CHART_COLORS = ['#00b4d8', '#36a2eb', '#ff6384', '#ff9f40']

    const filteredData = computed(() => {
        if (filterType.value === 'all') return props.patient_analytics
        return props.patient_analytics.filter((p) => {
            if (filterType.value === 'regular') return p.code === 'RP'
            if (filterType.value === 'pwd') return p.code === 'PWD'
            if (filterType.value === 'senior') return p.code === 'SC'
            if (filterType.value === 'pregnant') return p.code === 'PW'
            return false
        })
    })

    const chartData = computed(() => {
        const labels = filteredData.value.map(
            (p) => codeToLabel[p.code] ?? 'Unknown'
        )
        const data = filteredData.value.map((p) => Number(p.total))

        if (data.length === 0) {
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
            labels,
            datasets: [
                {
                    data,
                    backgroundColor: CHART_COLORS.slice(0, data.length),
                    borderWidth: 0,
                },
            ],
        }
    })

    const chartOptions = computed(() => ({
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'left',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 16,
                    font: {
                        size: 14,
                        weight: '500',
                    },
                    color: '#374151',
                    generateLabels: (chart) => {
                        const datasets = chart.data.datasets
                        const data = chart.data
                        if (!datasets.length || !data.labels) return []
                        return data.labels.map((label, i) => {
                            const value = datasets[0].data[i]
                            const isPlaceholder = label === 'No Data'
                            const text = isPlaceholder
                                ? 'No Data'
                                : value
                                  ? `${label}: ${value}`
                                  : label
                            return {
                                text,
                                fillStyle: datasets[0].backgroundColor[i],
                                strokeStyle: datasets[0].backgroundColor[i],
                                lineWidth: 1,
                                hidden: false,
                                index: i,
                            }
                        })
                    },
                },
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const label = context.label || ''
                        const value = context.raw || 0
                        const total = context.dataset.data.reduce((a, b) => a + b, 0)
                        const pct = total ? ((value / total) * 100).toFixed(1) : 0
                        return `${label}: ${value} (${pct}%)`
                    },
                },
            },
        },
    }))
</script>

<template>
    <div class="mt-7 mb-12 p-6 max-w-6xl mx-auto rounded-lg shadow-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-600">
            Patient Statistics
        </h2>

        <div class="flex justify-center gap-3 mb-4">
            <button
                v-for="type in filters"
                :key="type"
                class="px-3 py-1 rounded-md text-sm capitalize"
                :class="
                    filterType === type ? 'bg-green-600 text-white' : 'bg-gray-200 text-black'
                "
                @click="filterType = type"
            >
                {{
                    type === 'all'
                        ? 'All'
                        : type === 'regular'
                          ? 'Regular Patient'
                          : type === 'pwd'
                            ? 'PWD'
                            : type === 'senior'
                              ? 'Senior Citizen'
                              : type === 'pregnant'
                                ? 'Pregnant Women'
                                : type
                }}
            </button>
        </div>

        <div class="px-4" style="height: 350px">
            <Pie :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
