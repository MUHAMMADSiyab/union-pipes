<template>
  <!-- Cheque Images -->
  <v-card>
    <v-card-title primary-title>Update Final Readings</v-card-title>

    <v-card-text>
      <form @submit.prevent="update">
        <div class="mt-1 mb-1">
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
                <tr v-for="(reading, i) in data.final_readings" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ reading.product }}</td>
                  <td>{{ reading.tank }}</td>
                  <td>{{ reading.dispenser }}</td>
                  <td width="250">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="
                        validation.getMessage(`final_readings.${i}.nozzle_id`)
                      "
                    ></small>
                    <v-select
                      class="mt-2"
                      :items="nozzles"
                      item-text="name"
                      item-value="id"
                      v-model="reading.nozzle_id"
                      placeholder="Select Nozzle"
                      readonly
                      autocomplete
                      filled
                    ></v-select>
                  </td>
                  <td width="160">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="
                        validation.getMessage(`final_readings.${i}.value`)
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
                  {{ totals.totalPetrolReading + totals.totalDieselReading }}
                </td>
                <td>
                  {{ money(totals.totalPetrolPrice) }}
                </td>
                <td>
                  {{ money(totals.totalDieselPrice) }}
                </td>
                <td>
                  {{ money(totals.totalPetrolPrice + totals.totalDieselPrice) }}
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
      getNozzles: "nozzle/getNozzles",
      getCurrentRates: "rate/getCurrentRates",
      updateFinalReadings: "sell/updateFinalReadings",
    }),

    closeDialog() {
      this.$emit("closeDialog");
    },

    getNormalizedReadings(readings) {
      return readings.map((reading) => ({
        product: reading.nozzle.dispenser.tank.product.name,
        tank: reading.nozzle.dispenser.tank.name,
        tank_id: reading.nozzle.dispenser.tank.id,
        dispenser: reading.nozzle.dispenser.name,
        nozzle_id: reading.nozzle_id,
        existing_value: reading.value,
        value: reading.value,
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
      nozzles: "nozzle/nozzles",
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

  async mounted() {
    await Promise.all([this.getCurrentRates(), this.getNozzles()]);

    if (this.sellId) {
      await this.getSellFinalReadings(this.sellId);
      this.data.final_readings = this.getNormalizedReadings(
        this.final_readings
      );
    }
  },
};
</script>