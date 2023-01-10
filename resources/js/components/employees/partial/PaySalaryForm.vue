<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Pay Salary</v-card-title>
    <v-card-subtitle>Pay the employee's salary</v-card-subtitle>

    <v-card-text class="mt-2">
      <v-form @submit.prevent="add" autocomplete="off">
        <v-row>
          <v-col cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('month')"
            ></small>
            <v-menu max-width="290px" min-width="auto">
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="data.month"
                  v-on="on"
                  label="Salary Month"
                  prepend-inner-icon="mdi-calendar"
                  dense
                  outlined
                ></v-text-field>
              </template>
              <v-date-picker
                type="month"
                v-model="data.month"
                label="Salary Month"
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
              v-text="validation.getMessage('additional_amount')"
            ></small>
            <v-text-field
              type="number"
              name="additional_amount"
              label="Additional Amount"
              id="additional_amount"
              v-model="data.additional_amount"
              dense
              outlined
            ></v-text-field>
          </v-col>

          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('deducted_amount')"
            ></small>
            <v-text-field
              type="number"
              name="deducted_amount"
              label="Deducted Amount"
              id="deducted_amount"
              v-model="data.deducted_amount"
              dense
              outlined
            ></v-text-field>
          </v-col>
        </v-row>

        <!-- Payment -->
        <v-row v-if="paymentSetting" class="mb-2">
          <v-col cols="12" class="py-0 mb-1">
            <h6 class="text-subtitle-2 primary--text">Payment Details</h6>
          </v-col>

          <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('amount')"
            ></small>
            <v-text-field
              name="amount"
              :label="`Amount Paid (${paymentSetting.currency})`"
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
          </template>
        </v-row>

        <v-btn color="primary" type="submit">Pay</v-btn>
        <v-btn color="secondary" type="button" @click="closeDialog"
          >Cancel</v-btn
        >
      </v-form>

      <alert />
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  props: ["currentEmployee"],

  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      data: {
        month: "",
        date: "",
        total_paid: 0,
        additional_amount: 0,
        deducted_amount: 0,
        employee_id: this.currentEmployee.id,
        payment: {
          model: "App\\Models\\Salary",
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
      getBanks: "bank/getBanks",
      getPaymentSetting: "getPaymentSetting",
      addSalary: "salary/addSalary",
      addNewPayment: "payment/addNewPayment",
    }),

    handleFiles(files) {
      this.data.payment.cheque_images = files;
    },

    async add() {
      this.formLoading = true;

      this.data.total_paid = this.data.payment.amount || 0;
      this.data.date = this.data.payment.payment_date;

      await this.addSalary(this.data);

      if (this.recent_salary) {
        this.data.payment.paymentable_id = this.recent_salary.id;
        this.data.payment.employeeId = this.data.employee_id;

        await this.addNewPayment(this.data.payment);
      }

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        if (this.alertData && this.alertData.type !== "error") {
          this.data.month = "";
          this.data.total_paid = "";
          this.data.additional_amount = "";
          this.data.deducted_amount = "";
          this.data.payment.amount = "";
          this.data.payment.payment_date = "";
          this.data.payment.bank_id = "";
          this.data.payment.payment_method = "";
          this.data.payment.cheque_type = "";
          this.data.payment.cheque_no = "";
          this.data.payment.cheque_due_date = "";
          this.data.payment.cheque_images = [];
          this.data.payment.description = "";
        }

        // Clear the validation messages object
        this.validation.setMessages({});

        this.closeDialog();
      }
    },

    closeDialog() {
      this.$emit("closeDialog");
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      banks: "bank/banks",
      paymentSetting: "paymentSetting",
      recent_salary: "salary/recent_salary",
      alertData: "alert/data",
    }),
  },

  mounted() {
    Promise.all([this.getPaymentSetting(), this.getBanks()]);
  },
};
</script>