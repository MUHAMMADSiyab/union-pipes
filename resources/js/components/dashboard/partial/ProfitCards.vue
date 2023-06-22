<template>
  <v-row>
    <v-col xl="6" lg="6" md="6" sm="12" cols="12">
      <v-card>
        <v-card-text>
          <v-row align="center">
            <v-col cols="6">
              <h4 class="text-h4">
                {{ money(profits.expectedProfit) }}
              </h4>
              <span class="text-subtitle d-block mt-2">Expected Profit</span>
            </v-col>
            <v-col cols="6">
              <v-progress-circular
                class="float-right"
                :rotate="360"
                :size="100"
                :width="15"
                :value="expectedProfitPercentage"
                color="indigo"
              >
                {{ expectedProfitPercentage }}%
              </v-progress-circular>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col xl="6" lg="6" md="6" sm="12" cols="12">
      <v-card>
        <v-card-text>
          <v-row align="center">
            <v-col cols="6">
              <h4 class="text-h4">
                {{ money(profits.realProfit) }}
              </h4>
              <span class="text-subtitle d-block mt-2">Real Profit</span>
            </v-col>
            <v-col cols="6">
              <v-progress-circular
                :title="`${roundedRealProfitPercentage}% of expected`"
                class="float-right"
                :rotate="360"
                :size="100"
                :width="15"
                :value="realProfitPercentage"
                color="pink"
              >
                {{ roundedRealProfitPercentage }}%
              </v-progress-circular>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>
<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";

export default {
  props: ["profits"],

  mixins: [CurrencyMixin],

  data() {
    return {
      expectedProfitPercentage: 0,
      realProfitPercentage: 0,
    };
  },

  computed: {
    roundedRealProfitPercentage: function () {
      return Math.round(
        this.realProfitPercentage ? this.realProfitPercentage : 0
      );
    },
  },

  beforeDestroy() {
    clearInterval(this.interval);
  },

  mounted() {
    const { expectedProfit, realProfit } = this.profits;

    if (expectedProfit > 0) {
      this.interval = setInterval(() => {
        const realProfitPercentageToExpected =
          (realProfit / expectedProfit) * 100;

        this.expectedProfitPercentage += 100;
        this.realProfitPercentage += realProfitPercentageToExpected;

        if (
          this.expectedProfitPercentage === 100 &&
          this.realProfitPercentage === realProfitPercentageToExpected
        ) {
          clearInterval(this.interval);
        }
      }, 700);
    }
  },
};
</script>