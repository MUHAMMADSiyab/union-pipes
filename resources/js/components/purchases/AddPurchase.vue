<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Purchase</v-card-title>
            <v-card-subtitle>Add a New Purchase</v-card-subtitle>

            <v-card-text class="mt-3">
              <v-form @submit.prevent="add">
                <v-row>
                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.date"
                          v-on="on"
                          label="Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.date"
                        label="Date"
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
                      v-text="validation.getMessage('company_id')"
                    ></small>
                    <v-select
                      :items="companies"
                      item-text="name"
                      item-value="id"
                      v-model="data.company_id"
                      placeholder="Select Company"
                      autocomplete
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('invoice_no')"
                    ></small>
                    <v-text-field
                      v-model="data.invoice_no"
                      label="Invoice No."
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('vehicle_id')"
                    ></small>
                    <v-select
                      :items="vehicles"
                      item-text="registration_no"
                      item-value="id"
                      v-model="data.vehicle_id"
                      @change="handleVehicleChange"
                      placeholder="Select Vehicle"
                      autocomplete
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('petrol_quantity')"
                    ></small>
                    <v-text-field
                      type="number"
                      v-model="data.petrol_quantity"
                      label="Petrol Quantity"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('diesel_quantity')"
                    ></small>
                    <v-text-field
                      type="number"
                      v-model="data.diesel_quantity"
                      label="Diesel Quantity"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('petrol_price')"
                    ></small>
                    <v-text-field
                      type="number"
                      v-model="data.petrol_price"
                      label="Petrol Price"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('diesel_price')"
                    ></small>
                    <v-text-field
                      type="number"
                      v-model="data.diesel_price"
                      label="Diesel Price"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('total_amount')"
                    ></small>
                    <v-text-field
                      type="number"
                      v-model="data.total_amount"
                      label="Total Amount"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>
                </v-row>

                <!-- Chamber Readings -->
                <v-row class="mt-3" v-if="data.chamber_readings.length">
                  <v-col cols="12" class="py-0">
                    <h3 class="ml-3 mb-4">Chambers Readings</h3>

                    <!-- Chambers -->
                    <v-list
                      v-for="(chamber, i) in data.chamber_readings"
                      :key="i"
                      no-action
                    >
                      <v-list-item>
                        <v-row>
                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              :label="`Chamber # ${i + 1}`"
                              dense
                              filled
                              disabled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              v-model="chamber.capacity"
                              type="number"
                              label="Capacity"
                              readonly
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              v-model="chamber.dip_value"
                              type="number"
                              label="Dip Value"
                              readonly
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <small
                              class="red--text"
                              v-if="validation.hasErrors()"
                              v-text="
                                validation.getMessage(
                                  `chamber_readings.${i}.rod_value`
                                )
                              "
                            ></small>
                            <v-text-field
                              v-model="chamber.rod_value"
                              type="number"
                              label="Rod Value"
                              height="20"
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <small
                              class="red--text"
                              v-if="validation.hasErrors()"
                              v-text="
                                validation.getMessage(
                                  `chamber_readings.${i}.available_quantity`
                                )
                              "
                            ></small>
                            <v-text-field
                              v-model="chamber.available_quantity"
                              type="number"
                              label="Available Quantity"
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <small
                              class="red--text"
                              v-if="validation.hasErrors()"
                              v-text="
                                validation.getMessage(
                                  `chamber_readings.${i}.excess_quantity`
                                )
                              "
                            ></small>
                            <v-text-field
                              v-model="chamber.excess_quantity"
                              type="number"
                              label="Excess Quantity"
                              dense
                              filled
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-list-item>
                      <v-divider class="mb-3"></v-divider>
                    </v-list>
                  </v-col>
                </v-row>

                <!-- Fuel Distribution -->
                <v-row
                  class="mt-3"
                  v-if="
                    data.chamber_readings.length && data.distributions.length
                  "
                >
                  <v-col cols="12" class="py-0">
                    <h3 class="ml-3 mb-4">Fuel Distribution</h3>

                    <!-- Tanks -->
                    <v-list
                      v-for="(tank, i) in data.distributions"
                      :key="i"
                      no-action
                    >
                      <v-list-item>
                        <v-row>
                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              :label="`${tank.name}`"
                              dense
                              filled
                              disabled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              v-model="tank.limit"
                              type="number"
                              label="Limit/Capacity"
                              readonly
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              v-model="tank.current_fuel_quantity"
                              type="number"
                              label="Curent Fuel Qty."
                              readonly
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              :value="tank.limit - tank.current_fuel_quantity"
                              type="number"
                              readonly
                              label="Free Space"
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <small
                              class="red--text"
                              v-if="validation.hasErrors()"
                              v-text="
                                validation.getMessage(
                                  `distributions.${i}.new_stock_quantity`
                                )
                              "
                            ></small>
                            <v-text-field
                              v-model="tank.new_stock_quantity"
                              type="number"
                              label="New Stock Qty."
                              dense
                              filled
                            ></v-text-field>
                          </v-col>

                          <v-col
                            xl="2"
                            lg="2"
                            md="2"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              :value="tank.product.name"
                              label="Tank Type"
                              disabled
                              dense
                              filled
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-list-item>
                      <v-divider class="mb-3"></v-divider>
                    </v-list>
                  </v-col>
                </v-row>

                <!-- Payment -->
                <v-row v-if="paymentSetting">
                  <v-col cols="12" class="py-0 mb-3">
                    <h6 class="text-subtitle-2 primary--text">
                      Payment Details
                    </h6>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('amount')"
                    ></small>
                    <v-text-field
                      name="amount"
                      :label="`Amount (${paymentSetting.currency})`"
                      id="amount"
                      v-model="data.payment.amount"
                      max="purchase.amount"
                      type="number"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('payment_date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.payment.payment_date"
                          v-on="on"
                          label="Payment Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.payment.payment_date"
                        label="Payment Date"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('payment_method')"
                    ></small>
                    <v-select
                      :items="paymentSetting.payment_methods"
                      label="Payment Method"
                      id="payment-method"
                      name="payment-method"
                      v-model="data.payment.payment_method"
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('bank_id')"
                    ></small>
                    <v-select
                      :items="banks"
                      name="bank"
                      label="Select Bank"
                      item-text="name"
                      item-value="id"
                      id="bank"
                      v-model="data.payment.bank_id"
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <template
                    v-if="data.payment.payment_method === 'Cheque'"
                    class="mt-1 mb-1"
                  >
                    <v-col xl="6" lg="6" md="6" sm="12" xs="12" class="py-0">
                      <small
                        class="red--text"
                        v-if="validation.hasErrors()"
                        v-text="validation.getMessage('cheque_type')"
                      ></small>
                      <v-select
                        :items="paymentSetting.cheque_types"
                        label="Cheque Type"
                        id="cheque-type"
                        name="cheque-type"
                        v-model="data.payment.cheque_type"
                        dense
                        outlined
                      ></v-select>
                    </v-col>
                    <v-col xl="6" lg="6" md="6" sm="12" xs="12" class="py-0">
                      <small
                        class="red--text"
                        v-if="validation.hasErrors()"
                        v-text="validation.getMessage('cheque_no')"
                      ></small>
                      <v-text-field
                        name="cheque-no"
                        label="Cheque No."
                        id="cheque-no"
                        v-model="data.payment.cheque_no"
                        dense
                        outlined
                      ></v-text-field>
                    </v-col>
                    <v-col xl="6" lg="6" md="6" sm="12" xs="12" class="py-0">
                      <small
                        class="red--text"
                        v-if="validation.hasErrors()"
                        v-text="validation.getMessage('cheque_due_date')"
                      ></small>
                      <v-menu max-width="290px" min-width="auto">
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.payment.cheque_due_date"
                            v-on="on"
                            label="Cheque Due Date"
                            prepend-inner-icon="mdi-calendar"
                            dense
                            outlined
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.payment.cheque_due_date"
                          label="Date"
                          no-title
                          outlined
                          dense
                          show-current
                        ></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col xl="6" lg="6" md="6" sm="12" xs="12" class="py-0">
                      <small
                        class="red--text"
                        v-if="validation.hasErrors()"
                        v-text="
                          validation.getMessage('cheque_images', true, true)
                        "
                      ></small>
                      <v-file-input
                        name="cheque-images"
                        label="Cheque Image(s)"
                        id="cheque-images"
                        @change="handleFiles"
                        prepend-inner-icon="mdi-camera"
                        prepend-icon=""
                        multiple
                        dense
                        :clearable="false"
                        outlined
                        hint="Only image files | Max. size 2MB"
                      ></v-file-input>
                    </v-col>
                  </template>
                </v-row>

                <v-btn color="primary" type="submit">Add</v-btn>
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
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
  mixins: [ValidationMixin],

  components: {
    Navbar,
  },

  data() {
    return {
      formLoading: false,
      data: {
        date: "",
        company_id: "",
        invoice_no: "",
        vehicle_id: "",
        petrol_quantity: 0,
        diesel_quantity: 0,
        petrol_price: 0,
        diesel_price: 0,
        total_amount:
          this.petrol_price * this.petrol_quantity +
          this.diesel_price * this.diesel_quantity,
        chamber_readings: [],
        distributions: [],
        payment: {
          model: "App\\Models\\Purchase",
          paymentable_id: null,
          amount: "",
          transaction_type: "Credit",
          payment_date: "",
          bank_id: "",
          payment_method: "",
          cheque_type: "",
          cheque_no: "",
          cheque_due_date: "",
          cheque_images: [],
          description: "",
        },
      },
    };
  },

  methods: {
    ...mapActions({
      getCompanies: "company/getCompanies",
      getVehicles: "vehicle/getVehicles",
      getTanks: "tank/getTanks",
      getPaymentSetting: "getPaymentSetting",
      getVehicleChambers: "vehicle/getVehicleChambers",
      getBanks: "bank/getBanks",
      addNewPayment: "payment/addNewPayment",
      addPurchase: "purchase/addPurchase",
    }),

    handleFiles(files) {
      this.data.payment.cheque_images = files;
    },

    async handleVehicleChange(vehicleId) {
      await this.getVehicleChambers(vehicleId);

      this.data.chamber_readings = this.vehicle_chambers.map((chamber) => ({
        chamber_id: chamber.id,
        capacity: chamber.capacity,
        dip_value: chamber.dip_value,
        rod_value: 0,
        available_quantity: 0,
        excess_quantity: 0,
      }));
    },

    async add() {
      this.formLoading = true;

      await this.addPurchase(this.data);

      if (this.recent_purchase) {
        this.data.payment.paymentable_id = this.recent_purchase.id;

        await this.addNewPayment(this.data.payment);
      }

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.date = "";
        this.data.invoice_no = "";
        this.data.company_id = "";
        this.data.vehicle_id = "";
        this.data.petrol_price = "";
        this.data.petrol_quantity = "";
        this.data.diesel_price = "";
        this.data.diesel_quantity = "";
        this.data.total_amount = "";
        this.data.payment.amount = "";
        this.data.payment.payment_date = "";
        this.data.payment.bank_id = "";
        this.data.payment.payment_method = "";
        this.data.payment.cheque_type = "";
        this.data.payment.cheque_no = "";
        this.data.payment.cheque_due_date = "";
        this.data.payment.cheque_images = [];
        this.data.payment.description = "";

        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      companies: "company/companies",
      vehicles: "vehicle/vehicles",
      tanks: "tank/tanks",
      vehicle_chambers: "vehicle/vehicle_chambers",
      paymentSetting: "paymentSetting",
      recent_purchase: "purchase/recent_purchase",
      banks: "bank/banks",
      validationErrors: "validationErrors",
    }),
  },

  watch: {
    data: {
      handler(data) {
        this.data.total_amount =
          data.petrol_price * data.petrol_quantity +
          data.diesel_price * data.diesel_quantity;
      },
      deep: true,
    },
  },

  async mounted() {
    await Promise.all([
      this.getCompanies(),
      this.getVehicles(),
      this.getTanks(),
      this.getPaymentSetting(),
      this.getBanks(),
    ]);

    this.data.distributions = this.tanks.map((tank) => ({
      tank_id: tank.id,
      name: tank.name,
      limit: tank.limit,
      current_fuel_quantity: tank.current_fuel_quantity,
      new_stock_quantity: 0,
      product: tank.product,
    }));
  },
};
</script>