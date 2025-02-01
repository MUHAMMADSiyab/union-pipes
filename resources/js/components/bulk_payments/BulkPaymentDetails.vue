<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-6 d-block" v-if="bulk_payment">
            <h5 class="text-subtitle-1 mb-2">Bulk Payment Details</h5>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-simple-table>
                                <tr v-if="bulk_payment.is_advance">
                                    <td>Advance Payment</td>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td>{{ bulk_payment.type }}</td>
                                </tr>
                                <tr v-if="bulk_payment.type === 'Sell'">
                                    <td>Customer</td>
                                    <td>{{ bulk_payment.customer?.name }}</td>
                                </tr>
                                <tr v-if="bulk_payment.type === 'Purchase'">
                                    <td>Company</td>
                                    <td>{{ bulk_payment.company?.name }}</td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>{{ bulk_payment.date }}</td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td>{{ money(bulk_payment.amount) }}</td>
                                </tr>
                                <tr>
                                    <td>Bank</td>
                                    <td>{{ bulk_payment.bank?.name }}</td>
                                </tr>
                                <tr>
                                    <td>Payment Method</td>
                                    <td>{{ bulk_payment.payment_method }}</td>
                                </tr>
                                <tr v-if="isChequePayment">
                                    <td>Cheque Type</td>
                                    <td>{{ bulk_payment.cheque_type }}</td>
                                </tr>
                                <tr v-if="isChequePayment">
                                    <td>Cheque #</td>
                                    <td>
                                        {{ bulk_payment.cheque_no }}
                                    </td>
                                </tr>
                                <tr v-if="isChequePayment">
                                    <td>Cheque Due Date</td>
                                    <td>
                                        {{ bulk_payment.cheque_due_date }}
                                    </td>
                                </tr>

                                <tr v-if="isChequePayment">
                                    <td colspan="2">
                                        <ChequeImages
                                            :hide-actions="true"
                                            :current-cheque-images="
                                                bulk_payment.cheque_images
                                            "
                                        />
                                    </td>
                                </tr>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";
import ChequeImages from "../globals/ChequeImages.vue";

export default {
    mixins: [CurrencyMixin],

    components: {
        Navbar,
        ChequeImages,
    },

    methods: {
        ...mapActions({
            getBulkPayment: "bulk_payment/getBulkPayment",
        }),
    },

    computed: {
        ...mapGetters({
            bulk_payment: "bulk_payment/bulk_payment",
            loading: "loading",
        }),

        isChequePayment() {
            return this.bulk_payment.payment_method === "Cheque";
        },
    },

    async mounted() {
        this.getBulkPayment(parseInt(this.$route.params.id));
    },
};
</script>
