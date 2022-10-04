<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4" v-if="purchase">
      <h5 class="text-subtitle-1 mb-2">Purchase Details</h5>

      <v-row>
        <v-col cols="12">
          <v-row>
            <!-- Purchase / Payment Details -->
            <v-col xl="7" lg="7" md="7" sm="12" cols="12">
              <PurchaseDetails :purchase="purchase" />

              <ChambersReadings :chamber_readings="purchase.chamber_readings" />

              <Distributions :distributions="purchase.distributions" />
            </v-col>

            <!-- Vehicle Details -->
            <v-col xl="5" lg="5" md="5" sm="12" cols="12">
              <VehicleDetails :vehicle="purchase.vehicle" />

              <PaymentDetails
                :payment="purchase.payment"
                :total_amount="purchase.total_amount"
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
import VehicleDetails from "./partial/VehicleDetails.vue";
import PurchaseDetails from "./partial/PurchaseDetails.vue";
import PaymentDetails from "./partial/PaymentDetails.vue";
import ChambersReadings from "./partial/ChambersReadings.vue";
import Distributions from "./partial/Distributions.vue";

export default {
  mixins: [CurrencyMixin],

  components: {
    Navbar,
    VehicleDetails,
    PurchaseDetails,
    PaymentDetails,
    ChambersReadings,
    Distributions,
  },

  methods: {
    ...mapActions({
      getPurchase: "purchase/getPurchase",
    }),
  },

  computed: {
    ...mapGetters({
      purchase: "purchase/purchase",
      loading: "loading",
    }),
  },

  async mounted() {
    this.getPurchase(parseInt(this.$route.params.id));
  },
};
</script>