<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>New Bank</v-card-title>
    <v-card-subtitle>Add a New Bank</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="add">
        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('name')"
        ></small>
        <v-text-field
          name="bank-name"
          label="Bank Name"
          id="bank-name"
          v-model="data.name"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('account_no')"
        ></small>
        <v-text-field
          name="accoun-no"
          label="Account No."
          id="accoun-no"
          type="number"
          v-model="data.account_no"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('branch_name')"
        ></small>
        <v-text-field
          name="branch-name"
          label="Branch Name"
          id="branch-name"
          v-model="data.branch_name"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('branch_code')"
        ></small>
        <v-text-field
          name="branch-code"
          label="Branch Code"
          id="branch-code"
          type="number"
          v-model="data.branch_code"
          dense
          outlined
        ></v-text-field>

        <v-btn color="primary" type="submit">Add</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      data: {
        name: "",
        account_no: "",
        branch_name: "",
        branch_code: "",
      },
    };
  },

  methods: {
    ...mapActions({ addBank: "bank/addBank" }),

    async add() {
      this.formLoading = true;

      await this.addBank(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
        this.data.account_no = "";
        this.data.branch_name = "";
        this.data.branch_code = "";
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
    }),
  },
};
</script>