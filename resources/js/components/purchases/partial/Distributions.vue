<template>
  <v-card class="mt-2">
    <v-card-title primary-title>
      <h6 class="text-uppercase">Fuel Distribution in Tanks</h6>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <thead>
            <tr>
              <th>S#</th>
              <th>Tank</th>
              <th>Product</th>
              <th>Limit/Capacity</th>
              <th>Cur. Fuel Qty.</th>
              <th>Free Space</th>
              <th>New Stock Quantity</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(distribution, i) in distributions" :key="i">
              <td>{{ i + 1 }}</td>
              <td>{{ distribution.tank.name }}</td>
              <td>{{ distribution.tank.product.name }}</td>
              <td>{{ distribution.tank.limit }}</td>
              <td>{{ distribution.tank.current_fuel_quantity }}</td>
              <td>
                {{
                  distribution.tank.limit -
                  distribution.tank.current_fuel_quantity
                }}
              </td>
              <td>{{ distribution.new_stock_quantity }}</td>
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
  props: ["distributions"],

  mixins: [CurrencyMixin],
};
</script>