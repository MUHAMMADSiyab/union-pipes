<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row v-if="dashboardData">
                <v-col cols="3">
                    <v-card>
                        <v-card-text>
                            <Card
                                color="light-green"
                                title="Materials Purchased"
                                :number="469000"
                                icon="mdi-archive-arrow-down-outline"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="3">
                    <v-card>
                        <v-card-text>
                            <Card
                                color="indigo"
                                title="Total Pipe Sold"
                                :number="230000"
                                icon="mdi-archive-arrow-up-outline"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="3">
                    <v-card>
                        <v-card-text>
                            <Card
                                color="pink"
                                title="Total Stock Weight"
                                :number="17000"
                                icon="mdi-warehouse"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="3">
                    <v-card>
                        <v-card-text>
                            <Card
                                color="orange"
                                title="Total Expenses"
                                :number="45000"
                                icon="mdi-currency-eur"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12">
                    <SellsLast12MonthsChart
                        :sell-data="dashboardData.lastTwelveMonthsSells"
                    />
                </v-col>

                <v-col cols="6">
                    <Last12MonthsExpensesChart
                        :expenses="
                            dashboardData.lastTwelveMonthsExpenses.chartData
                        "
                        :months="dashboardData.lastTwelveMonthsExpenses.months"
                    />
                </v-col>
                <v-col cols="6">
                    <ReceivablesChart
                        :receivables="dashboardData.receivables"
                    />
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar.vue";
import Last12MonthsExpensesChart from "./partial/charts/Last12MonthsExpensesChart.vue";
import SellsLast12MonthsChart from "./partial/charts/SellsLast12MonthsChart.vue";
import ReceivablesChart from "./partial/charts/ReceivablesChart.vue";
import Card from "./partial/Card.vue";

export default {
    components: {
        Navbar,
        Last12MonthsExpensesChart,
        SellsLast12MonthsChart,
        ReceivablesChart,
        Card,
    },

    mixins: [CurrencyMixin],

    methods: {
        ...mapActions({ getDashboardData: "dashboard/getDashboardData" }),
    },

    computed: {
        ...mapGetters({ dashboardData: "dashboard/dashboardData" }),
    },

    mounted() {
        this.getDashboardData();
    },
};
</script>
