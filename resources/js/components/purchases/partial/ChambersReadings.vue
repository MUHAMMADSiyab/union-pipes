<template>
  <v-card class="mt-2">
    <v-card-title primary-title>
      <h6 class="text-uppercase">Chambers Readings</h6>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <thead>
            <tr>
              <th>S#</th>
              <th>Capacity</th>
              <th>Dip Value</th>
              <th>Rod Value</th>
              <th>Available Quantity</th>
              <th>Excess Quantity</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(chamber_reading, i) in chamber_readings" :key="i">
              <td>{{ i + 1 }}</td>
              <td>{{ chamber_reading.chamber.capacity }}</td>
              <td>{{ chamber_reading.chamber.dip_value }}</td>
              <td>{{ chamber_reading.rod_value }}</td>
              <td>{{ chamber_reading.available_quantity }}</td>
              <td>{{ chamber_reading.excess_quantity }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";

export default {
  props: ["chamber_readings"],

  mixins: [CurrencyMixin],
};
</script>