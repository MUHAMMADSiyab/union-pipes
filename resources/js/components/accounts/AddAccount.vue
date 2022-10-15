<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Account</v-card-title>
            <v-card-subtitle>Add a New Account Entry</v-card-subtitle>

            <v-card-text class="mt-2">
              <v-form @submit.prevent="add">
                <v-row>
                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
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

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('vehicle_no')"
                    ></small>
                    <v-text-field
                      name="vehicle_no"
                      label="Vehicle No."
                      id="vehicle_no"
                      v-model="data.vehicle_no"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('product')"
                    ></small>
                    <v-select
                      :items="products"
                      name="product"
                      label="Select Product"
                      item-text="name"
                      item-value="name"
                      id="product"
                      v-model="data.product"
                      @change="handleProductChange"
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('product_price')"
                    ></small>
                    <v-text-field
                      name="product_price"
                      type="number"
                      :label="`${data.product} Rate`"
                      id="product_price"
                      v-model="data.product_price"
                      dense
                      readonly
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('product_quantity')"
                    ></small>
                    <v-text-field
                      name="product_quantity"
                      type="number"
                      label="Quantity"
                      id="product_quantity"
                      v-model="data.product_quantity"
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
                      name="total_amount"
                      type="number"
                      label="Total Amount"
                      id="total_amount"
                      v-model="data.total_amount"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
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
                </v-row>

                <v-switch
                  label="Receive Payment"
                  color="purple"
                  v-model="data.receive_payment"
                ></v-switch>

                <!-- Payment -->
                <v-row
                  class="mt-2"
                  v-if="paymentSetting && data.receive_payment"
                >
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
        receive_payment: false,
        customer_id: "",
        product_price: 0,
        vehicle_no: "",
        product: "",
        product_quantity: 0,
        total_amount: 0,
        date: "",

        payment: {
          model: "App\\Models\\Account",
          paymentable_id: null,
          amount: "",
          transaction_type: "Debit",
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
      getBanks: "bank/getBanks",
      getPaymentSetting: "getPaymentSetting",
      getProducts: "product/getProducts",
      getCustomers: "customer/getCustomers",
      getCurrentRates: "rate/getCurrentRates",
      addAccount: "account/addAccount",
      addNewPayment: "payment/addNewPayment",
    }),

    handleFiles(files) {
      this.data.payment.cheque_images = files;
    },

    handleProductChange(product) {
      this.data.product_price = this.current_rates.find(
        (rate) => rate.product.name === product
      )?.rate;
    },

    async add() {
      this.formLoading = true;

      if (this.data.date) {
        this.data.date = moment(this.data.date).format("YYYY-MM-DD HH:mm:ss");
      }

      await this.addAccount(this.data);

      if (this.recent_account && this.data.receive_payment) {
        this.data.payment.paymentable_id = this.recent_account.id;

        await this.addNewPayment(this.data.payment);
      }

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.product = "";
        this.data.product_quantity = 0;
        this.data.product_price = 0;
        this.data.total_amount = 0;
        this.data.date = "";

        this.data.payment.amount = "";
        this.data.payment.bank_id = "";
        this.data.payment.payment_date = "";
        this.data.payment.payment_method = "";
        this.data.payment.cheque_type = "";
        this.data.payment.cheque_no = "";
        this.data.payment.cheque_due_date = "";
        this.data.payment.cheque_images = [];

        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      banks: "bank/banks",
      paymentSetting: "paymentSetting",
      products: "product/products",
      customers: "customer/customers",
      current_rates: "rate/current_rates",
      recent_account: "account/recent_account",
    }),
  },

  watch: {
    data: {
      handler(data) {
        this.data.total_amount = data.product_price * data.product_quantity;
      },
      deep: true,
    },
  },

  mounted() {
    Promise.all([
      this.getPaymentSetting(),
      this.getCurrentRates(),
      this.getCustomers(),
      this.getProducts(),
      this.getBanks(),
    ]);
  },
};
</script>