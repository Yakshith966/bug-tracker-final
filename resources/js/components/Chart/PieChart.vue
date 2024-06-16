<template>
    <div>
      <h3>{{ title }}</h3>
      <Bar :data="chartData" :options="chartOptions"></Bar>
    </div>
  </template>
  
  <script>
  import { Bar } from 'vue-chartjs';
  import { Chart, registerables } from 'chart.js';
  
  Chart.register(...registerables);
  
  export default {
    name: 'BugChart',
    components: {
      Bar,
    },
    props: {
      title: {
        type: String,
        default: 'Bugs Chart',
      },
      openBugs: {
        type: Number,
        default: 0,
      },
      closedBugs: {
        type: Number,
        default: 0,
      },
    },
    computed: {
      chartData() {
        return {
          labels: ['Open Bugs', 'Closed Bugs'],
          datasets: [
            {
              label: 'Bugs',
              backgroundColor: ['#ff6384', '#36a2eb'],
              data: [this.openBugs, this.closedBugs],
            },
          ],
        };
      },
      chartOptions() {
        return {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        };
      },
    },
  };
  </script>
  
  <style scoped>
  div {
    max-width: 600px;
    margin: auto;
  }
  </style>
  