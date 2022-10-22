<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row v-if="dashboardData">
        <v-col xl="6" lg="6" md="6" sm="12" cols="12">
          <RateCards />

          <RatesHistoryChart :rates_history="dashboardData.rates_history" />

          <UtilitiesChart
            :six-month-utilities="dashboardData.sixMonthUtilities"
          />
        </v-col>

        <v-col xl="6" lg="6" md="6" sm="12" cols="12">
          <SellTotalsCards :sell_totals="dashboardData.sell_totals" />

          <SellLastMonthChart
            :last_thirty_days_sell="
              dashboardData.sell_totals.last_thirty_days_sell
            "
          />

          <TanksStatsCard :tanks="dashboardData.tanks" />
        </v-col>
      </v-row>

      <!-- <ProfitCards
        :profits="dashboardData.profits"
        v-if="dashboardData && dashboardData.profits"
      /> -->

      <!-- <v-row>
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
      </v-row> -->
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Navbar from "../navs/Navbar.vue";
// import VehiclesChart from "./partial/charts/VehiclesChart.vue";
// import UtilitiesChart from "./partial/charts/UtilitiesChart.vue";
import RateCards from "./partial/RateCards.vue";
import RatesHistoryChart from "./partial/charts/RatesHistoryChart.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import SellTotalsCards from "./partial/SellTotalsCards.vue";
import SellLastMonthChart from "./partial/charts/SellLastMonthChart.vue";
import TanksStatsCard from "./partial/TanksStatsCard.vue";
import UtilitiesChart from "./partial/charts/UtilitiesChart.vue";
// import ProfitCards from "./partial/ProfitCards.vue";

export default {
  components: {
    Navbar,
    RateCards,
    RatesHistoryChart,
    SellTotalsCards,
    SellLastMonthChart,
    TanksStatsCard,
    UtilitiesChart,
  },

  mixins: [CurrencyMixin],

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