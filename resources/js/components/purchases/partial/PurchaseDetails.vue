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
                            <td>Invoice #</td>
                            <td>{{ purchase.invoice_no }}</td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>{{ purchase.date }}</td>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <td>{{ sell.category }}</td>
                        </tr>
                        <tr>
                            <td>Supplier Company</td>
                            <td>{{ purchase.company.name }}</td>
                        </tr>
                        <tr>
                            <td>Sales Tax Percentage</td>
                            <td>{{ purchase.sales_tax_percentage }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td>{{ money(purchase.total_amount) }}</td>
                        </tr>
                        <tr>
                            <td>Total Paid</td>
                            <td>{{ money(purchase.paid) }}</td>
                        </tr>
                        <tr>
                            <td>Balance</td>
                            <td>{{ money(purchase.balance) }}</td>
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
