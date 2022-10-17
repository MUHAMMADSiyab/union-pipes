<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>Edit Invoice</v-card-title>
            <v-card-subtitle>Edit an Invoice</v-card-subtitle>

            <v-card-text class="mt-2">
              <v-form @submit.prevent="update">
                <v-row>
                  <v-col xl="3" lg="3" md="3" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('invoice_no')"
                    ></small>
                    <v-text-field
                      name="invoice_no"
                      label="Invoice No."
                      id="invoice_no"
                      v-model="data.invoice_no"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="3" lg="3" md="3" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('buyer')"
                    ></small>
                    <v-text-field
                      name="buyer"
                      label="Buyer"
                      id="buyer"
                      v-model="data.buyer"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('address')"
                    ></small>
                    <v-text-field
                      name="address"
                      label="Address"
                      id="address"
                      v-model="data.address"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('ntn_no')"
                    ></small>
                    <v-text-field
                      name="ntn_no"
                      label="NTN #"
                      id="ntn_no"
                      v-model="data.ntn_no"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('gst_no')"
                    ></small>
                    <v-text-field
                      name="gst_no"
                      label="GST #"
                      id="gst_no"
                      v-model="data.gst_no"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('sales_tax_rate')"
                    ></small>
                    <v-text-field
                      type="number"
                      name="sales_tax_rate"
                      label="Sales Tax Rate"
                      id="sales_tax_rate"
                      v-model="data.sales_tax_rate"
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
                      v-text="validation.getMessage('rate')"
                    ></small>
                    <v-text-field
                      name="rate"
                      type="number"
                      :label="`${data.product} Rate`"
                      id="rate"
                      v-model="data.rate"
                      dense
                      readonly
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('quantity')"
                    ></small>
                    <v-text-field
                      name="quantity"
                      type="number"
                      label="Quantity"
                      id="quantity"
                      v-model="data.quantity"
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
                </v-row>
                <v-btn color="primary" class="mt-3" type="submit">Update</v-btn>
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
        id: "",
        buyer: "",
        address: "",
        rate: 0,
        invoice_no: "",
        product: "",
        ntn_no: "",
        gst_no: "",
        quantity: 0,
        sales_tax_rate: 0,
        total_amount: 0,
        date: "",
      },
    };
  },

  methods: {
    ...mapActions({
      getPaymentSetting: "getPaymentSetting",
      getProducts: "product/getProducts",
      getCurrentRates: "rate/getCurrentRates",
      getInvoice: "invoice/getInvoice",
      updateInvoice: "invoice/updateInvoice",
    }),

    handleProductChange(product) {
      this.data.rate = this.current_rates.find(
        (rate) => rate.product.name === product
      )?.rate;
    },

    async update() {
      this.formLoading = true;

      await this.updateInvoice(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});
        this.$router.push({ name: "invoices" });
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      products: "product/products",
      current_rates: "rate/current_rates",
      invoice: "invoice/invoice",
    }),
  },

  watch: {
    data: {
      handler(data) {
        this.data.total_amount = data.rate * data.quantity;
      },
      deep: true,
    },
  },

  async mounted() {
    await Promise.all([
      this.getCurrentRates(),
      this.getProducts(),
      this.getInvoice(this.$route.params.id),
    ]);

    if (!this.invoice) {
      return this.$router.push({ name: "not_found" });
    }

    this.data.id = this.invoice.id;
    this.data.invoice_no = this.invoice.invoice_no;
    this.data.buyer = this.invoice.buyer;
    this.data.product = this.invoice.product;
    this.data.address = this.invoice.address;
    this.data.gst_no = this.invoice.gst_no;
    this.data.ntn_no = this.invoice.ntn_no;
    this.data.quantity = this.invoice.quantity;
    this.data.rate = this.invoice.rate;
    this.data.sales_tax_rate = this.invoice.sales_tax_rate;
    this.data.total_amount = this.invoice.total_amount;
    this.data.date = this.invoice.date;
  },
};
</script>