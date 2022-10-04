<template>
  <!-- Purchase -->
  <v-card>
    <v-card-title primary-title>
      <h6 class="text-uppercase">Purchase Details</h6>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>Company</td>
              <td>{{ purchase.company.name }}</td>
            </tr>
            <tr>
              <td>Invoice No.</td>
              <td>{{ purchase.invoice_no }}</td>
            </tr>
            <tr>
              <td>Date</td>
              <td>{{ purchase.date }}</td>
            </tr>
            <tr>
              <td>Petrol Quantity</td>
              <td>{{ purchase.petrol_quantity }}</td>
            </tr>
            <tr>
              <td>Diesel Quantity</td>
              <td>{{ purchase.diesel_quantity }}</td>
            </tr>
            <tr>
              <td>Petrol Price / Ltr.</td>
              <td>{{ purchase.petrol_price }}</td>
            </tr>
            <tr>
              <td>Diesel Price / Ltr.</td>
              <td>{{ purchase.diesel_price }}</td>
            </tr>
            <tr>
              <td>Total Amount</td>
              <td>{{ money(purchase.total_amount) }}</td>
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
  props: ["purchase"],

  mixins: [CurrencyMixin],
};
</script>