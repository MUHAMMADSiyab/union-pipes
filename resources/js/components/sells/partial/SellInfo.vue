<template>
  <!-- Sell Info -->
  <v-card>
    <v-card-title primary-title>
      <h6 class="text-uppercase">Sell Details</h6>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>Date</td>
              <td>{{ sell.sell_date }}</td>
            </tr>
            <tr>
              <td>Petrol Price</td>
              <td>{{ sell.petrol_price }}</td>
            </tr>
            <tr>
              <td>Diesel Price</td>
              <td>{{ sell.diesel_price }}</td>
            </tr>

            <tr>
              <td>Petrol Initial Reading</td>
              <td>{{ sell.petrol_initial_reading }}</td>
            </tr>

            <tr>
              <td>Petrol Final Reading</td>
              <td>{{ sell.petrol_final_reading }}</td>
            </tr>

            <tr>
              <td>Diesel Initial Reading</td>
              <td>{{ sell.diesel_initial_reading }}</td>
            </tr>

            <tr>
              <td>Diesel Final Reading</td>
              <td>{{ sell.diesel_final_reading }}</td>
            </tr>

            <tr>
              <td>Petrol Sold Quantity</td>
              <td>{{ sell.petrol_sold_quantity }}</td>
            </tr>

            <tr>
              <td>Diesel Sold Quantity</td>
              <td>{{ sell.diesel_sold_quantity }}</td>
            </tr>

            <tr>
              <td>Petrol Sold Amount</td>
              <td>{{ sell.petrol_sold_amount }}</td>
            </tr>

            <tr>
              <td>Diesel Sold Amount</td>
              <td>{{ sell.diesel_sold_amount }}</td>
            </tr>

            <tr>
              <td>Total Sell Amount</td>
              <td>{{ money(sell.total_sell_amount) }}</td>
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
  props: ["sell"],

  mixins: [CurrencyMixin],
};
</script>