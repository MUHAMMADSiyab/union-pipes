<template>
    <v-simple-table dense class="mt-3">
        <template v-slot:default>
            <thead>
                <tr>
                    <th colspan="2">Total Payments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Paid to Parties</td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.payments.paid_to_parties) }}
                    </td>
                </tr>
                <tr>
                    <td>Received from Customers</td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.payments.received_from_customers) }}
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2">Weights</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Purchased Weight</td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.weights.purchased_weight) }}
                    </td>
                </tr>
                <tr>
                    <td>Purchased Weight Amount</td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.weights.purchased_weight_amount) }}
                    </td>
                </tr>
                <tr>
                    <td>Sold Weight</td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.weights.sold_weight) }}
                    </td>
                </tr>
                <tr>
                    <td>Sold Weight Amount</td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.weights.sold_weight_amount) }}
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2">Total Expenses</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(expense, index) in data.expenses.all_expenses"
                    :key="index"
                >
                    <td>{{ expense.name }}</td>
                    <td class="text-right font-weight-bold">
                        {{ money(expense.total) }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Total Expenses Amount</strong></td>
                    <td class="text-right font-weight-bold">
                        {{ money(data.expenses.expenses_total) }}
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2">Production Cost Per Unit</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Weight Produced</td>
                    <td class="text-right font-weight-bold">
                        {{
                            money(
                                data.production_cost_per_unit
                                    .total_weight_produced
                            )
                        }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Production Cost Per Unit</strong></td>
                    <td class="text-right font-weight-bold">
                        <span class="grey--text">
                            Total Production / Total Expenses ({{
                                money(data.expenses.expenses_total)
                            }}
                            /
                            {{  money(
                                    data.production_cost_per_unit
                                        .total_weight_produced
                                )}} ) =
                        </span>

                        {{
                            money(
                                data.production_cost_per_unit
                                    .production_cost_per_unit
                            )
                        }}
                    </td>
                </tr>
            </tbody>
        </template>
    </v-simple-table>
</template>

<script>
import DatatableMixin from "../../../mixins/DatatableMixin";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import { mapGetters } from "vuex";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    props: ["data"],

    data() {
        return {};
    },

    computed: {
        ...mapGetters({
            loading: "loading",
        }),
    },
};
</script>

<style scoped>
th {
    font-size: 0.9rem !important;
    color: indigo !important;
}
td {
    font-size: small !important;
}
</style>
