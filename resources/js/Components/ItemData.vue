<script setup>
import ApexCharts from 'vue3-apexcharts'

const series = [120, 110, 98, 87, 72] // Usage count
const labels = ['Syringe 5ml', 'Alcohol Pads', 'Gloves (Medium)', 'Cotton Balls', 'Bandage Roll']

const chartOptions = {
  labels,
  colors: ['#00b4d8', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
  legend: {
    position: 'bottom',
  },
  dataLabels: {
    enabled: true,
    formatter: function (val, opts) {
          const label = labels[opts.seriesIndex];
      const percent = val.toFixed(1) + '%';
      return `${label}\n${percent}`;
      return labels[opts.seriesIndex]; // Just show the item name
    },
    style: {
      fontSize: '14px',
      fontWeight: 'bold',
      colors: ['#fff'],
    },
    dropShadow: {
      enabled: true,
      top: 1,
      left: 1,
      blur: 1,
      opacity: 0.9,
    },
    // Position closer to center
    offsetX: 0,
    offsetY: 0,
    textAnchor: 'middle',
  },
  plotOptions: {
    pie: {
      dataLabels: {
        offset: -20, // keep labels centered
      }
    }
  },
  responsive: [
    {
      breakpoint: 480,
      options: {
        chart: { width: 350},
        legend: { position: 'bottom' },
      },
    },
  ],
}


</script>

<script>
export default {
  components: {
    apexchart: ApexCharts,
  },
}
</script>

<template>
  <div class="p-6 max-w-xl mx-auto">
    <h2 class="text-2xl font-bold mb-4 text-center text-black">Most Usable Items</h2>
    <apexchart
      width="100%"
      type="pie"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>
