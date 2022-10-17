<template>
  <v-card height="500">
    <v-card-text>
      <h4 class="grey--text">Invoice # {{ invoice.invoice_no }}</h4>
      <h5 class="grey--text text-right ml-auto">Date: {{ invoice.date }}</h5>
      <h4
        class="mb-5 mt-4 text-center text-decoration-underline text-uppercase"
      >
        Sales Tax Invoice
      </h4>

      <v-simple-table dense class="mb-5">
        <tbody>
          <tr>
            <td>Buyer</td>
            <th>{{ invoice.buyer }}</th>
          </tr>
          <tr>
            <td>Address</td>
            <td>{{ invoice.address }}</td>
          </tr>
          <tr>
            <td>GST #</td>
            <td>{{ invoice.gst_no }}</td>
          </tr>
          <tr>
            <td>NTN #</td>
            <td>{{ invoice.ntn_no }}</td>
          </tr>
        </tbody>
      </v-simple-table>

      <v-simple-table dense class="mt-5">
        <thead>
          <tr>
            <th>Quantity (Litres)</th>
            <th>Description</th>
            <th>Rate %</th>
            <th>Amount Excl. Sales Tax</th>
            <th>Sales Tax Rate</th>
            <th>Sales Tax Amount</th>
            <th>Amount Incl. Sales Tax</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ invoice.quantity }}</td>
            <td>{{ invoice.product }}</td>
            <td>{{ invoice.rate }}</td>
            <td>{{ money(invoice.total_amount) }}</td>
            <td>{{ invoice.sales_tax_rate }}</td>
            <td>{{ money(invoice.sales_tax_amount) }}</td>
            <td>{{ money(invoice.amount_including_sales_tax) }}</td>
          </tr>
        </tbody>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
  props: ["invoice"],

  mixins: [CurrencyMixin],
};
</script>