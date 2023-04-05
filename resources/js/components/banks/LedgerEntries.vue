<template>
    <div>
        <Navbar v-if="!printMode" />
        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Ledger Entries</h5>

            <v-row>
                <v-col cols="12">
                    <v-card :loading="loading">
                        <v-card-title primary-title v-if="bank"
                            >{{ bank.name }}
                            <v-btn
                                color="indigo"
                                class="white--text ml-auto"
                                to="/banks"
                                small
                                >Back to Banks</v-btn
                            ></v-card-title
                        >

                        <v-card-text class="mt-1">
                            <v-simple-table dense v-if="ledger_entries.length">
                                <template v-slot:default>
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Date</th>
                                            <th>Particular</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(entry, i) in ledger_entries"
                                            :key="i"
                                        >
                                            <td>{{ i + 1 }}</td>
                                            <td>{{ entry.date }}</td>
                                            <td>{{ entry.particular }}</td>
                                            <td>{{ money(entry.debit) }}</td>
                                            <td>{{ money(entry.credit) }}</td>
                                            <td class="font-weight-bold">
                                                {{ money(entry.balance) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
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
        return {};
    },

    methods: {
        ...mapActions({
            getLedgerEntries: "bank/getLedgerEntries",
            getBank: "bank/getBank",
        }),
    },

    computed: {
        ...mapGetters({
            ledger_entries: "bank/ledger_entries",
            bank: "bank/bank",
            loading: "loading",
        }),
    },

    mounted() {
        Promise.all([
            this.getBank(this.$route.params.id),
            this.getLedgerEntries(this.$route.params.id),
        ]);
    },
};
</script>
