<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4" v-if="dashboardData">
            <TotalsCards :totals="dashboardData.totals" />
            <v-row>
                <v-col xl="12" lg="12" md="12" sm="12" cols="12">
                    <Last30DaysProduction
                        :chart-data="dashboardData.last30DaysProduction"
                    />
                </v-col>

                <v-col xl="12" lg="12" md="12" sm="12" cols="12">
                    <SellsLast12MonthsChart
                        :sell-data="dashboardData.lastTwelveMonthsSells"
                    />
                </v-col>

                <v-col xl="7" lg="7" md="7" sm="12" cols="12">
                    <Last12MonthsExpensesChart
                        :expenses="
                            dashboardData.lastTwelveMonthsExpenses.chartData
                        "
                        :months="dashboardData.lastTwelveMonthsExpenses.months"
                    />
                </v-col>

                <v-col xl="5" lg="5" md="5" sm="12" cols="12">
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
import Last30DaysProduction from "./partial/charts/Last30DaysProduction.vue";
import TotalsCards from "./partial/TotalsCards.vue";

export default {
    components: {
        Navbar,
        Last12MonthsExpensesChart,
        SellsLast12MonthsChart,
        ReceivablesChart,
        Last30DaysProduction,
        TotalsCards,
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
