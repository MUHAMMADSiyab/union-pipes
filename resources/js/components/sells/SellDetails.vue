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
              <SellInfo :sell="sell.sell" />
            </v-col>

            <!-- Readings -->
            <v-col cols="12">
              <SellReadings
                type="Initial"
                :readings="initial_readings"
                v-if="initial_readings.length"
              />
            </v-col>

            <v-col cols="12">
              <SellReadings
                type="Final"
                :readings="final_readings"
                v-if="final_readings.length"
              />
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

  data() {
    return {
      initial_readings: [],
      final_readings: [],
    };
  },

  components: {
    Navbar,
    SellInfo,
    SellReadings,
  },

  methods: {
    ...mapActions({
      getSell: "sell/getSell",
    }),

    getNormalizedReadings(readings) {
      return readings.map((meter_readings) => ({
        product: meter_readings[0].meter.dispenser.tank.product,
        tank: meter_readings[0].meter.dispenser.tank,
        dispenser: meter_readings[0].meter.dispenser,
        meters: meter_readings.map((meter_reading) => ({
          id: meter_reading.meter.id,
          name: meter_reading.meter.name,
          value: meter_reading.value,
          existing_value: meter_reading.value,
        })),
      }));
    },
  },

  computed: {
    ...mapGetters({
      sell: "sell/sell",
      loading: "loading",
    }),
  },

  async mounted() {
    await this.getSell(parseInt(this.$route.params.id));

    this.initial_readings = this.getNormalizedReadings(
      this.sell.initial_readings
    );
    this.final_readings = this.getNormalizedReadings(this.sell.final_readings);
  },
};
</script>