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
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.globals.labels[opts.seriesIndex] + ': ' + val.toFixed(1) + '%'
            },
            style: {
                fontSize: '18px',
                fontWeight: 'bold',
                colors: ['#fff'], // make text white for contrast
            },
            dropShadow: {
                enabled: true,
                top: 1,
                left: 1,
                blur: 1,
                color: '#000',
                opacity: 0.7,
            },
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    offset: -25, // move labels closer or farther inside
                    minAngleToShowLabel: 15, // avoid clutter if slice is too small
                },
            },
        },
        legend: {
            position: 'right',
            labels: {
                colors: '#fff', // white legend text for dark mode
            },
        },
        tooltip: {
            enabled: false,
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
                ...chartOptions.value,
                labels: data.map((item) => item.brand_name),
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
    <div class="mt-7 mb-12 p-6 max-w-6xl mx-auto rounded-lg shadow-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-600">Most Used Medical Supplies</h2>

        <!-- Filter Buttons -->
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
