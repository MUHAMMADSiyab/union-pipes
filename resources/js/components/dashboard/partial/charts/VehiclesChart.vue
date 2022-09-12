<template>
  <v-card>
    <v-card-subtitle class="font-weight-bold">
      Vehicles Purchase & Sale during current year
    </v-card-subtitle>

    <v-card-text>
      <apexchart
        type="area"
        height="350"
        :options="options"
        :series="series"
      ></apexchart>
    </v-card-text>
  </v-card>
</template>

<script>
import _ from "lodash";

export default {
  props: ["yearlyTotals"],

  data() {
    return {
      options: {
        chart: {
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
          stroke: {
            curve: "smooth",
            width: 1,
          },
          markers: {
            size: 1,
          },
        },
        xaxis: {
          categories: Object.keys(this.yearlyTotals),
        },
      },
      series: [
        {
          name: "Purchased Vehicles",
          data: this.getTotals("purchasedVehiclesCount"),
        },
        {
          name: "Sold Vehicles",
          data: this.getTotals("soldVehiclesCount"),
        },
      ],
    };
  },

  methods: {
    getTotals(type) {
      const vehiclesTotals = [];

      _.toArray(this.yearlyTotals).forEach((d) => {
        vehiclesTotals.push(d[type]);
      });

      return vehiclesTotals;
    },
  },
};
</script>