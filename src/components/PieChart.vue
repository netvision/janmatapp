<template>
  <div class="pie-chart-container">
    <canvas 
      ref="chartCanvas" 
      :width="width" 
      :height="height"
    ></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import { Chart, registerables } from 'chart.js'

// Register Chart.js components
Chart.register(...registerables)

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  labels: {
    type: Array,
    required: true
  },
  colors: {
    type: Array,
    default: () => [
      '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
      '#06B6D4', '#EC4899', '#84CC16', '#F97316', '#6366F1'
    ]
  },
  width: {
    type: Number,
    default: 300
  },
  height: {
    type: Number,
    default: 300
  },
  showLegend: {
    type: Boolean,
    default: false
  }
})

const chartCanvas = ref(null)
let chartInstance = null

const renderChart = () => {
  if (!chartCanvas.value || !props.data.length) return

  const ctx = chartCanvas.value.getContext('2d')
  
  // Destroy existing chart if it exists
  if (chartInstance) {
    chartInstance.destroy()
  }

  const chartData = {
    labels: props.labels,
    datasets: [{
      data: props.data,
      backgroundColor: props.labels.map((_, index) => props.colors[index % props.colors.length]),
      borderWidth: 2,
      borderColor: '#ffffff'
    }]
  }

  chartInstance = new Chart(ctx, {
    type: 'pie',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: props.showLegend,
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const value = context.parsed
              const total = context.dataset.data.reduce((a, b) => a + b, 0)
              const percentage = ((value / total) * 100).toFixed(1)
              return `${context.label}: ${value} (${percentage}%)`
            }
          }
        }
      }
    }
  })
}

// Watch for data changes
watch(() => [props.data, props.labels], () => {
  renderChart()
}, { deep: true })

onMounted(() => {
  renderChart()
})

onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>

<style scoped>
.pie-chart-container {
  position: relative;
}
</style>
