<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Customers</h5>

      <v-row>
        <v-col
          xl="3"
          lg="3"
          md="3"
          sm="12"
          cols="12"
          v-for="(customer, i) in customers"
          :key="i"
        >
          <v-card class="mx-auto" max-width="344" ripple>
            <v-img
              :src="customer.photo"
              v-if="customer.photo"
              height="220px"
            ></v-img>
            <v-img
              src="/storage/common/user-placeholder.jpg"
              height="220px"
              v-else
            ></v-img>

            <v-card-title>
              {{ customer.name }}
            </v-card-title>

            <v-card-subtitle v-if="customer.phone">
              <v-icon left>mdi-card-account-phone-outline</v-icon>
              {{ customer.phone }}
            </v-card-subtitle>

            <v-card-actions>
              <v-btn
                x-small
                text
                color="secondary"
                :to="`/customers/edit/${customer.id}`"
                title="Edit"
                v-if="can('customer_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setCustomerId(customer.id)"
                title="Delete"
                v-if="can('customer_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>

              <v-btn
                x-small
                text
                color="info darken-2"
                :to="`/accounts/customer/${customer.id}`"
                title="Accounts (کھاتے)"
                v-if="can('account_access')"
              >
                <v-icon small>mdi-account-cash-outline</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Confirmation -->
        <Confirmation
          ref="confirmationComponent"
          :id="customerId"
          @confirmDeletion="
            customerId
              ? handleCustomerDelete()
              : handleMultipleCustomersDelete()
          "
        />
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditCustomer from "./EditCustomer.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
  components: {
    EditCustomer,
    Navbar,
    Confirmation,
  },

  data() {
    return {
      allCustomers: [],
      customerId: null,
    };
  },

  methods: {
    ...mapActions({
      getCustomers: "customer/getCustomers",
      getCustomer: "customer/getCustomer",
      deleteCustomer: "customer/deleteCustomer",
      deleteMultipleCustomers: "customer/deleteMultipleCustomers",
    }),

    setCustomerId(id) {
      this.customerId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleCustomerDelete() {
      await this.deleteCustomer(this.customerId);
      this.customerId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleCustomersDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleCustomers(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      customers: "customer/customers",
      customer: "customer/customer",
      loading: "loading",
    }),
  },

  async mounted() {
    await this.getCustomers();

    this.allCustomers = this.customers;
  },
};
</script>