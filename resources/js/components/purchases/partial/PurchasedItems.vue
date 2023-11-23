<template>
    <v-card class="mt-2">
        <v-card-title primary-title>
            <h6 class="text-uppercase">Purchased Items</h6>
        </v-card-title>
        <v-card-text>
            <v-simple-table dense>
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th>S#</th>
                            <th>Item</th>
                            <th>Rate</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Sales Tax Amount</th>
                            <th>Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(purchasedItem, i) in purchasedItems"
                            :key="i"
                        >
                            <td>{{ i + 1 }}</td>
                            <td>{{ purchasedItem.purchase_item.name }}</td>
                            <td>{{ purchasedItem.rate }}</td>
                            <td>{{ money(purchasedItem.quantity) }}</td>
                            <td>{{ money(purchasedItem.total) }}</td>
                            <td>{{ money(purchasedItem.sales_tax) }}</td>
                            <td>{{ money(purchasedItem.grand_total) }}</td>
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
    props: ["purchasedItems", "dialog"],

    mixins: [CurrencyMixin],

    methods: {
        closeDialog() {
            this.$emit("closeDialog");
        },
    },
};
</script>
