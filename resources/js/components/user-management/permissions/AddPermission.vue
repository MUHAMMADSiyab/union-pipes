<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>New Permission</v-card-title>
    <v-card-subtitle>Add a New Permission</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="add">
        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('name')"
        ></small>
        <v-text-field
          name="permission-name"
          label="Permission Name"
          id="permission-name"
          v-model="data.name"
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
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      data: {
        name: "",
      },
    };
  },

  methods: {
    ...mapActions({ addPermission: "permission/addPermission" }),

    async add() {
      this.formLoading = true;

      await this.addPermission(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
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