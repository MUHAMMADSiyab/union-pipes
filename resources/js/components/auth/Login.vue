<template>
  <v-container
    fluid
    fill-height
    style="
      background: url('storage/common/login-bg.jpg') no-repeat center/cover;
    "
  >
    <v-row justify="center">
      <v-col xl="4" lg="4" md="5" sm="8" xs="12">
        <v-card elevation="1" :loading="formLoading" class="pa-3 login-card">
          <v-img
            v-if="app_setting && app_setting.app_logo"
            :src="app_setting.app_logo"
            max-width="200"
            max-height="120"
            class="mx-auto"
          ></v-img>

          <h4 class="text-subtitle text-center mt-3 grey--text">User Login</h4>

          <v-card-text class="mt-2">
            <v-form @submit.prevent="handleSubmit">
              <small
                class="red--text"
                v-if="validation.hasErrors()"
                v-text="validation.getMessage('email')"
              ></small>
              <v-text-field
                name="email"
                label="Email"
                id="email"
                type="email"
                v-model="data.email"
                prepend-inner-icon="mdi-email"
                :required="true"
                aria-required="true"
              ></v-text-field>

              <small
                class="red--text"
                v-if="validation.hasErrors()"
                v-text="validation.getMessage('password')"
              ></small>
              <v-text-field
                name="password"
                label="Password"
                id="password"
                type="password"
                v-model="data.password"
                prepend-inner-icon="mdi-account-key"
                required="true"
                aria-required="true"
              ></v-text-field>

              <v-btn
                type="submit"
                color="primary"
                class="mt-2"
                :disabled="formLoading"
              >
                Login
              </v-btn>
            </v-form>

            <alert />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
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
        email: "",
        password: "",
      },
    };
  },

  methods: {
    ...mapActions({
      attemptLogin: "auth/attemptLogin",
      getAppSetting: "setting/getAppSetting",
    }),

    async handleSubmit() {
      this.formLoading = true;

      await this.attemptLogin(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});

        if (!this.authError) {
          this.data.email = "";
          this.data.password = "";
          // Redirect to dashboard on successful login
          this.$router.replace({ name: "dashboard" });
        }
      }
    },
  },

  computed: {
    ...mapGetters({
      user: "auth/user",
      authError: "auth/error",
      app_setting: "setting/app_setting",
      validationErrors: "validationErrors",
    }),
  },

  mounted() {
    this.getAppSetting();
  },
};
</script>

<style>
.login-card {
  /* background: rgba(255, 255, 255, 0.4) !important; */
}
</style>