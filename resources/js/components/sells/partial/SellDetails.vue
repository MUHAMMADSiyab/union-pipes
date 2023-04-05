<template>
    <!-- Sell -->
    <v-card>
        <v-card-title primary-title>
            <h6 class="text-uppercase">Sell Details</h6>
        </v-card-title>
        <v-card-text>
            <v-simple-table dense>
                <template v-slot:default>
                    <tbody>
                        <tr>
                            <td>Invoice #</td>
                            <td>{{ sell.invoice_no }}</td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>{{ sell.date }}</td>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <td>{{ sell.category }}</td>
                        </tr>
                        <tr>
                            <td>Unit</td>
                            <td>{{ sell.unit }}</td>
                        </tr>
                        <tr>
                            <td>Customer</td>
                            <td>{{ sell.customer.name }}</td>
                        </tr>
                        <tr>
                            <td>Sales Tax Percentage</td>
                            <td>{{ sell.sales_tax_percentage }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td>
                                <span>
                                    {{ money(sell.discounted_total_amount) }}
                                </span>
                                <small
                                    class="purple--text"
                                    v-if="sell.discount > 0"
                                    >({{ sell.discount }}% discount applied on
                                    {{ money(sell.total_amount) }})</small
                                >
                            </td>
                        </tr>
                        <tr>
                            <td>Total Paid</td>
                            <td>
                                {{ money(sell.paid) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Balance</td>
                            <td>{{ money(sell.balance) }}</td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>{{ sell.description }}</td>
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
