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

    // Compute filtered data
    const filteredData = computed(() => {
        if (filterType.value === 'all') {
            return props.patient_analytics
        }
        return props.patient_analytics.filter((p) => {
            if (filterType.value === 'regular') return p.code === 'RP'
            if (filterType.value === 'pwd') return p.code === 'PWD'
            if (filterType.value === 'senior') return p.code === 'SC'
            if (filterType.value === 'pregnant') return p.code === 'PW'
            return false
        })
    })

    // Compute series and labels
    const series = computed(() => filteredData.value.map((p) => p.total))
    const labels = computed(() => filteredData.value.map((p) => codeToLabel[p.code] ?? 'Unknown'))

    // Chart options with computed labels
    const chartOptions = computed(() => ({
        labels: labels.value,
        colors: ['#00b4d8', '#36a2eb', '#ff6384', '#ff9f40', '#4bc0c0'],
        dataLabels: {
            enabled: true,
            formatter: (val, opts) => {
                // Get the raw number from series instead of percentage
                const rawValue = opts.w.globals.series[opts.seriesIndex]
                return opts.w.globals.labels[opts.seriesIndex] + ': ' + rawValue
            },
            style: { fontSize: '18px', fontWeight: 'bold', colors: ['#fff'] },
            dropShadow: { enabled: true, top: 1, left: 1, blur: 1, color: '#000', opacity: 0.7 },
        },
        plotOptions: {
            pie: { dataLabels: { offset: -25, minAngleToShowLabel: 15 } },
        },
        legend: {
            position: 'right',
            labels: { colors: '#fff' },
            formatter: (seriesName, opts) => {
                // Show raw number in legend as well
                const rawValue = opts.w.globals.series[opts.seriesIndex]
                return seriesName + ': ' + rawValue
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: (val) => val + ' patients'
            }
        },
    }))
</script>

<template>
    <div class="mt-7 mb-12 p-6 max-w-6xl mx-auto rounded-lg shadow-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-600">Average Patient Analytics</h2>

        <div class="flex justify-center gap-3 mb-4">
            <button
                v-for="type in filters"
                :key="type"
                class="px-3 py-1 rounded-md text-sm capitalize"
                :class="filterType === type ? 'bg-green-600 text-white' : 'bg-gray-200 text-black'"
                @click="filterType = type"
            >
                {{
                    type === 'all' ? 'All' :
                    type === 'regular' ? 'Regular Patient' :
                    type === 'pwd' ? 'PWD' :
                    type === 'senior' ? 'Senior Citizen' :
                    type === 'pregnant' ? 'Pregnant Women' : type
                }}
            </button>
        </div>

        <ApexChart type="pie" height="350" :options="chartOptions" :series="series" />
    </div>
</template>
