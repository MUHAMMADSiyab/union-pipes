<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Edit Bank</v-card-title>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="update">
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

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('balance')"
        ></small>
        <v-text-field
          name="balance"
          label="Balance"
          id="balance"
          type="number"
          v-model="data.balance"
          dense
          outlined
        ></v-text-field>

        <v-btn color="success" type="submit">Update</v-btn>
        <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
  props: ["singleBank"],

  mixins: [ValidationMixin],

  data() {
    const { id, name, account_no, branch_name, branch_code, balance } =
      this.singleBank;

    return {
      formLoading: false,
      data: {
        id,
        name,
        account_no,
        branch_name,
        branch_code,
        balance,
      },
    };
  },

  methods: {
    ...mapActions({ updateBank: "bank/updateBank" }),

    async update() {
      this.formLoading = true;

      await this.updateBank(this.data);

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

  watch: {
    singleBank: {
      handler({ id, name, account_no, branch_name, branch_code, balance }) {
        this.data.id = id;
        this.data.name = name;
        this.data.account_no = account_no;
        this.data.branch_name = branch_name;
        this.data.branch_code = branch_code;
        this.data.balance = balance;
      },
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
    }),
  },
};
</script>