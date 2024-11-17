<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-6 d-block" v-if="stock_sheet">
            <v-btn
                color="light"
                x-small
                class="mb-3 py-2 d-print-none"
                title="Back to Stock Sheets"
                @click="$router.push({ name: 'stock_sheets' })"
                ><v-icon small>mdi-arrow-left</v-icon></v-btn
            >
            <h5 class="text-subtitle-1 mb-2">
                Stock Sheet Entries for
                <strong>{{ month }}</strong>
            </h5>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-simple-table dense bordered>
                                <thead>
                                    <tr>
                                        <th class="text-left">Product</th>
                                        <th class="text-left">Quantity</th>
                                        <th class="text-left">Weight</th>
                                        <th class="text-left">Rate</th>
                                        <th class="text-left">Total Weight</th>
                                        <th class="text-left">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            entry, index
                                        ) in stock_sheet.entries"
                                        :key="index"
                                    >
                                        <td>{{ entry.product }}</td>
                                        <td>{{ money(entry.quantity) }}</td>
                                        <td>{{ money(entry.weight) }}</td>
                                        <td>{{ money(entry.rate) }}</td>
                                        <td>{{ money(entry.total_weight) }}</td>
                                        <td>{{ money(entry.total_amount) }}</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Totals</td>
                                        <td>
                                            {{
                                                money(
                                                    stock_sheet.entries_sum_quantity
                                                )
                                            }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            {{
                                                money(
                                                    stock_sheet.entries_sum_total_weight
                                                )
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                money(
                                                    stock_sheet.entries_sum_total_amount
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";

export default {
    mixins: [CurrencyMixin],

    components: {
        Navbar,
    },

    methods: {
        ...mapActions({
            getStockSheet: "stock_sheet/getStockSheet",
        }),
    },

    computed: {
        ...mapGetters({
            stock_sheet: "stock_sheet/stock_sheet",
            loading: "loading",
        }),

        month() {
            return new Date(this.stock_sheet.month).toLocaleDateString(
                "en-US",
                {
                    month: "long",
                    year: "numeric",
                }
            );
        },
    },

    async mounted() {
        this.getStockSheet(parseInt(this.$route.params.id));
    },
};
</script>
