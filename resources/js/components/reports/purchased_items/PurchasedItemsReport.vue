<template>
    <v-card class="mt-2">
        <v-card-text>
            <table class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Total</th>
                        <th>Sales Tax</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>

                <tbody>
                    <template v-for="(company, i) in purchasedItems">
                        <tr :key="`${i}_${company.company_id}`">
                            <td colspan="8" class="text-center">
                                <h4 class="company-title">
                                    {{ company.company_name }}
                                </h4>
                            </td>
                        </tr>
                        <tr
                            v-for="(item, i) in company.purchased_items"
                            :key="`${i}_a_${company.company_id}`"
                        >
                            <td>{{ formatDate(item.date) }}</td>
                            <td>{{ item.invoice_no }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ money(item.quantity) }}</td>
                            <td>{{ money(item.rate) }}</td>
                            <td>{{ money(item.total) }}</td>
                            <td>{{ money(item.sales_tax) }}</td>
                            <td>{{ money(item.grand_total) }}</td>
                        </tr>

                        <tr :key="company.company_id" class="totals-row">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                {{ money(company.total_quantity) }}
                            </td>
                            <td></td>
                            <td>
                                {{ money(company.total_total) }}
                            </td>
                            <td>
                                {{ money(company.total_sales_tax) }}
                            </td>
                            <td>
                                {{ money(company.total_grand_total) }}
                            </td>
                        </tr>
                    </template>

                    <!-- Overall totals -->
                    <tr class="overall-totals">
                        <td colspan="3">Overall Totals</td>
                        <td>{{ money(totals.overallQuantity) }}</td>
                        <td></td>
                        <td>{{ money(totals.overallTotal) }}</td>
                        <td>{{ money(totals.overallSalesTax) }}</td>
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
    props: ["purchasedItems", "totals"],

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

.company-title {
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
