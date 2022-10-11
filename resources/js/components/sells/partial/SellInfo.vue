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
              <td>Total Initial Reading (Ltrs.)</td>
              <td>{{ sell.total_initial_reading }}</td>
            </tr>
            <tr>
              <td>Total Final Reading (Ltrs.)</td>
              <td>{{ sell.total_final_reading }}</td>
            </tr>
            <tr>
              <td>Quantity Sold (Ltrs.)</td>
              <td>
                {{ sell.total_final_reading - sell.total_initial_reading }}
              </td>
            </tr>
            <tr>
              <td>Initial Reading Amount</td>
              <td>{{ money(sell.initial_reading_amount) }}</td>
            </tr>
            <tr>
              <td>Final Reading Amount</td>
              <td>{{ money(sell.final_reading_amount) }}</td>
            </tr>
            <tr>
              <td>Total Sell Amount</td>
              <td>{{ money(sell.total_amount) }}</td>
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