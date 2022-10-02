<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>Edit Payment Setting</v-card-title>
            <v-card-subtitle
              >Edit the application's global payment setting</v-card-subtitle
            >

            <v-card-text class="mt-1">
              <v-form @submit.prevent="update">
                <small
                  class="red--text"
                  v-if="validation.hasErrors()"
                  v-text="validation.getMessage('payment_methods')"
                ></small>
                <v-combobox
                  :items="data.payment_methods"
                  v-model="data.payment_methods"
                  label="Payment Methods"
                  multiple
                  search
                  clearable
                  dense
                  chips
                  outlined
                  solo
                  clear-icon="mdi-close-circle-outline"
                >
                  <template
                    v-slot:selection="{ attrs, item, select, selected }"
                  >
                    <v-chip
                      v-bind="attrs"
                      :input-value="selected"
                      close
                      @click="select"
                      class="mt-2 mb-2"
                      color="primary"
                      @click:close="remove(item, 'payment_methods')"
                    >
                      <strong>{{ item }}</strong>
                    </v-chip>
                  </template>
                </v-combobox>

                <small
                  class="red--text"
                  v-if="validation.hasErrors()"
                  v-text="validation.getMessage('cheque_types')"
                ></small>
                <v-combobox
                  :items="data.cheque_types"
                  v-model="data.cheque_types"
                  label="Cheque Types"
                  multiple
                  search
                  clearable
                  dense
                  chips
                  outlined
                  solo
                  clear-icon="mdi-close-circle-outline"
                >
                  <template
                    v-slot:selection="{ attrs, item, select, selected }"
                  >
                    <v-chip
                      v-bind="attrs"
                      :input-value="selected"
                      close
                      @click="select"
                      class="mt-2 mb-2"
                      color="primary"
                      @click:close="remove(item, 'cheque_types')"
                    >
                      <strong>{{ item }}</strong>
                    </v-chip>
                  </template>
                </v-combobox>

                <small
                  class="red--text"
                  v-if="validation.hasErrors()"
                  v-text="validation.getMessage('currency')"
                ></small>
                <v-select
                  :items="currencies"
                  label="Currency"
                  id="code"
                  name="name"
                  item-value="code"
                  item-text="name"
                  v-model="data.currency"
                  search=""
                  dense
                  outlined
                ></v-select>

                <v-btn color="success" type="submit">Update</v-btn>
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
import { mapGetters } from "vuex";
import { SET_VALIDATION_ERRORS } from "../../mutation_constants";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
  mixins: [ValidationMixin],

  components: { Navbar },

  data() {
    return {
      formLoading: false,
      currencies: [],
      paymentSettingId: null,
      data: {
        payment_methods: [],
        cheque_types: [],
        currency: "",
      },
    };
  },

  methods: {
    remove(item, field) {
      const index = this.data[field].findIndex((pm) => pm === item);

      this.data[field].splice(index, 1);
      this.data[field] = [...this.data[field]];
    },

    async getPaymentSetting() {
      try {
        const res = await axios.get("/api/payment_settings");

        this.data.payment_methods = res.data.payment_methods;
        this.data.cheque_types = res.data.cheque_types;
        this.data.currency = res.data.currency;
        this.paymentSettingId = res.data.id;
      } catch (error) {
        console.log(error);
      }
    },

    async getCurrencies() {
      try {
        const res = await axios.get("/api/payment_settings/currencies");

        this.currencies = res.data;
      } catch (error) {
        console.log(error);
      }
    },

    async update() {
      this.formLoading = true;

      try {
        const res = await axios.put(
          `/api/payment_settings/${this.paymentSettingId}`,
          this.data
        );

        this.formLoading = false;

        this.$store.dispatch(
          "alert/setAlert",
          {
            type: "success",
            message: res.data.success,
          },
          { root: true }
        );
      } catch (error) {
        this.formLoading = false;

        if (error.response.status === 422) {
          this.$store.commit(SET_VALIDATION_ERRORS, error.response.data, {
            root: true,
          });
        }

        // Validation
        if (this.validationErrors !== null) {
          this.validation.setMessages(this.validationErrors.errors);
        } else {
          this.data.name = "";
          this.data.cnic = "";
          this.data.address = "";
          this.data.phone = "";
          this.data.photo = "";
          // Clear the validation messages object
          this.validation.setMessages({});
        }
        console.log(error);
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
    }),
  },

  mounted() {
    Promise.all([this.getCurrencies(), this.getPaymentSetting()]);
  },
};
</script>