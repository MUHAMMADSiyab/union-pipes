<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Payables Report</h5>

            <v-card class="mb-2 d-print-none">
                <v-card-text>
                    <v-row class="mt-2">
                        <v-col
                            xl="6"
                            lg="6"
                            md="6"
                            sm="12"
                            cols="12"
                            class="py-0"
                        >
                            <v-menu max-width="290px" min-width="auto">
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="filters.from_date"
                                        v-on="on"
                                        label="From Date"
                                        prepend-inner-icon="mdi-calendar"
                                        dense
                                        filled
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="filters.from_date"
                                    label="From Date"
                                    no-title
                                    dense
                                    show-current
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col
                            xl="6"
                            lg="6"
                            md="6"
                            sm="12"
                            cols="12"
                            class="py-0"
                        >
                            <v-menu max-width="290px" min-width="auto">
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="filters.to_date"
                                        v-on="on"
                                        label="To Date"
                                        prepend-inner-icon="mdi-calendar"
                                        dense
                                        filled
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="filters.to_date"
                                    label="To Date"
                                    no-title
                                    outlined
                                    dense
                                    show-current
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

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

    data() {
        return {
            filters: {
                from_date: "",
                to_date: "",
            },
        };
    },

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

    watch: {
        filters: {
            handler(newVal) {
                if (newVal.from_date && newVal.to_date) {
                    this.getPayablesReportData(newVal);
                }
            },
            deep: true,
        },
    },

    mounted() {
        this.getPayablesReportData(this.filters);
    },
};
</script>
