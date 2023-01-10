<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Edit Salary</v-card-title>
    <v-card-subtitle>Edit the employee's salary</v-card-subtitle>

    <v-card-text class="mt-2">
      <v-form @submit.prevent="update" autocomplete="off">
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

        <v-btn color="primary" type="submit">Update</v-btn>
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
  props: ["salary"],

  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      data: {
        id: this.salary.id,
        month: this.salary.month,
        additional_amount: this.salary.additional_amount,
        deducted_amount: this.salary.deducted_amount,
      },
    };
  },

  methods: {
    ...mapActions({
      getBanks: "bank/getBanks",
      updateSalary: "salary/updateSalary",
    }),

    async update() {
      this.formLoading = true;

      await this.updateSalary(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
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
      alertData: "alert/data",
    }),
  },

  watch: {
    salary: {
      handler({ id, month, additional_amount, deducted_amount }) {
        this.data.id = id;
        this.data.month = month;
        this.data.additional_amount = additional_amount;
        this.data.deducted_amount = deducted_amount;
      },
    },
  },
};
</script>