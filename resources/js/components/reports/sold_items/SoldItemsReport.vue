<template>
    <v-card class="mt-2">
        <v-card-text>
            <table class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Item</th>
                        <th>Weight</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Total</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>

                <tbody>
                    <template v-for="(customer, i) in soldItems">
                        <tr :key="`${i}_${customer.customer_id}`">
                            <td colspan="8" class="text-center">
                                <h4 class="customer-title">
                                    {{ customer.customer_name }}
                                </h4>
                            </td>
                        </tr>
                        <tr
                            v-for="(item, i) in customer.sold_items"
                            :key="`${i}_a_${customer.customer_id}`"
                        >
                            <td>{{ formatDate(item.date) }}</td>
                            <td>{{ item.invoice_no }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ money(item.weight) }}</td>
                            <td>{{ money(item.quantity) }}</td>
                            <td>{{ money(item.rate) }}</td>
                            <td>{{ money(item.total) }}</td>
                            <td>{{ money(item.grand_total) }}</td>
                        </tr>

                        <tr :key="customer.customer_id" class="totals-row">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                {{ money(customer.total_weight) }}
                            </td>
                            <td>
                                {{ money(customer.total_quantity) }}
                            </td>
                            <td></td>
                            <td>
                                {{ money(customer.total_total) }}
                            </td>
                            <td>
                                {{ money(customer.total_grand_total) }}
                            </td>
                        </tr>
                    </template>

                    <!-- Overall totals -->
                    <tr class="overall-totals">
                        <td colspan="3">Overall Totals</td>
                        <td>{{ money(totals.overallWeight) }}</td>
                        <td>{{ money(totals.overallQuantity) }}</td>
                        <td></td>
                        <td>{{ money(totals.overallTotal) }}</td>
                        <td>{{ money(totals.overallGrandTotal) }}</td>
                    </tr>
                </tbody>
            </table>
        </v-card-text>
    </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
    props: ["soldItems", "totals"],

    mixins: [CurrencyMixin],

    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                year: "numeric",
                month: "short",
                day: "numeric",
            };
            return date.toLocaleString("en-US", options);
        },
    },
};
</script>

<style scoped>
.table {
    width: 100%;
    font-size: small;
    table-layout: fixed;
}

.table td,
.table th {
    width: fit-content;
}

.table tr th,
.table tr td {
    padding: 6px;
}

@media print {
    .table tr th,
    .table tr td {
        padding: 2px !important;
    }
}

.table thead tr {
    background: rgb(230, 230, 230);
}
.table thead tr th {
    text-align: left;
}

.customer-title {
    margin-top: 8px;
    font-size: larger;
    text-transform: uppercase;
}
.totals-row td {
    border-top: 1px solid rgb(212, 212, 212);
    border-bottom: 1px solid rgb(212, 212, 212);
    font-weight: bold;
}

.overall-totals {
    border-top: 1px solid rgb(212, 212, 212);
    border-bottom: 1px solid rgb(212, 212, 212);
    font-weight: bold;
    font-size: 0.8rem;
}
</style>
