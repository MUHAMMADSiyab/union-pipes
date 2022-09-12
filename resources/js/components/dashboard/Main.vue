<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <Stats
            :totals="dashboardData.totals"
            v-if="dashboardData && dashboardData.totals"
          />
        </v-col>
      </v-row>

      <ProfitCards
        :profits="dashboardData.profits"
        v-if="dashboardData && dashboardData.profits"
      />

      <v-row>
        <v-col xl="8" lg="8" md="8" sm="12" cols="12">
          <vehicles-chart
            :yearly-totals="dashboardData.yearlyTotals"
            v-if="dashboardData && dashboardData.yearlyTotals"
          />
        </v-col>

        <v-col xl="4" lg="4" md="4" sm="12" cols="12">
          <utilities-chart
            :yearly-utilities="dashboardData.yearlyUtilities"
            v-if="dashboardData && dashboardData.yearlyUtilities"
          />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Navbar from "../navs/Navbar.vue";
import VehiclesChart from "./partial/charts/VehiclesChart.vue";
import UtilitiesChart from "./partial/charts/UtilitiesChart.vue";
import Stats from "./partial/Stats.vue";
import ProfitCards from "./partial/ProfitCards.vue";

export default {
  components: { Navbar, Stats, VehiclesChart, UtilitiesChart, ProfitCards },

  methods: {
    ...mapActions({ getDashboardData: "dashboard/getDashboardData" }),
  },

  computed: {
    ...mapGetters({ dashboardData: "dashboard/dashboardData" }),
  },

  mounted() {
    this.getDashboardData();
  },
};
</script>