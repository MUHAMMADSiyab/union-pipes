<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4" v-if="accounts.customer">
      <h5 class="text-subtitle-1 mb-2">Account Entries</h5>

      <v-row>
        <v-col xl="6" lg="6" md="6" sm="12" cols="12"
          ><CustomerDetails :customer="accounts.customer"
        /></v-col>

        <v-col xl="6" lg="6" md="6" sm="12" cols="12">
          <Totals :totals="totals" />
        </v-col>

        <v-col cols="12">
          <Entries :entries="accounts.entries" />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
      
<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";
import CustomerDetails from "./partial/CustomerDetails.vue";
import Entries from "./partial/Entries.vue";
import Totals from "./partial/Totals.vue";

export default {
  mixins: [CurrencyMixin],

  components: {
    Navbar,
    CustomerDetails,
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
      getAccounts: "account/getAccounts",
    }),
  },

  computed: {
    ...mapGetters({
      accounts: "account/accounts",
      loading: "loading",
    }),
  },

  async mounted() {
    await this.getAccounts(parseInt(this.$route.params.id));

    if (!this.accounts.customer) {
      return this.$router.push({ name: "not_found" });
    }

    this.totals.total_amount = this.accounts.totals.total_amount;
    this.totals.total_paid = this.accounts.totals.total_paid;
    this.totals.balance = this.accounts.totals.balance;
  },
};
</script>