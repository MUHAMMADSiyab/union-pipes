<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>Edit App Setting</v-card-title>
            <v-card-subtitle>Edit the Application Setting</v-card-subtitle>

            <v-card-text class="mt-1">
              <v-form @submit.prevent="update">
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
                  label="New Password"
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
                  :append-icon="
                    !showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'
                  "
                  @click:append="showPasswordConfirm = !showPasswordConfirm"
                  dense
                  outlined
                ></v-text-field>

                <v-btn color="success" type="submit">Save</v-btn>
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
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";
import Navbar from "../../navs/Navbar.vue";

export default {
  components: {
    Navbar,
  },

  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      showPassword: false,
      showPasswordConfirm: false,
      data: {
        id: "",
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
    };
  },

  methods: {
    ...mapActions({
      editUserAccount: "user/editUserAccount",
    }),

    async update() {
      this.formLoading = true;

      await this.editUserAccount(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});

        this.data.password = "";
        this.data.password_confirmation = "";
      }
    },
  },

  computed: {
    ...mapGetters({
      authUser: "auth/user",
      validationErrors: "validationErrors",
    }),
  },

  mounted() {
    if (this.authUser) {
      this.data.name = this.authUser.name;
      this.data.email = this.authUser.email;
    }
  },
};
</script>