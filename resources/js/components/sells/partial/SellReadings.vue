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
              <th>Tank (Current Fuel Qty.)</th>
              <th>Dispenser</th>
              <th>Meter</th>
              <th>Reading Value</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(reading, i) in readings">
              <template v-for="(meter, index) in reading.meters">
                <tr :key="meter.id">
                  <td rowspan="2" v-if="index === 0">{{ i + 1 }}</td>
                  <td rowspan="2" v-if="index === 0">
                    {{ reading.product.name }}
                  </td>
                  <td rowspan="2" v-if="index === 0">
                    {{ reading.tank.name }}
                    <v-chip
                      color="indigo"
                      small
                      outlined
                      class="white--text"
                      pill
                    >
                      <strong>{{ reading.tank.current_fuel_quantity }}</strong>
                    </v-chip>
                  </td>
                  <td rowspan="2" v-if="index === 0">
                    {{ reading.dispenser.name }}
                  </td>
                  <td>{{ meter.name }}</td>
                  <td>{{ meter.value }}</td>
                </tr>
              </template>
            </template>
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