<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4" v-if="vehicle_transactions.vehicle">
      <h5 class="text-subtitle-1 mb-2">
        Vehicle Transactions Entries

        <v-btn
          color="success"
          small
          link
          to="/vehicle_transactions/add"
          class="ma-2 text-right"
          v-if="can('vehicle_transaction_create')"
        >
          <v-icon left>mdi-vehicle_transaction-plus-outline</v-icon>
          New Entry</v-btn
        >
      </h5>

      <v-row>
        <v-col xl="6" lg="6" md="6" sm="12" cols="12"
          ><VehicleDetails :vehicle="vehicle_transactions.vehicle"
        /></v-col>

        <v-col xl="6" lg="6" md="6" sm="12" cols="12">
          <Totals :totals="totals" />
        </v-col>

        <v-col cols="12">
          <Entries :entries="vehicle_transactions.entries" />
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
import Entries from "./partial/Entries.vue";
import Totals from "./partial/Totals.vue";

export default {
  mixins: [CurrencyMixin],

  components: {
    Navbar,
    VehicleDetails,
    Entries,
    Totals,
  },

  data() {
    return {
      totals: {
        total_amount: 0,
        total_paid: 0,
        balance: 0,
      },
    };
  },

  methods: {
    ...mapActions({
      getVehicleTransactions: "vehicle_transaction/getVehicleTransactions",
    }),
  },

  computed: {
    ...mapGetters({
      vehicle_transactions: "vehicle_transaction/vehicle_transactions",
      loading: "loading",
    }),
  },

  async mounted() {
    await this.getVehicleTransactions(parseInt(this.$route.params.id));

    if (!this.vehicle_transactions.vehicle) {
      return this.$router.push({ name: "not_found" });
    }

    this.totals.total_vehicle_charges =
      this.vehicle_transactions.totals.total_vehicle_charges;
    this.totals.total_expense = this.vehicle_transactions.totals.total_expense;
    this.totals.balance = this.vehicle_transactions.totals.balance;
  },
};
</script>