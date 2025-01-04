<template>
    <v-simple-table dense class="mt-3 text-center">
        <template v-slot:default>
            <thead>
                <tr class="text-center">
                    <th class="text-center">S#</th>
                    <th class="text-center">Stock Product</th>
                    <th class="text-center">Total Length Produced</th>
                    <th class="text-center">Total Weight Produced</th>
                </tr>
            </thead>

            <tbody class="text-center">
                <tr v-for="(stock_data, i) in data" :key="stock_data.id">
                    <td>{{ i + 1 }}</td>
                    <td>{{ stock_data.stock_product.name }}</td>
                    <td>
                        {{ money(stock_data.total_length) }}
                    </td>
                    <td>
                        {{ money(stock_data.total_weight) }}
                    </td>
                </tr>

                <tr class="footer-row">
                    <td class="font-weight-bold text-center" colspan="2">
                        Overall Totals
                    </td>
                    <td class="font-weight-bold text-center">
                        {{ money(overallLength) }}
                    </td>
                    <td class="font-weight-bold text-center">
                        {{ money(overallWeight) }}
                    </td>
                </tr>
            </tbody>
        </template>
    </v-simple-table>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
    props: ["data"],

    mixins: [CurrencyMixin],

    computed: {
        overallLength() {
            return this.data?.reduce((b, a) => a.total_length + b, 0);
        },

        overallWeight() {
            return this.data?.reduce((b, a) => a.total_weight + b, 0);
        },
    },
};
</script>
<style scoped>
.footer-row {
    font-size: x-large !important;
}
</style>
