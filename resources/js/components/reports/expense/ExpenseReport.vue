<template>
    <v-card class="mt-2">
        <v-card-text>
            <table class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    <template v-for="(expenseSource, i) in expenseData">
                        <tr :key="`${i}_${expenseSource.id}`">
                            <td colspan="8" class="text-center">
                                <h4 class="expense-source-title">
                                    {{ expenseSource.name }}
                                </h4>
                            </td>
                        </tr>
                        <tr
                            v-for="(expense, i) in expenseSource.expenses"
                            :key="`${i}_a_${expense.id}`"
                        >
                            <td>{{ formatDate(expense.date) }}</td>
                            <td>{{ expense.name }}</td>
                            <td>{{ money(expense.amount) }}</td>
                        </tr>

                        <tr :key="expenseSource.id" class="totals-row">
                            <td colspan="2"></td>
                            <td>
                                {{ money(expenseSource.total) }}
                            </td>
                        </tr>
                    </template>

                    <!-- Overall totals -->
                    <tr class="overall-totals">
                        <td colspan="2">Overall Totals</td>
                        <td>{{ money(totals.overallTotal) }}</td>
                    </tr>
                </tbody>
            </table>
        </v-card-text>
    </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
    props: ["expenseData", "totals"],

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
    /* table-layout: fixed; */
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

.expense-source-title {
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
