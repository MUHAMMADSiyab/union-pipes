<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Edit Payment</v-card-title>
    <v-card-subtitle>Edit a payment</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="update">
        <!-- Investor -->
        <template v-if="payment.model === 'App\\Models\\PurchasedVehicle'">
          <v-row
            v-for="(investment, i) in data.investments"
            :key="i"
            class="mb-2"
          >
            <v-col xl="8" lg="8" md="8" sm="12" cols="12" class="py-0">
              <v-select
                :items="[
                  {
                    investor_id: investment.investor_id,
                    investor: investment.investor,
                  },
                ]"
                v-model="investment.investor_id"
                item-text="investor"
                item-value="investor_id"
                label="Investor"
                dense
                outlined
                readonly
              ></v-select>
            </v-col>

            <v-col xl="4" lg="4" md="4" smal="12" cols="12" class="py-0">
              <v-text-field
                name="investor-amount"
                label="Amount"
                v-model="investment.amount"
                dense
                outlined
              ></v-text-field>
            </v-col>
          </v-row>
        </template>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('amount')"
        ></small>
        <v-text-field
          name="amount"
          :label="`Amount (${paymentSetting.currency})`"
          id="amount"
          v-model="data.amount"
          type="number"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('payment_date')"
        ></small>
        <v-datetime-picker
          label="Payment Date"
          v-model="data.payment_date"
          :textFieldProps="{
            dense: true,
            outlined: true,
          }"
          :timePickerProps="{
            'use-seconds': true,
          }"
          timeFormat="HH:mm:ss"
        >
          <v-icon slot="timeIcon">mdi-clock-time-three</v-icon>
          <v-icon slot="dateIcon">mdi-calendar</v-icon>
        </v-datetime-picker>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('payment_method')"
        ></small>
        <v-select
          :items="paymentMethods"
          label="Payment Method"
          id="payment-method"
          name="payment-method"
          v-model="data.payment_method"
          dense
          outlined
        ></v-select>

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
          v-model="data.bank_id"
          dense
          outlined
        ></v-select>

        <v-row v-if="data.payment_method === 'Cheque'" class="mt-1 mb-1">
          <v-col xl="6" lg="6" md="6" sm="12" xs="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('cheque_type')"
            ></small>
            <v-select
              :items="chequeTypes"
              label="Cheque Type"
              id="cheque-type"
              name="cheque-type"
              v-model="data.cheque_type"
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
              v-model="data.cheque_no"
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
                  v-model="data.cheque_due_date"
                  v-on="on"
                  label="Cheque Due Date"
                  prepend-inner-icon="mdi-calendar"
                  dense
                  outlined
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="data.cheque_due_date"
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
              v-text="validation.getMessage('cheque_images', true, true)"
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
        </v-row>

        <v-menu
          max-width="290px"
          min-width="auto"
          v-if="payment.model === 'App\\Models\\PurchasedVehicle'"
        >
          <template v-slot:activator="{ on }">
            <v-text-field
              v-model="data.installment_month"
              v-on="on"
              label="Installment Month (in case of installment payment)"
              prepend-inner-icon="mdi-calendar"
              dense
              outlined
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="data.installment_month"
            label="Installment Month"
            type="month"
            no-title
            outlined
            dense
            show-current
          ></v-date-picker>
        </v-menu>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('description')"
        ></small>
        <v-textarea
          name="description"
          label="Description"
          id="description"
          rows="2"
          v-model="data.description"
          dense
          outlined
        ></v-textarea>

        <v-btn color="success" type="submit">Update</v-btn>
        <v-btn color="secondary" @click="$emit('closeEditMode')">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  props: ["paymentSetting", "payment", "entry", "employeeId"],

  mixins: [ValidationMixin],

  data() {
    const investments = [];

    if (this.payment.model === "App\\Models\\PurchasedVehicle") {
      this.entry.investments.forEach((investment) => {
        investments.push({
          investor_id: investment.investor_id,
          investor: investment.investor,
          amount: 0,
        });
      });
    }

    const {
      id,
      amount,
      payment_date,
      bank_id,
      paymentable_id,
      model,
      payment_method,
      cheque_type,
      cheque_no,
      cheque_due_date,
      installment_month,
      description,
    } = this.payment;

    return {
      paymentMethods: this.paymentSetting.payment_methods,
      chequeTypes: this.paymentSetting.cheque_types,
      formLoading: false,
      menu: false,
      data: {
        id,
        amount,
        payment_date,
        bank_id,
        payment_method,
        cheque_type,
        cheque_no,
        cheque_due_date,
        cheque_images: [],
        installment_month,
        paymentable_id,
        model,
        description,
        investments,
      },
    };
  },

  methods: {
    ...mapActions({
      getBanks: "bank/getBanks",
      editPayment: "payment/editPayment",
      getInvestmentPayments: "payment/getInvestmentPayments",
    }),

    handleFiles(files) {
      this.data.cheque_images = files;
    },

    async update() {
      this.formLoading = true;

      if (this.payment.model === "App\\Models\\PurchasedVehicle") {
        this.data.old_investments = this.investment_payments;
      }

      // in case of salary payment
      if (this.employeeId) {
        this.data.employeeId = this.employeeId;
      }

      await this.editPayment(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});

        this.$emit("closeEditMode");
      }
    },

    closeDialog() {
      this.$emit("closeDialog");
    },
  },

  computed: {
    ...mapGetters({
      banks: "bank/banks",
      investment_payments: "payment/investment_payments",
      validationErrors: "validationErrors",
    }),
  },

  watch: {
    payment: {
      handler(newPayment) {
        this.data.id = newPayment.id;
        this.data.amount = newPayment.amount;
        this.data.payment_date = newPayment.payment_date;
        this.data.bank_id = newPayment.bank_id;
        this.data.paymentable_id = newPayment.paymentable_id;
        this.data.model = newPayment.model;
        this.data.payment_method = newPayment.payment_method;
        this.data.cheque_type = newPayment.cheque_type;
        this.data.cheque_no = newPayment.cheque_no;
        this.data.cheque_due_date = newPayment.cheque_due_date;
        this.data.description = newPayment.description;
      },
      deep: true,
    },

    "data.investments": {
      handler(investments) {
        let investmentAmounts = 0;

        investments.map((inv) => (investmentAmounts += parseInt(inv.amount)));

        this.data.amount = parseInt(investmentAmounts);
      },

      deep: true,
    },
  },

  async mounted() {
    await Promise.all([
      this.getInvestmentPayments(this.payment.id),
      this.getBanks(),
    ]);

    // Populate investor's amounts fields
    this.investment_payments.forEach((investment_payment) => {
      this.data.investments.forEach((investment) => {
        if (investment_payment.investor_id === investment.investor_id) {
          investment.amount = investment_payment.amount;
        }
      });
    });
  },
};
</script>