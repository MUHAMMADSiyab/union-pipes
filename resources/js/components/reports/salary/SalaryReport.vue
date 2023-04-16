<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Salary Report</h5>

            <v-row>
                <v-col cols="12">
                    <v-simple-table dense class="mt-3" v-if="reportData.length">
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th>S#</th>
                                    <th>Employee Name</th>
                                    <th>Balance (To be paid)</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(employee, i) in reportData"
                                    :key="employee.id"
                                >
                                    <td>{{ i + 1 }}</td>
                                    <td>{{ employee.name }}</td>
                                    <td class="red--text font-weight-bold">
                                        {{
                                            money(employee.salaries_sum_balance)
                                        }}
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
    components: { Navbar },

    mixins: [CurrencyMixin],

    methods: {
        ...mapActions({
            getSalaryReportData: "report/getSalaryReportData",
        }),
    },

    computed: {
        ...mapGetters({
            reportData: "report/reportData",
            loading: "loading",
        }),
    },

    mounted() {
        this.getSalaryReportData();
    },
};
</script>
