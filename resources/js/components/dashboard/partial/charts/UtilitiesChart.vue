<template>
  <v-card>
    <v-card-subtitle class="font-weight-bold">
      Utilities during current year
    </v-card-subtitle>

    <v-card-text>
      <apexchart height="350" :options="options" :series="series"></apexchart>
    </v-card-text>
  </v-card>
</template>

<script>
import _ from "lodash";

export default {
  props: ["yearlyUtilities"],

  data() {
    return {
      series: [
        {
          data: Object.values(this.yearlyUtilities),
        },
      ],
      options: {
        chart: {
          type: "bar",
          height: 350,
          toolbar: {
            show: true,
            tools: {
              download: true,
              selection: false,
              zoom: false,
              zoomin: false,
              zoomout: false,
              pan: false,
              reset: false,
              customIcons: [],
            },
          },
        },

        fill: {
          type: "gradient",
          gradient: {
            shade: "light",
            type: "horizontal",
            shadeIntensity: 0.5,
            gradientToColors: ["#2397b5", "#870cf2"], // optional, if not defined - uses the shades of same color in series
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 100],
            colorStops: [],
          },
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: true,
          },
        },
        dataLabels: {
          enabled: false,
        },
        xaxis: {
          categories: Object.keys(this.yearlyUtilities),
        },
      },
    };
  },
};
</script>