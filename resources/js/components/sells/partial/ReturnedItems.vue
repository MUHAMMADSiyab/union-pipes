<template>
    <v-card class="mt-2">
        <v-card-title primary-title>
            <h6 class="text-uppercase">Returned Items</h6>
        </v-card-title>
        <v-card-text>
            <v-simple-table dense>
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th>S#</th>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Weight</th>
                            <th>Rate</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(returnedItem, i) in returnedItems" :key="i">
                            <td>{{ i + 1 }}</td>
                            <td>{{ returnedItem.date }}</td>
                            <td>
                                {{ returnedItem.product.name }} ({{
                                    returnedItem.product.size
                                }})({{ returnedItem.product.type }})
                            </td>
                            <td>{{ money(returnedItem.weight) }}</td>
                            <td>{{ money(returnedItem.rate) }}</td>
                            <td>{{ money(returnedItem.quantity) }}</td>
                            <td>{{ money(returnedItem.total) }}</td>
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
    props: ["returnedItems"],

    mixins: [CurrencyMixin],
};
</script>
