<template>
  <v-card class="mt-2">
    <v-card-title primary-title>
      <h6 class="text-uppercase">Payment Details</h6>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>Amount Paid</td>
              <td>{{ money(payment.amount) }}</td>
            </tr>
            <tr>
              <td>Balance</td>
              <td>
                {{ money(total_amount - payment.amount) }}
              </td>
            </tr>
            <tr>
              <td>Payment Date</td>
              <td>{{ payment.payment_date }}</td>
            </tr>
            <tr>
              <td>Bank</td>
              <td>{{ payment.bank.name }} ({{ payment.bank.account_no }})</td>
            </tr>
            <tr>
              <td>Payment Method</td>
              <td>{{ payment.payment_method }}</td>
            </tr>
            <template v-if="payment.payment_method === 'Cheque'">
              <tr>
                <td>Cheque No.</td>
                <td>{{ payment.cheque_no }}</td>
              </tr>
              <tr>
                <td>Cheque Type</td>
                <td>{{ payment.cheque_type }}</td>
              </tr>
              <tr>
                <td>Cheque Due Date</td>
                <td>{{ payment.cheque_due_date }}</td>
              </tr>
            </template>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";

export default {
  props: ["payment", "total_amount"],

  mixins: [CurrencyMixin],
};
</script>