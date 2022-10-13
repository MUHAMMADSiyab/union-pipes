<template>
  <!-- Cheque Images -->
  <v-card>
    <v-card-title primary-title>Enter Final Readings</v-card-title>

    <v-card-text>
      <form @submit.prevent="update">
        <div class="mt-1 mb-1">
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
                  v-for="(reading, index) in data.final_readings"
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
                    <ul style="list-style: none; padding-inline-start: 0">
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
                              `final_readings.${index}.meters.${i}.value`
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
        </div>

        <v-card-actions>
          <v-btn
            color="success"
            type="submit"
            class="mt-3"
            :disabled="formLoading"
            >Update</v-btn
          >
          <v-btn color="secondary" @click="closeDialog" class="mt-3"
            >Close</v-btn
          >
        </v-card-actions>
      </form>
    </v-card-text>
  </v-card>
</template>
  
  <script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  props: ["sellId"],

  mixins: [ValidationMixin, CurrencyMixin],

  data() {
    return {
      totals: {
        totalPetrolReading: 0,
        totalDieselReading: 0,
        totalPetrolPrice: 0,
        totalDieselPrice: 0,
      },
      formLoading: false,
      data: {
        sell_id: this.sellId,
        final_readings: [],
      },
    };
  },

  methods: {
    ...mapActions({
      getSellFinalReadings: "sell/getSellFinalReadings",
      getMeters: "meter/getMeters",
      getCurrentRates: "rate/getCurrentRates",
      updateFinalReadings: "sell/updateFinalReadings",
    }),

    closeDialog() {
      this.$emit("closeDialog");
    },

    getNormalizedReadings(readings) {
      return readings.map((meter_readings) => ({
        product: meter_readings[0].meter.dispenser.tank.product,
        tank: meter_readings[0].meter.dispenser.tank,
        dispenser: meter_readings[0].meter.dispenser,
        meters: meter_readings.map((meter_reading) => ({
          id: meter_reading.meter.id,
          name: meter_reading.meter.name,
          value: meter_reading.value,
          existing_value: meter_reading.value,
        })),
      }));
    },

    async update() {
      this.formLoading = true;

      await this.updateFinalReadings(this.data);

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
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      current_rates: "rate/current_rates",
      final_readings: "sell/final_readings",
      meters: "meter/meters",
    }),
  },

  watch: {
    sellId: {
      handler(sellId) {
        if (sellId) {
          this.getSellFinalReadings(sellId).then(() => {
            this.data.final_readings = this.getNormalizedReadings(
              this.final_readings
            );
          });
        }
      },
    },

    "data.final_readings": {
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

  async mounted() {
    await Promise.all([this.getCurrentRates(), this.getMeters()]);

    if (this.sellId) {
      await this.getSellFinalReadings(this.sellId);

      this.data.final_readings = this.getNormalizedReadings(
        this.final_readings
      );
    }
  },
};
</script>