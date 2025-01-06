<template>
    <div>
        <Navbar v-if="!printMode" />
        <print-button />
        <v-container class="mt-4">
            <h4 class="text-title" v-if="partner">{{ partner.name }}</h4>
            <h5 class="text-subtitle-2 mb-2 grey--text darken-3">
                Partner Transactions
            </h5>

            <v-row>
                <v-col cols="12">
                    <v-btn
                        color="success"
                        small
                        link
                        :to="`/partner_transactions/add`"
                        class="ma-2 text-right float-right"
                        v-if="can('partner_transaction_create')"
                    >
                        <v-icon left>mdi-plus-thick</v-icon>
                        New Transaction</v-btn
                    >
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <v-row align="center">
                        <v-col cols="4">
                            <v-text-field
                                v-model="filterData.search"
                                placeholder="Search"
                                append-icon="mdi-magnify"
                                dense
                            />
                        </v-col>
                        <v-spacer></v-spacer>
                        <v-col cols="3">
                            <v-text-field
                                v-model="filterData.from_date"
                                label="From"
                                type="date"
                                dense
                            />
                        </v-col>
                        <v-col cols="3">
                            <v-text-field
                                v-model="filterData.to_date"
                                label="To"
                                type="date"
                                dense
                            />
                        </v-col>
                    </v-row>
                    <v-simple-table dense>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left caption">S#</th>
                                    <th class="text-left caption">Date</th>
                                    <th class="text-left caption">Title</th>
                                    <th class="text-left caption">
                                        Description
                                    </th>
                                    <th class="text-right caption">Debit</th>
                                    <th class="text-right caption">Credit</th>
                                    <th class="text-right caption">Balance</th>
                                    <th class="text-center caption">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        item, index
                                    ) in partner_transactions"
                                    :key="item.id"
                                >
                                    <td class="caption">{{ index + 1 }}</td>
                                    <td class="caption">
                                        {{
                                            formatDate(
                                                item.payment.payment_date
                                            )
                                        }}
                                    </td>
                                    <td class="caption">{{ item.title }}</td>
                                    <td class="caption">
                                        {{ item.description }}
                                    </td>
                                    <td class="text-right caption">
                                        {{ money(item.debit) }}
                                    </td>
                                    <td class="text-right caption">
                                        {{ money(item.credit) }}
                                    </td>
                                    <td class="text-right caption">
                                        {{ money(item.balance) }}
                                    </td>
                                    <td class="text-center">
                                        <v-btn
                                            x-small
                                            text
                                            color="primary"
                                            :to="`/partner_transactions/edit/${item.id}`"
                                            title="Edit"
                                            v-if="
                                                can('partner_transaction_edit')
                                            "
                                        >
                                            <v-icon x-small>mdi-pencil</v-icon>
                                        </v-btn>
                                        <v-btn
                                            x-small
                                            text
                                            color="red darken-2"
                                            @click="
                                                setPartnerTransactionId(item.id)
                                            "
                                            title="Delete"
                                            v-if="
                                                can(
                                                    'partner_transaction_delete'
                                                )
                                            "
                                        >
                                            <v-icon x-small>mdi-delete</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>

                                <tr v-if="totals">
                                    <td
                                        colspan="4"
                                        class="font-weight-bold text-center"
                                    >
                                        Totals
                                    </td>
                                    <td class="font-weight-bold text-right">
                                        {{ money(totals.total_debit) }}
                                    </td>
                                    <td class="font-weight-bold text-right">
                                        {{ money(totals.total_credit) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>

                    <Confirmation
                        ref="confirmationComponent"
                        :id="partner_transactionId"
                        @confirmDeletion="handlePartnerTransactionDelete"
                    />
                </v-col>
            </v-row>
            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditPartnerTransaction from "./EditPartnerTransaction.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],
    components: {
        EditPartnerTransaction,
        Navbar,
        Confirmation,
    },
    data() {
        return {
            partnerId: null,
            filterData: {
                search: "",
                from_date: "",
                to_date: "",
            },

            partner_transactionId: null,
        };
    },
    methods: {
        ...mapActions({
            getPartnerTransactions:
                "partner_transaction/getPartnerTransactions",
            deletePartnerTransaction:
                "partner_transaction/deletePartnerTransaction",
            getPartner: "partner/getPartner",
        }),

        formatDate(date) {
            return new Date(date).toLocaleString("en-US", {
                day: "2-digit",
                month: "long",
                year: "numeric",
            });
        },

        setPartnerTransactionId(id) {
            this.partner_transactionId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handlePartnerTransactionDelete() {
            await this.deletePartnerTransaction(this.partner_transactionId);
            this.partner_transactionId = null;
            this.$refs.confirmationComponent.setDialog(false);

            return this.$router.push({
                name: "partner_transactions",
                query: { partner_id: this.partnerId },
            });
        },
    },
    computed: {
        ...mapGetters({
            partner_transactions: "partner_transaction/partner_transactions",
            totals: "partner_transaction/totals",
            partner: "partner/partner",
        }),
    },

    watch: {
        filterData: {
            handler: function (newVal, oldVal) {
                this.getPartnerTransactions({
                    partnerId: this.partnerId,
                    search: newVal.search,
                    from_date: newVal.from_date,
                    to_date: newVal.to_date,
                });
            },
            deep: true,
        },
    },

    async mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        this.partnerId = urlParams.get("partner_id");

        if (!this.partnerId) {
            return this.$router.push({ name: "partners" });
        }

        await this.getPartner(this.partnerId);

        await this.getPartnerTransactions({
            partnerId: this.partnerId,
            search: this.search,
            from_date: this.from_date,
            to_date: this.to_date,
        });
    },
};
</script>
<style scoped>
.v-application .caption {
    font-size: 0.85rem !important;
}
</style>
