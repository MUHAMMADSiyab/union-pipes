<template>
  <v-row class="mb-3">
    <v-col cols="12">
      <v-card color="purple">
        <v-card-title>
          <h6 class="text-uppercase white--text">Overall Sell Last 30 Days</h6>
        </v-card-title>

        <v-card-text>
          <h4 class="text-h4 white--text">
            {{ money(sell_totals.last_thirty_days_all) }}

            <v-icon large right class="float-right" color="#5bff8c"
              >mdi-chart-bell-curve-cumulative</v-icon
            >
          </h4>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
      <v-card color="#02d68b">
        <v-card-title>
          <h6 class="text-uppercase white--text">Diesel</h6>
        </v-card-title>

        <v-card-text>
          <h5 class="text-h5 white--text font-weight-medium">
            {{ money(sell_totals.last_thirty_days_diesel) }}
          </h5>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
      <v-card color="#139af8">
        <v-card-title>
          <h6 class="text-uppercase white--text">Petrol</h6>
        </v-card-title>

        <v-card-text>
          <h5 class="text-h5 white--text font-weight-medium">
            {{ money(sell_totals.last_thirty_days_petrol) }}
          </h5>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
  props: ["sell_totals"],

  mixins: [CurrencyMixin],
};
</script>