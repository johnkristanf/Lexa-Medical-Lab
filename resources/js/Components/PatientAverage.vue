<script setup>
    import ApexChart from 'vue3-apexcharts'
    import { ref, computed } from 'vue'

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

    const series = computed(() => filteredData.value.map((p) => Number(p.total)))

    const labels = computed(() => filteredData.value.map((p) => `${codeToLabel[p.code] ?? 'Unknown'}`))

    const chartOptions = computed(() => ({
        chart: {
            type: 'pie',
        },

        labels: labels.value,

        legend: {
            show: true,
            position: 'left',
            fontSize: '14px',
            horizontalAlign: 'left',
            offsetX: 0,
            offsetY: 0,
            markers: {
                width: 12,
                height: 12,
                radius: 12,
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5,
            },
            formatter: (seriesName, opts) => {
                const value = opts.w.globals.series[opts.seriesIndex]
                return `${seriesName}: ${value}`
            },
        },

        dataLabels: {
            enabled: true,
            formatter: (val, opts) => {
                const label = opts.w.globals.labels[opts.seriesIndex]
                const value = opts.w.globals.series[opts.seriesIndex]
                return `${label}: ${value}`
            },
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                colors: ['#fff'],
            },
        },

        colors: ['#00b4d8', '#36a2eb', '#ff6384', '#ff9f40'],

        responsive: [
            {
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        ],
    }))
</script>

<template>
    <div class="mt-7 mb-12 p-6 max-w-6xl mx-auto rounded-lg shadow-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-600">Patient Statistics</h2>

        <div class="flex justify-center gap-3 mb-4">
            <button
                v-for="type in filters"
                :key="type"
                class="px-3 py-1 rounded-md text-sm capitalize"
                :class="filterType === type ? 'bg-green-600 text-white' : 'bg-gray-200 text-black'"
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

        <div class="px-4">
            <ApexChart type="pie" height="350" :options="chartOptions" :series="series" />
        </div>
    </div>
</template>
