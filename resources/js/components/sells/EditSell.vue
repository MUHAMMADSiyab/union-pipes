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
                            <th>Tank (Current Fuel Qty.)</th>
                            <th>Dispenser</th>
                            <th>Meter's Reading Value</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(reading, index) in data.initial_readings"
                            :key="index"
                          >
                            <td>{{ index + 1 }}</td>
                            <td>{{ reading.product.name }}</td>
                            <td>
                              {{ reading.tank.name }}
                              <v-chip color="indigo" class="white--text" pill>
                                {{ reading.tank.current_fuel_quantity }}
                              </v-chip>
                            </td>
                            <td>{{ reading.dispenser.name }}</td>
                            <td>
                              <ul
                                style="
                                  list-style: none;
                                  padding-inline-start: 0;
                                "
                              >
                                <li
                                  v-for="(meter, i) in reading.meters"
                                  :key="i"
                                  class="d-flex"
                                >
                                  <v-text-field
                                    class="d-block"
                                    v-model="meter.value"
                                    :class="{ 'mt-1': i === 0 }"
                                    type="number"
                                    :suffix="meter.name"
                                    prepend-inner-icon="mdi-speedometer"
                                    required
                                    dense
                                    filled
                                  ></v-text-field>

                                  <small
                                    class="red--text d-block ml-1"
                                    v-if="validation.hasErrors()"
                                    v-text="
                                      validation.getMessage(
                                        `initial_readings.${index}.meters.${i}.value`
                                      )
                                    "
                                  ></small>
                                </li>
                              </ul>
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
                          <th>Petrol Price</th>
                          <th>Diesel Price</th>
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
                            {{ money(totals.totalPetrolPrice) }}
                          </td>
                          <td>
                            {{ money(totals.totalDieselPrice) }}
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
      getDetailedMeters: "meter/getDetailedMeters",
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
          if (reading.product.name === "Petrol") {
            reading.meters.map((meter) => {
              totalPetrolReading += meter.value ? parseFloat(meter.value) : 0;
            });
          } else if (reading.product.name === "Diesel") {
            reading.meters.map((meter) => {
              totalDieselReading += meter.value ? parseFloat(meter.value) : 0;
            });
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
      detailed_meters: "meter/detailed_meters",
      sell: "sell/sell",
      current_rates: "rate/current_rates",
    }),
  },

  async mounted() {
    await Promise.all([
      this.getDetailedMeters(),
      this.getSell(this.$route.params.id),
      this.getCurrentRates(),
    ]);

    if (!this.sell) {
      return this.$router.push({ name: "not_found" });
    }

    this.data.id = this.sell.sell.id;
    this.data.sell_date = this.sell.sell.sell_date;

    if (this.sell.initial_readings.length) {
      this.data.initial_readings = this.sell.initial_readings.map(
        (meter_readings) => ({
          product: meter_readings[0].meter.dispenser.tank.product,
          tank: meter_readings[0].meter.dispenser.tank,
          dispenser: meter_readings[0].meter.dispenser,
          meters: meter_readings.map((meter_reading) => ({
            id: meter_reading.meter.id,
            name: meter_reading.meter.name,
            value: meter_reading.value,
            existing_value: meter_reading.value,
          })),
        })
      );
    }
  },
};
</script>