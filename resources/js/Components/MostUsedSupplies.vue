<script setup>
    import ApexChart from 'vue3-apexcharts'
    import { ref, computed, watch, onMounted } from 'vue'
    import axios from 'axios'

    const filterType = ref('all')
    const filters = ['all', 'year', 'month', 'day']

    // options should be reactive because we'll update labels
    const chartOptions = ref({
        labels: [],
        colors: ['#00b4d8', '#36a2eb', '#ff6384', '#ff9f40', '#4bc0c0'],
        legend: { position: 'bottom' },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.globals.labels[opts.seriesIndex] + ': ' + val.toFixed(1) + '%'
            },
        },
        tooltip: {
            enabled: false, // 🚫 disables hover tooltips
        },
    })

    const series = ref([])

    const fetchChartData = async () => {
        try {
            const { data } = await axios.get(route('most.used.supply'), {
                params: { filter: filterType.value },
            })

            console.log('data: ', data)

            series.value = data.map((item) => Number(item.total_quantity))

            chartOptions.value = {
                ...chartOptions.value, // keep existing options (colors, legend, etc.)
                labels: data.map((item) => item.brand_name), // reassign labels
            }
        } catch (error) {
            console.error('Error fetching chart data:', error)
        }
    }

    // fetch on mount
    onMounted(fetchChartData)

    // re-fetch when filter changes
    watch(filterType, () => fetchChartData())
</script>

<template>
    <div class="p-6 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-center text-black">Most Used Medical Supplies</h2>

        <!-- Filter Buttons -->
        <div class="flex justify-center gap-3 mb-4">
            <button
                v-for="type in filters"
                :key="type"
                class="px-3 py-1 rounded-md text-sm capitalize"
                :class="filterType === type ? 'bg-blue-600 text-white' : 'bg-gray-200 text-black'"
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

        <!-- Pie Chart -->
        <ApexChart type="pie" height="350" :options="chartOptions" :series="series" />
    </div>
</template>
