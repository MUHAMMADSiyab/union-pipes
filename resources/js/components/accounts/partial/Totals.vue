<template>
  <v-card height="100%">
    <v-card-text>
      <h5 class="text-uppercase light--text">Totals</h5>

      <v-row class="mt-3">
        <v-col xl="4" lg="4" md="4" sm="12" cols="12">
          <h5 class="text-uppercase grey--text font-weight-normal darken-1">
            Total Amount
          </h5>
          <h1 class="green--text accent-4 mt-3">
            {{ money(totals.total_amount) }}
          </h1>
        </v-col>

        <v-col xl="4" lg="4" md="4" sm="12" cols="12">
          <h5 class="text-uppercase grey--text font-weight-normal darken-1">
            Total Paid
          </h5>
          <h1 class="green--text accent-4 mt-3">
            {{ money(totals.total_paid) }}
          </h1>
        </v-col>

        <v-col xl="4" lg="4" md="4" sm="12" cols="12">
          <h5 class="text-uppercase grey--text font-weight-normal darken-1">
            Balance
          </h5>
          <h1 class="green--text accent-4 mt-3">
            {{ money(totals.balance) }}
          </h1>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
  mixins: [CurrencyMixin],

  props: ["totals"],
};
</script>