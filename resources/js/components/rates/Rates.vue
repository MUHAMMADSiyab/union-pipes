<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <!-- Change Rates -->
        <v-col xl="5" lg="5" md="5" sm="12" cols="12" class="py-0">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>Change Rates</v-card-title>
            <v-card-subtitle>Update Fuel Rates</v-card-subtitle>

            <v-card-text class="mt-3">
              <v-form @submit.prevent="update">
                <v-row v-for="(rate, i) in data.rates" :key="i">
                  <v-col xl="8" lg="8" md="8" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('name')"
                    ></small>
                    <v-text-field
                      :value="rate.product.name"
                      dense
                      disabled
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage(`rates.${i}.rate`)"
                    ></small>
                    <v-text-field
                      v-model="rate.rate"
                      dense
                      label="Rate"
                    ></v-text-field>
                  </v-col>
                </v-row>

                <v-row class="mt-4">
                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('change_date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.change_date"
                          v-on="on"
                          label="Change Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.change_date"
                        label="Change Date"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>

                <v-btn class="mt-4" color="primary" type="submit">Update</v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col xl="7" lg="7" md="7" sm="12" cols="12" class="py-0">
          <v-data-table
            :headers="headers"
            :items="rates"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('product_delete') && !printMode"
            loading-text="Loading products..."
            :footer-props="footerProps"
            v-model="selectedItems"
          >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
              props.index + 1
            }}</template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
              <Excel
                module="rates"
                :ids="selectedItems.map((item) => item.id)"
              />
              <CSV module="rates" :ids="selectedItems.map((item) => item.id)" />
              <PDF module="rates" :ids="selectedItems.map((item) => item.id)" />

              <v-text-field
                v-model="search"
                placeholder="Search"
                class="mx-4"
                append-icon="mdi-magnify"
              ></v-text-field>
            </template>
          </v-data-table>
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import DatatableMixin from "../../mixins/DatatableMixin";
import Navbar from "../navs/Navbar";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";

export default {
  mixins: [ValidationMixin, DatatableMixin],

  components: {
    Navbar,
    Excel,
    PDF,
    CSV,
  },

  data() {
    return {
      formLoading: false,
      data: {
        rates: [],
        change_date: "",
      },

      headers: [
        { text: "S#", value: "sno" },
        { text: "Product", value: "product.name" },
        { text: "Rate", value: "rate" },
        { text: "Change Date", value: "change_date" },
      ],

      selectedItems: [],
    };
  },

  methods: {
    ...mapActions({
      getCurrentRates: "rate/getCurrentRates",
      getRates: "rate/getRates",
      updateRates: "rate/updateRates",
    }),

    async update() {
      this.formLoading = true;

      await this.updateRates({
        change_date: this.data.change_date,
        rates: this.data.rates,
      });

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.change_date = "";
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      current_rates: "rate/current_rates",
      rates: "rate/rates",
      validationErrors: "validationErrors",
      loading: "loading",
    }),
  },

  async mounted() {
    await Promise.all([this.getCurrentRates(), this.getRates()]);

    this.data.rates = this.current_rates;
  },
};
</script>