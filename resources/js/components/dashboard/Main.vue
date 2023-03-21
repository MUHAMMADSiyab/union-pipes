<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row v-if="dashboardData"> </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar.vue";
import ExpensesChart from "./partial/charts/ExpensesChart.vue";

export default {
    components: {
        Navbar,
        ExpensesChart,
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
