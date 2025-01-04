<template>
    <div>
        <Navbar v-if="!printMode" />
        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Ledger Entries</h5>

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
                    <v-card :loading="loading">
                        <v-card-title primary-title v-if="customer"
                            >{{ customer.name }}
                            <v-btn
                                color="indigo"
                                class="white--text ml-auto d-print-none"
                                to="/customers"
                                small
                                >Back to Customers</v-btn
                            ></v-card-title
                        >

                        <v-card-text class="mt-1">
                            <table
                                v-if="filteredEntries.length"
                                cellspacing="0"
                            >
                                <tr>
                                    <th>S#</th>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>

                                <tr
                                    v-for="(entry, i) in filteredEntries"
                                    :key="i"
                                >
                                    <td>{{ i + 1 }}</td>
                                    <td>
                                        {{ formatDate(entry.date) }}
                                    </td>
                                    <td>{{ entry.invoice_no }}</td>
                                    <td>{{ entry.description }}</td>
                                    <td>{{ money(entry.debit) }}</td>
                                    <td>{{ money(entry.credit) }}</td>
                                    <td class="font-weight-bold">
                                        {{ money(entry.balance) }}
                                    </td>
                                </tr>

                                <tr style="color: #000">
                                    <td colspan="4" class="text-center">
                                        <strong>Total</strong>
                                    </td>
                                    <td>
                                        <strong>{{ money(totalDebit) }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{
                                            money(totalCredit)
                                        }}</strong>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    mixins: [CurrencyMixin],

    data() {
        return {
            filteredEntries: [],
            filters: {
                from_date: "",
                to_date: "",
            },
        };
    },

    methods: {
        ...mapActions({
            getLedgerEntries: "customer/getLedgerEntries",
            getCustomer: "customer/getCustomer",
        }),

        formatDate(date) {
            return new Date(date).toLocaleString("en-US", {
                day: "2-digit",
                month: "long",
                year: "numeric",
                // hour: "2-digit",
                // minute: "2-digit",
                // second: "2-digit",
                // hour12: true,
            });
        },
        async filterEntries() {
            if (this.filters.from_date && this.filters.to_date) {
                try {
                    const res = await axios.post(
                        `/api/customers/${this.customer.id}/ledger_entries?from_date=${this.filters.from_date}&to_date=${this.filters.to_date}`
                    );

                    this.filteredEntries = res.data;
                } catch (error) {
                    console.log(error);
                }
            }
        },
    },

    computed: {
        ...mapGetters({
            ledger_entries: "customer/ledger_entries",
            customer: "customer/customer",
            loading: "loading",
        }),

        // filteredEntries() {
        //     const { from_date, to_date } = this.filters;

        //     if (!from_date || !to_date) {
        //         return this.ledger_entries;
        //     }

        //     return this.ledger_entries.filter((entry) => {
        //         const entryDate = new Date(entry.date);
        //         const fromDate = new Date(from_date);
        //         const toDate = new Date(to_date);

        //         // Include entries on the selected date
        //         toDate.setDate(toDate.getDate()); // Add one day to the toDate
        //         return entryDate >= fromDate && entryDate <= toDate;
        //     });
        // },

        totalDebit() {
            return this.filteredEntries.reduce((total, entry) => {
                return total + entry.debit;
            }, 0);
        },

        totalCredit() {
            return this.filteredEntries.reduce((total, entry) => {
                return total + entry.credit;
            }, 0);
        },
    },

    watch: {
        filters: {
            handler(newVal) {
                if (newVal.from_date && newVal.to_date) {
                    this.filterEntries();
                }
            },
            deep: true,
        },
    },

    async mounted() {
        await Promise.all([
            this.getCustomer(this.$route.params.id),
            this.getLedgerEntries(this.$route.params.id),
        ]);

        this.filteredEntries = this.ledger_entries;
    },
};
</script>

<style>
table {
    width: 100%;
    text-align: left !important;
    padding: 5px;
    color: rgb(29, 29, 29) !important;
}

table thead {
    text-align: left !important;
}

td,
th {
    padding: 4px;
    border-bottom: 1px solid rgb(83, 83, 83) !important;
}

@media print {
    table {
        padding: 2px !important;
        font-size: 10px !important;
    }
}
</style>
