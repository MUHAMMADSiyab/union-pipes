<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row v-if="dashboardData">
                <v-col cols="12">
                    <SellsLast12MonthsChart
                        :sell-data="dashboardData.lastTwelveMonthsSells"
                    />
                </v-col>

                <v-col cols="6">
                    <Last12MonthsExpensesChart
                        :expenses="dashboardData.lastTwelveMonthsExpenses"
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

export default {
    components: {
        Navbar,
        Last12MonthsExpensesChart,
        SellsLast12MonthsChart,
        ReceivablesChart,
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
