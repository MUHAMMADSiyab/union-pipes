<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Vehicle Transaction</v-card-title>
            <v-card-subtitle
              >Add a New Vehicle Transaction Entry</v-card-subtitle
            >

            <v-card-text class="mt-2">
              <v-form @submit.prevent="add">
                <v-row>
                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('vehicle_id')"
                    ></small>
                    <v-select
                      :items="vehicles"
                      name="vehicle_id"
                      label="Select Vehicle"
                      item-text="registration_no"
                      item-value="id"
                      id="vehicle"
                      v-model="data.vehicle_id"
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('driver')"
                    ></small>
                    <v-text-field
                      name="driver"
                      label="Driver"
                      id="driver"
                      v-model="data.driver"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('vehicle_charges')"
                    ></small>
                    <v-text-field
                      name="vehicle_charges"
                      type="number"
                      label="Vehicle Charges"
                      id="vehicle_charges"
                      v-model="data.vehicle_charges"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('expense')"
                    ></small>
                    <v-text-field
                      name="expense"
                      type="number"
                      label="Expense"
                      id="expense"
                      v-model="data.expense"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('date')"
                    ></small>
                    <v-datetime-picker
                      v-model="data.date"
                      label="Date"
                      no-title
                      :textFieldProps="{ dense: true, outlined: true }"
                      timeFormat="HH:mm:ss"
                      show-current
                    >
                    </v-datetime-picker>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('purchase_id')"
                    ></small>
                    <v-select
                      :items="purchases"
                      name="purchase_id"
                      label="Select Purchase"
                      item-text="invoice_no"
                      item-value="id"
                      id="purchase"
                      v-model="data.purchase_id"
                      dense
                      outlined
                    ></v-select>
                  </v-col>
                </v-row>

                <v-btn color="primary" class="mt-3" type="submit">Add</v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import moment from "moment";
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
  mixins: [ValidationMixin],

  components: { Navbar },

  data() {
    return {
      formLoading: false,
      data: {
        vehicle_id: "",
        vehicle_charges: 0,
        expense: 0,
        driver: "",
        purchase_id: "",
        date: "",
      },
    };
  },

  methods: {
    ...mapActions({
      getVehicles: "vehicle/getVehicles",
      getPurchases: "purchase/getPurchases",
      addVehicleTransaction: "vehicle_transaction/addVehicleTransaction",
    }),

    async add() {
      this.formLoading = true;

      if (this.data.date) {
        this.data.date = moment(this.data.date).format("YYYY-MM-DD HH:mm:ss");
      }

      await this.addVehicleTransaction(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.vehicle_id = "";
        this.data.product_quantity = 0;
        this.data.expense = 0;
        this.data.driver = "";
        this.data.purchase_id = "";
        this.data.date = "";

        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      purchases: "purchase/purchases",
      vehicles: "vehicle/vehicles",
    }),
  },

  mounted() {
    Promise.all([this.getVehicles(), this.getPurchases()]);
  },
};
</script>