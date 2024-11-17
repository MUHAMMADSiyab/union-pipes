<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-6 d-block" v-if="monthly_sheet">
            <v-btn
                color="light"
                x-small
                class="mb-3 py-2 d-print-none"
                title="Back to Monthly Sheets"
                @click="$router.push({ name: 'monthly_sheets' })"
                ><v-icon small>mdi-arrow-left</v-icon></v-btn
            >
            <h5 class="text-subtitle-1 mb-2">
                Monthly Sheet Entries for
                <strong>{{ month }}</strong>
            </h5>
            <v-row>
                <v-col cols="12">
                    <v-card class="mb-2">
                        <v-card-title>
                            Total Amount of Assets, Non-Assets &
                            Market</v-card-title
                        >
                        <v-card-text>
                            <v-simple-table dense bordered>
                                <thead>
                                    <tr>
                                        <th class="text-left">Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(entry, index) in assets"
                                        :key="index"
                                    >
                                        <td>
                                            {{ entry.description }}
                                        </td>
                                        <td class="text-right">
                                            {{ money(entry.amount) }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Totals</td>
                                        <td class="text-right">
                                            {{ money(asssetsTotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>
                    <v-card class="mb-2">
                        <v-card-title> Total Payable Amount</v-card-title>
                        <v-card-text>
                            <v-simple-table dense bordered>
                                <thead>
                                    <tr>
                                        <th class="text-left">Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(entry, index) in payables"
                                        :key="index"
                                    >
                                        <td>{{ entry.description }}</td>
                                        <td class="text-right">
                                            {{ money(entry.amount) }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Totals</td>
                                        <td class="text-right">
                                            {{ money(payablesTotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>

                    <v-card class="mb-2">
                        <v-card-title>Overall Totals</v-card-title>
                        <v-card-text>
                            <v-simple-table dense bordered>
                                <tbody>
                                    <tr class="font-weight-bold">
                                        <td class="text-left">
                                            + Total Amount of Assets, Non-Assets
                                            & Market
                                        </td>
                                        <td class="text-right">
                                            {{ money(asssetsTotal) }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td class="text-left">
                                            - Total Amount Payable
                                        </td>
                                        <td class="text-right">
                                            {{ money(payablesTotal) }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td class="text-left">= Total</td>
                                        <td class="text-right">
                                            {{
                                                money(
                                                    asssetsTotal - payablesTotal
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td class="text-left">
                                            - {{ previousMonthName }} Total
                                        </td>
                                        <td class="text-right">
                                            {{
                                                money(
                                                    monthly_sheet.previous_month_total
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td class="text-left">
                                            - Total Profit/Loss for {{ month }}
                                        </td>
                                        <td class="text-right">
                                            {{
                                                money(
                                                    asssetsTotal -
                                                        payablesTotal -
                                                        monthly_sheet.previous_month_total
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

    data() {
        return {
            assets: [],
            payables: [],
        };
    },

    methods: {
        ...mapActions({
            getMonthlySheet: "monthly_sheet/getMonthlySheet",
        }),
    },

    computed: {
        ...mapGetters({
            monthly_sheet: "monthly_sheet/monthly_sheet",
            loading: "loading",
        }),

        month() {
            return new Date(this.monthly_sheet.month).toLocaleDateString(
                "en-US",
                {
                    month: "long",
                    year: "numeric",
                }
            );
        },

        asssetsTotal() {
            return this.assets.reduce(function (b, a) {
                return b + parseInt(a.amount, 10);
            }, 0);
        },
        payablesTotal() {
            return this.payables.reduce(function (b, a) {
                return b + parseInt(a.amount, 10);
            }, 0);
        },

        previousMonthName() {
            if (this.monthly_sheet.month) {
                const date = new Date(this.monthly_sheet.month);
                date.setMonth(date.getMonth() - 1);
                return date.toLocaleString("en-US", {
                    month: "long",
                    year: "numeric",
                });
            }
            return "Previous Month";
        },
    },

    async mounted() {
        await this.getMonthlySheet(parseInt(this.$route.params.id));
        this.assets = this.monthly_sheet.entries.filter(
            (entry) => entry.category === "asset"
        );
        this.payables = this.monthly_sheet.entries.filter(
            (entry) => entry.category === "payable"
        );
    },
};
</script>
