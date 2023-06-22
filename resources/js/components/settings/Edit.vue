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
                  v-text="validation.getMessage('app_name')"
                ></small>
                <v-text-field
                  v-model="data.app_name"
                  label="App Name"
                  dense
                  outlined
                ></v-text-field>

                <v-row>
                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('phone')"
                    ></small>
                    <v-text-field
                      v-model="data.phone"
                      label="Phone"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('fax')"
                    ></small>
                    <v-text-field
                      v-model="data.fax"
                      label="Fax"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('email')"
                    ></small>
                    <v-text-field
                      type="email"
                      v-model="data.email"
                      label="Email"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>
                </v-row>

                <small
                  class="red--text"
                  v-if="validation.hasErrors()"
                  v-text="validation.getMessage('address')"
                ></small>
                <v-textarea
                  v-model="data.address"
                  label="Address"
                  rows="3"
                  dense
                  outlined
                ></v-textarea>

                <small
                  class="red--text"
                  v-if="validation.hasErrors()"
                  v-text="validation.getMessage('app_logo')"
                ></small>
                <v-file-input
                  name="app-logo"
                  label="Company/Business Logo"
                  id="app-logo"
                  @change="handleFile"
                  prepend-inner-icon="mdi-camera-image"
                  prepend-icon=""
                  dense
                  :clearable="false"
                  outlined
                  hint="Only image files (transparent PNG) | Max. size 2MB"
                  persistent-hint
                ></v-file-input>

                <v-row>
                  <v-col xl="10" lg="10" md="10" sm="12" cols="12">
                    <v-btn color="success" type="submit" class="mt-3"
                      >Save</v-btn
                    >
                  </v-col>

                  <v-col xl="2" lg="2" md="2" sm="12" cols="12">
                    <v-img
                      :src="logoPreviewUrl"
                      alt="Logo Image Preview"
                      v-if="logoPreviewUrl"
                      class="float-right rounded-lg"
                      :max-width="200"
                    />
                  </v-col>
                </v-row>
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
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar.vue";

export default {
  components: {
    Navbar,
  },

  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      data: {
        id: "",
        app_name: "",
        phone: "",
        email: "",
        fax: "",
        address: "",
        app_logo: "",
      },
      logoPreviewUrl: null,
    };
  },

  methods: {
    ...mapActions({
      getAppSetting: "setting/getAppSetting",
      updateAppSetting: "setting/updateAppSetting",
    }),

    handleFile(file) {
      this.data.app_logo = file;
      this.logoPreviewUrl = URL.createObjectURL(file);
    },

    async update() {
      this.formLoading = true;

      await this.updateAppSetting(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});

        // Clear logo image
        this.data.app_logo = null;
        this.logoPreviewUrl = null;
      }
    },
  },

  computed: {
    ...mapGetters({
      app_setting: "setting/app_setting",
      validationErrors: "validationErrors",
    }),
  },

  async mounted() {
    await this.getAppSetting();

    this.data.id = this.app_setting.id;
    this.data.app_name = this.app_setting.app_name;
    this.data.phone = this.app_setting.phone;
    this.data.fax = this.app_setting.fax;
    this.data.email = this.app_setting.email;
    this.data.address = this.app_setting.address;
  },
};
</script>