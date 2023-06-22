<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>New User</v-card-title>
    <v-card-subtitle>Add a New User</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="add">
        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('name')"
        ></small>
        <v-text-field
          name="user-name"
          label="User Name"
          id="user-name"
          v-model="data.name"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('email')"
        ></small>
        <v-text-field
          name="user-email"
          label="Email"
          id="user-email"
          type="email"
          v-model="data.email"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('password')"
        ></small>
        <v-text-field
          name="user-password"
          label="Password"
          id="user-password"
          hint="At least 6 characters long"
          :type="showPassword ? 'text' : 'password'"
          v-model="data.password"
          :append-icon="!showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append="showPassword = !showPassword"
          dense
          outlined
        ></v-text-field>

        <v-text-field
          name="user-password-confirmation"
          label="Password Confirm"
          id="user-password-confirmation"
          :type="showPasswordConfirm ? 'text' : 'password'"
          v-model="data.password_confirmation"
          :append-icon="!showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append="showPasswordConfirm = !showPasswordConfirm"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('roles')"
        ></small>
        <v-combobox
          :items="roles"
          v-model="data.userRoles"
          item-text="name"
          item-value="id"
          label="Select roles"
          multiple
          search
          clearable
          dense
          chips
          outlined
          clear-icon="mdi-close-circle-outline"
        >
          <template v-slot:selection="{ attrs, item, select, selected }">
            <v-chip
              v-bind="attrs"
              :input-value="selected"
              close
              @click="select"
              class="mt-2"
              color="primary"
              @click:close="remove(item)"
            >
              <strong>{{ item.name }}</strong>
            </v-chip>
          </template>
        </v-combobox>

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
      showPassword: false,
      showPasswordConfirm: false,
      data: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        userRoles: [],
      },
    };
  },

  methods: {
    ...mapActions({
      addUser: "user/addUser",
      getRoles: "role/getRoles",
    }),

    remove(item) {
      const index = this.data.userRoles.findIndex(
        (role) => role.id === item.id
      );
      this.data.userRoles.splice(index, 1);
      this.data.userRoles = [...this.data.userRoles];
    },

    async add() {
      this.formLoading = true;
      this.data.roles = this.data.userRoles.length
        ? this.data.userRoles.map((role) => role.id)
        : this.data.userRoles;

      await this.addUser(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
        this.data.email = "";
        this.data.password = "";
        this.data.password_confirmation = "";
        this.data.userRoles = [];
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      roles: "role/roles",
      validationErrors: "validationErrors",
    }),
  },

  mounted() {
    this.getRoles();
  },
};
</script>