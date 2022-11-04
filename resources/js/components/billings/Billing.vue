<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12" class="py-0">
          <v-card
            :loading="formLoading"
            :disabled="formLoading"
            class="d-print-none"
          >
            <v-card-title primary-title>Customer Billing</v-card-title>
            <v-card-subtitle
              >Generate customer's account bill between a date
              range</v-card-subtitle
            >

            <v-card-text class="mt-3">
              <v-form @submit.prevent="generate">
                <v-row>
                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('customer_id')"
                    ></small>
                    <v-select
                      :items="customers"
                      name="customer_id"
                      label="Select Customer"
                      item-text="name"
                      item-value="id"
                      id="customer"
                      v-model="data.customer_id"
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('from_date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.from_date"
                          v-on="on"
                          label="From Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.from_date"
                        label="From Date"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('to_date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.to_date"
                          v-on="on"
                          label="To Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.to_date"
                        label="To Date"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>

                <v-btn class="mt-4" color="primary" type="submit"
                  >Generate</v-btn
                >
              </v-form>
            </v-card-text>
          </v-card>

          <!-- Bill -->
          <Bill
            v-if="success && billing_data"
            :billing-data="billing_data"
            :form-data="{ from_date: data.from_date, to_date: data.to_date }"
          />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import DatatableMixin from "../../mixins/DatatableMixin";
import Navbar from "../navs/Navbar";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import Bill from "./Bill.vue";

export default {
  mixins: [ValidationMixin, DatatableMixin],

  components: {
    Navbar,
    Excel,
    PDF,
    CSV,
    Bill,
  },

  data() {
    return {
      formLoading: false,
      success: false,
      data: {
        data: {
          customer_id: "",
          from_date: "",
          to_date: "",
        },
      },
    };
  },

  methods: {
    ...mapActions({
      getCustomers: "customer/getCustomers",
      getBillingData: "customer/getBillingData",
    }),

    async generate() {
      this.formLoading = true;

      await this.getBillingData(this.data);

      this.formLoading = false;
      this.success = true;
      // Validation
      if (this.validationErrors !== null) {
        this.success = false;

        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      customers: "customer/customers",
      billing_data: "customer/billing_data",
      validationErrors: "validationErrors",
      loading: "loading",
    }),
  },

  mounted() {
    this.getCustomers();
  },
};
</script>