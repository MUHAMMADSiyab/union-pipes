<template>
    <v-card class="mt-2">
        <v-card-title primary-title>
            <h6 class="text-uppercase">Sold Items</h6>
        </v-card-title>
        <v-card-text>
            <v-simple-table dense>
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th>S#</th>
                            <th>Item</th>
                            <th>Weight</th>
                            <th>Rate</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Sales Tax Amount</th>
                            <th>Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(soldItem, i) in soldItems" :key="i">
                            <td>{{ i + 1 }}</td>
                            <td>
                                {{ soldItem.product.name }} ({{
                                    soldItem.product.size
                                }})({{ soldItem.product.type }})
                            </td>
                            <td>{{ money(soldItem.weight) }}</td>
                            <td>{{ money(soldItem.rate) }}</td>
                            <td>{{ money(soldItem.quantity) }}</td>
                            <td>{{ money(soldItem.total) }}</td>
                            <td>{{ money(soldItem.sales_tax) }}</td>
                            <td>{{ money(soldItem.grand_total) }}</td>
                        </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </v-card-text>

        <v-divider v-if="dialog"></v-divider>

        <v-card-actions v-if="dialog">
            <v-btn color="secondary" right @click="closeDialog">Close</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import CurrencyMixin from "../../../mixins/CurrencyMixin";

export default {
    props: ["soldItems", "dialog"],

    mixins: [CurrencyMixin],

    methods: {
        closeDialog() {
            this.$emit("closeDialog");
        },
    },
};
</script>
