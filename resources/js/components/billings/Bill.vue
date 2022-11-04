<template>
  <v-card class="mt-3 mb-5">
    <h3 class="text-center py-4">Customer's Account Bill</h3>

    <v-card-text>
      <v-simple-table dense>
        <tbody>
          <tr>
            <td colspan="2">
              <h5 class="font-weight-bold text-uppercase blue-grey--text">
                Customer Details
              </h5>
            </td>
          </tr>
          <tr>
            <td>Name</td>
            <td>{{ billingData.customer.name }}</td>
          </tr>
          <tr>
            <td>CNIC</td>
            <td>{{ billingData.customer.cnic }}</td>
          </tr>
          <tr>
            <td>Phone</td>
            <td>{{ billingData.customer.phone }}</td>
          </tr>
          <tr>
            <td>Address</td>
            <td>{{ billingData.customer.address }}</td>
          </tr>
        </tbody>
      </v-simple-table>

      <h4
        class="d-block mb-3 text-center mt-5 font-weight-bold blue-grey--text"
      >
        {{ formData.from_date }} <span class="black--text">to</span>
        {{ formData.to_date }}
      </h4>

      <v-simple-table dense style="margin-top: 50px; margin-bottom: 50px">
        <thead>
          <tr>
            <th>Particualr</th>
            <th>Quantity</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Petrol</td>
            <td>{{ billingData.petrolTotal }}</td>
            <td>{{ money(billingData.petrolAmount) }}</td>
          </tr>
          <tr>
            <td>Diesel</td>
            <td>{{ billingData.dieselTotal }}</td>
            <td>{{ money(billingData.dieselAmount) }}</td>
          </tr>
          <tr class="font-weight-bold" style="font-size: larger">
            <td colspan="2">Total Amount</td>
            <td>
              {{ money(billingData.petrolAmount + billingData.dieselAmount) }}
            </td>
          </tr>
        </tbody>
      </v-simple-table>

      <v-btn
        color="info"
        small
        link
        class="ma-2 text-right d-print-none"
        @click="print"
      >
        <v-icon left>mdi-printer</v-icon>
        Print Bill</v-btn
      >
    </v-card-text>
  </v-card>
</template>

<script>
import CurrencyMixin from "../../mixins/CurrencyMixin";
export default {
  props: ["billingData", "formData"],

  mixins: [CurrencyMixin],
};
</script>