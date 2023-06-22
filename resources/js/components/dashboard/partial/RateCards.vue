<template>
  <v-row>
    <v-col cols="12">
      <v-card elevation="2">
        <v-card-title>
          <h6 class="text-uppercase grey--text">Current Rates</h6>
        </v-card-title>

        <v-card-text class="mt-1" v-if="current_rates.length">
          <v-row>
            <v-col v-for="(data, i) in current_rates" :key="i">
              <Card
                color="indigo"
                icon="mdi-currency-usd-circle-outline"
                :title="data.product.name"
                :number="data.rate"
              />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Card from "./Card.vue";

export default {
  props: ["totals"],

  components: { Card },

  methods: {
    ...mapActions({ getCurrentRates: "rate/getCurrentRates" }),
  },

  computed: {
    ...mapGetters({ current_rates: "rate/current_rates" }),
  },

  mounted() {
    this.getCurrentRates();
  },
};
</script>