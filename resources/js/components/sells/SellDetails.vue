<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4" v-if="sell">
      <h5 class="text-subtitle-1 mb-2">Sell Details</h5>

      <v-row>
        <v-col cols="12">
          <v-row>
            <!-- Sell Details -->
            <v-col cols="12">
              <SellInfo :sell="sell" />
            </v-col>

            <!-- Readings -->
            <v-col xl="6" lg="6" md="6" sm="12" cols="12">
              <SellReadings type="Initial" :readings="sell.initial_readings" />
            </v-col>

            <v-col xl="6" lg="6" md="6" sm="12" cols="12">
              <SellReadings type="Final" :readings="sell.final_readings" />
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
    
    <script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";
import SellInfo from "./partial/SellInfo.vue";
import SellReadings from "./partial/SellReadings.vue";

export default {
  mixins: [CurrencyMixin],

  components: {
    Navbar,
    SellInfo,
    SellReadings,
  },

  methods: {
    ...mapActions({
      getSell: "sell/getSell",
    }),
  },

  computed: {
    ...mapGetters({
      sell: "sell/sell",
      loading: "loading",
    }),
  },

  async mounted() {
    this.getSell(parseInt(this.$route.params.id));
  },
};
</script>