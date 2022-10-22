<template>
  <v-card class="mt-3">
    <v-card-title>
      <h6 class="text-uppercase grey--text">Sell Last 30 Days</h6>
    </v-card-title>

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
export default {
  props: ["last_thirty_days_sell"],

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
          categories: this.last_thirty_days_sell.map((item) => item.sell_date),
        },
      },
      series: [
        {
          name: "Diesel",
          data: this.last_thirty_days_sell.map(
            (item) => item.diesel_sold_amount
          ),
        },
        {
          name: "Petrol",
          data: this.last_thirty_days_sell.map(
            (item) => item.petrol_sold_amount
          ),
        },
      ],
    };
  },
};
</script>