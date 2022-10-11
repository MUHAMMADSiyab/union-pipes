<template>
  <!-- Sell Info -->
  <v-card>
    <v-card-title primary-title>
      <h6 class="text-uppercase">{{ type }} Readings</h6>
    </v-card-title>
    <v-card-text>
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
            <tr v-for="(reading, i) in readings" :key="i">
              <td>{{ i + 1 }}</td>
              <td>{{ reading.nozzle.dispenser.tank.product.name }}</td>
              <td>{{ reading.nozzle.dispenser.tank.name }}</td>
              <td>{{ reading.nozzle.dispenser.name }}</td>
              <td>{{ reading.nozzle.name }}</td>
              <td>{{ reading.value }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>
  
  <script>
export default {
  props: ["type", "readings"],
};
</script>