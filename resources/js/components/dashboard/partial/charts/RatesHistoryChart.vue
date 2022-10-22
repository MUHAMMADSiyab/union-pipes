<template>
  <v-card class="mt-3">
    <v-card-title>
      <h6 class="text-uppercase grey--text">Rates History of Last 2 Years</h6>
    </v-card-title>
    <v-card-text>
      <apexchart
        type="bar"
        height="350"
        :options="options"
        :series="series"
      ></apexchart>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["rates_history"],

  data() {
    return {
      options: {
        chart: {
          type: "bar",
          height: 350,
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "55%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          show: true,
          width: 2,
          colors: ["transparent"],
        },
        xaxis: {
          categories: this.rates_history.dates,
        },
        yaxis: {
          title: {
            text: "Per Litre Price",
          },
        },
        fill: {
          opacity: 1,
        },

        fill: {
          type: "gradient",
          gradient: {
            shade: "dark",
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            stops: [50, 0, 100],
          },
        },
      },

      series: this.rates_history.data.map((rate) => ({
        name: rate.product,
        data: rate.rates,
      })),
    };
  },
};
</script>