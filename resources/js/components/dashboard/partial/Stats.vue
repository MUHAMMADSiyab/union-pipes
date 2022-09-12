<template>
  <v-row>
    <v-col cols="12">
      <v-card elevation="2">
        <v-card-title primary-title>Overall Statistics</v-card-title>

        <v-card-text class="mt-1">
          <v-row>
            <v-col v-for="(data, i) in cardsData" :key="i">
              <StatsCard
                :color="data.color"
                :icon="data.icon"
                :title="data.title"
                :number="data.number"
              />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import StatsCard from "./StatsCard.vue";

export default {
  props: ["totals"],

  components: { StatsCard },

  data() {
    return {
      cardsData: [
        {
          color: "purple",
          icon: "mdi-car-arrow-right",
          title: "Purchased",
          number: this.totals.purchasedVehiclesCount,
        },

        {
          color: "success",
          icon: "mdi-car-arrow-left",
          title: "Sold",
          number: this.totals.soldVehiclesCount,
        },

        {
          color: "orange",
          icon: "mdi-warehouse",
          title: "In-stock",
          number: this.totals.inStockVehiclesCount,
        },

        {
          color: "indigo",
          icon: "mdi-car-settings",
          title: "Maintenanced",
          number: this.totals.vehiclesMaintenancesCount,
        },

        {
          color: "info",
          icon: "mdi-file-document-outline",
          title: "Files Issued",
          number: this.totals.issuedFilesVehiclesCount,
        },
      ],
    };
  },
};
</script>