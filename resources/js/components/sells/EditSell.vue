<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>Edit Sell</v-card-title>
            <v-card-subtitle>Update a Sell</v-card-subtitle>

            <v-card-text class="mt-3">
              <v-form @submit.prevent="update">
                <v-row>
                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('sell_date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.sell_date"
                          v-on="on"
                          label="Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.sell_date"
                        label="Date"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>

                <!-- Sell Readings (Initial) -->
                <v-row class="mt-3 mb-2" v-if="data.initial_readings.length">
                  <v-col cols="12" class="py-0">
                    <h3 class="ml-3 mb-4">Initial Readings</h3>

                    <v-simple-table dense>
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th>S#</th>
                            <th>Product</th>
                            <th>Tank</th>
                            <th>Dispenser</th>
                            <th>Nozzle</th>
                            <th>Value (Ltrs.)</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(reading, i) in data.initial_readings"
                            :key="i"
                          >
                            <td>{{ i + 1 }}</td>
                            <td>{{ reading.product }}</td>
                            <td>{{ reading.tank }}</td>
                            <td>{{ reading.dispenser }}</td>
                            <td width="250">
                              <small
                                class="red--text"
                                v-if="validation.hasErrors()"
                                v-text="
                                  validation.getMessage(
                                    `initial_readings.${i}.nozzle_id`
                                  )
                                "
                              ></small>
                              <v-select
                                class="mt-2"
                                :items="detailed_nozzles"
                                item-text="name"
                                item-value="id"
                                v-model="reading.nozzle_id"
                                placeholder="Select Nozzle"
                                autocomplete
                                readonly
                                filled
                              ></v-select>
                            </td>
                            <td width="160">
                              <small
                                class="red--text"
                                v-if="validation.hasErrors()"
                                v-text="
                                  validation.getMessage(
                                    `initial_readings.${i}.value`
                                  )
                                "
                              ></small>
                              <v-text-field
                                v-model="reading.value"
                                class="mt-1"
                                type="number"
                                label="Value"
                                dense
                                filled
                              ></v-text-field>
                            </td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>

                    <!-- Totals -->
                    <v-simple-table dense>
                      <thead>
                        <tr>
                          <th>Petrol Reading</th>
                          <th>Diesel Reading</th>
                          <th>Total Reading</th>
                          <th>Petrol Price</th>
                          <th>Diesel Price</th>
                          <th>Total Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                            {{ totals.totalPetrolReading }}
                          </td>
                          <td>
                            {{ totals.totalDieselReading }}
                          </td>
                          <td>
                            {{
                              totals.totalPetrolReading +
                              totals.totalDieselReading
                            }}
                          </td>
                          <td>
                            {{ money(totals.totalPetrolPrice) }}
                          </td>
                          <td>
                            {{ money(totals.totalDieselPrice) }}
                          </td>
                          <td>
                            {{
                              money(
                                totals.totalPetrolPrice +
                                  totals.totalDieselPrice
                              )
                            }}
                          </td>
                        </tr>
                      </tbody>
                    </v-simple-table>
                  </v-col>
                </v-row>

                <v-btn color="primary" type="submit" class="mt-5">Update</v-btn>
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
import CurrencyMixin from "../../mixins/CurrencyMixin";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
  mixins: [ValidationMixin, CurrencyMixin],

  components: {
    Navbar,
  },

  data() {
    return {
      formLoading: false,
      totals: {
        totalPetrolReading: 0,
        totalDieselReading: 0,
        totalPetrolPrice: 0,
        totalDieselPrice: 0,
      },
      data: {
        id: "",
        sell_date: "",
        initial_readings: [],
      },
    };
  },

  methods: {
    ...mapActions({
      getDetailedNozzles: "nozzle/getDetailedNozzles",
      getCurrentRates: "rate/getCurrentRates",
      getSell: "sell/getSell",
      updateSell: "sell/updateSell",
    }),

    async update() {
      this.formLoading = true;

      await this.updateSell(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});

        return this.$router.push({ name: "sells" });
      }
    },
  },

  watch: {
    "data.initial_readings": {
      handler(readings) {
        let totalPetrolReading = 0;
        let totalDieselReading = 0;

        readings.map((reading) => {
          if (reading.product === "Petrol") {
            totalPetrolReading += parseFloat(reading.value);
          } else if (reading.product === "Diesel") {
            totalDieselReading += parseFloat(reading.value);
          }
        });

        const petrolRate = this.current_rates.find(
          (rate) => rate.product.name === "Petrol"
        ).rate;
        const dieselRate = this.current_rates.find(
          (rate) => rate.product.name === "Diesel"
        ).rate;

        this.totals.totalPetrolReading = totalPetrolReading;
        this.totals.totalDieselReading = totalDieselReading;
        this.totals.totalPetrolPrice = totalPetrolReading * petrolRate;
        this.totals.totalDieselPrice = totalDieselReading * dieselRate;
      },
      deep: true,
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      detailed_nozzles: "nozzle/detailed_nozzles",
      sell: "sell/sell",
      current_rates: "rate/current_rates",
    }),
  },

  async mounted() {
    await Promise.all([
      this.getDetailedNozzles(),
      this.getSell(this.$route.params.id),
      this.getCurrentRates(),
    ]);

    this.data.id = this.sell.id;
    this.data.sell_date = this.sell.sell_date;

    if (this.detailed_nozzles.length) {
      this.data.initial_readings = this.detailed_nozzles.map(
        (nozzle, index) => ({
          product: nozzle.dispenser.tank.product.name,
          tank: nozzle.dispenser.tank.name,
          dispenser: nozzle.dispenser.name,
          nozzle_id: this.sell.initial_readings[index].nozzle_id,
          value: this.sell.initial_readings[index].value,
        })
      );
    }
  },
};
</script>