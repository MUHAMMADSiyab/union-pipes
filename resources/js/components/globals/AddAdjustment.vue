<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Add Adjustment</v-card-title>
    <v-card-subtitle>Add a new adjustment</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="add">
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
          v-text="validation.getMessage('type')"
        ></small>
        <v-select
          :items="adjustmentTypes"
          label="Adjustment Type"
          id="type"
          name="type"
          v-model="data.type"
          dense
          outlined
        ></v-select>

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

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('description')"
        ></small>
        <v-textarea
          name="descriptio "
          label="Description"
          id="descriptio "
          rows="2"
          v-model="data.description"
          dense
          outlined
        ></v-textarea>

        <v-btn color="success" type="submit">Add</v-btn>
        <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import store from "../../store/index";

export default {
  props: ["id", "adjustmentType", "paymentSetting"],

  mixins: [ValidationMixin],

  data() {
    return {
      adjustmentTypes: ["Depositing", "Withdrawing"],
      paymentMethods: this.paymentSetting.payment_methods,
      chequeTypes: this.paymentSetting.cheque_types,
      formLoading: false,
      menu: false,
      data: {
        ...(this.adjustmentType === "investment"
          ? { investor_id: this.id }
          : { account_id: this.id }),
        amount: "",
        type: "",
        date: "",
        payment_method: "",
        cheque_type: "",
        cheque_no: "",
        cheque_due_date: "",
        cheque_images: [],
        description: "",
      },
    };
  },

  methods: {
    handleFiles(files) {
      this.data.cheque_images = files;
    },

    async add() {
      this.formLoading = true;

      if (this.adjustmentType === "investment") {
        // await this.addInvestmentAdjustment(this.data);
        await store.dispatch("investor/addInvestmentAdjustment", this.data);
      } else {
        await store.dispatch("account/addAccountAdjustment", this.data);
      }

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.amount = 0;
        this.data.type = "";
        this.data.date = "";
        this.data.payment_method = "";
        this.data.cheque_type = "";
        this.data.cheque_no = "";
        this.data.cheque_due_date = "";
        this.data.cheque_images = "";
        this.data.description = "";
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
    }),
  },
};
</script>