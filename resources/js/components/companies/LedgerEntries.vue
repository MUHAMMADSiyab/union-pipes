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
                        <v-card-title primary-title v-if="company"
                            >{{ company.name }}
                            <v-btn
                                color="indigo"
                                class="white--text d-print-none ml-2"
                                to="/companies"
                                small
                                >Back to Supplier Companies</v-btn
                            >
                            <v-spacer></v-spacer>
                            <v-btn
                                color="success"
                                class="white--text d-print-none"
                                @click="exportPDF"
                                small
                                >Export PDF</v-btn
                            >
                        </v-card-title>

                        <v-card-text class="mt-1">
                            <table
                                dense
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
                                    <td>{{ formatDate(entry.date) }}</td>
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

            <v-btn
                fab
                small
                color="primary"
                class="scroll-btn top-btn d-print-none"
                @click="scrollToTop"
            >
                <v-icon>mdi-arrow-up</v-icon>
            </v-btn>
            <v-btn
                fab
                small
                color="primary"
                class="scroll-btn bottom-btn d-print-none"
                @click="scrollToBottom"
            >
                <v-icon>mdi-arrow-down</v-icon>
            </v-btn>
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
            getLedgerEntries: "company/getLedgerEntries",
            getCompany: "company/getCompany",
        }),

        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0"); // Months are zero-based
            const year = String(d.getFullYear());

            return `${day}/${month}/${year}`;
        },

        async exportPDF() {
            const url =
                `/companies/${this.company.id}/ledger/export/pdf` +
                (this.filters.from_date && this.filters.to_date
                    ? `?from_date=${this.filters.from_date}&to_date=${this.filters.to_date}`
                    : "");
            window.open(url, "_blank");
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        },
        scrollToBottom() {
            window.scrollTo({
                top: document.documentElement.scrollHeight,
                behavior: "smooth",
            });
        },

        async filterEntries() {
            if (this.filters.from_date && this.filters.to_date) {
                try {
                    const res = await axios.post(
                        `/api/companies/${this.company.id}/ledger_entries?from_date=${this.filters.from_date}&to_date=${this.filters.to_date}`
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
            ledger_entries: "company/ledger_entries",
            company: "company/company",
            loading: "loading",
        }),

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
            this.getCompany(this.$route.params.id),
            this.getLedgerEntries(this.$route.params.id),
        ]);

        this.filteredEntries = this.ledger_entries;
    },
};
</script>

<style scoped>
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
.scroll-btn {
    position: fixed;
    right: 20px;
    z-index: 1000;
}
.top-btn {
    bottom: 80px;
}
.bottom-btn {
    bottom: 20px;
}
</style>
