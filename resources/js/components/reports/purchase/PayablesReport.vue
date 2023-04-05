<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Payables Report</h5>

            <v-row>
                <v-col cols="12">
                    <v-simple-table
                        v-if="!loading && reportData.length"
                        :dense="printMode"
                    >
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left">
                                        Supplier Company Name
                                    </th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(company, i) in reportData" :key="i">
                                    <td class="text-left">
                                        {{ company.name }}
                                    </td>
                                    <td class="text-right">
                                        {{ money(company.balance) }}
                                    </td>
                                </tr>
                                <tr class="font-weight-bold indigo--text">
                                    <td class="text-left">Total</td>
                                    <td class="text-right">
                                        {{ money(getTotalBalance()) }}
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import Navbar from "../../navs/Navbar";

export default {
    components: {
        Navbar,
    },

    mixins: [CurrencyMixin],

    methods: {
        ...mapActions({
            getPayablesReportData: "report/getPayablesReportData",
        }),

        getTotalBalance() {
            let sum = 0;
            for (let i = 0; i < this.reportData.length; i++) {
                sum += this.reportData[i].balance;
            }
            return sum;
        },
    },

    computed: {
        ...mapGetters({
            reportData: "report/reportData",
            loading: "loading",
        }),
    },

    mounted() {
        this.getPayablesReportData();
    },
};
</script>
