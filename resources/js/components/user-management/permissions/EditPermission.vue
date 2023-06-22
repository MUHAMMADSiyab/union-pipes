<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Edit Permission</v-card-title>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="update">
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

        <v-btn color="success" type="submit">Update</v-btn>
        <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  props: ["singlePermission"],

  mixins: [ValidationMixin],

  data() {
    const { id, name } = this.singlePermission;

    return {
      formLoading: false,
      data: {
        id,
        name,
      },
    };
  },

  methods: {
    ...mapActions({ updatePermission: "permission/updatePermission" }),

    async update() {
      this.formLoading = true;

      await this.updatePermission(this.data);

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
    singlePermission: {
      handler({ id, name }) {
        this.data.id = id;
        this.data.name = name;
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