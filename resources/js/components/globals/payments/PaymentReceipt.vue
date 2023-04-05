<template>
    <div v-if="paymentData">
        <v-card v-for="(item, i) in [1, 2]" :key="i" class="mt-5 mx-5">
            <v-card-title
                >Payment Receipt for
                {{ paymentData.model.split("\\")[2] }}</v-card-title
            >
            <v-simple-table dense>
                <tbody>
                    <template>
                        <tr>
                            <td>
                                <span
                                    v-if="
                                        paymentData.model ===
                                        'App\\Models\\Purchase'
                                    "
                                    >Payee</span
                                >
                                <span
                                    v-if="
                                        paymentData.model ===
                                        'App\\Models\\Sell'
                                    "
                                    >Payer</span
                                >
                            </td>
                            <td>
                                <span
                                    v-if="
                                        paymentData.model ===
                                        'App\\Models\\Purchase'
                                    "
                                    >{{
                                        paymentData.purchase.company.name
                                    }}</span
                                >
                                <span
                                    v-if="
                                        paymentData.model ===
                                        'App\\Models\\Sell'
                                    "
                                    >{{ paymentData.sell.customer.name }}</span
                                >
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <td>Payment Date:</td>
                        <td>{{ formatDate(paymentData.payment_date) }}</td>
                    </tr>
                    <tr>
                        <td>Amount:</td>
                        <td>{{ money(paymentData.amount) }}</td>
                    </tr>
                    <tr>
                        <td>Discount %:</td>
                        <td>
                            {{ paymentData.discount }} ({{
                                money(
                                    paymentData.amount *
                                        (paymentData.discount / 100)
                                )
                            }})
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span
                                v-if="
                                    paymentData.model ===
                                    'App\\Models\\Purchase'
                                "
                                >Paid</span
                            >
                            <span
                                v-if="paymentData.model === 'App\\Models\\Sell'"
                                >Received</span
                            >
                        </td>
                        <td>
                            {{
                                money(
                                    paymentData.amount -
                                        paymentData.amount *
                                            (paymentData.discount / 100)
                                )
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td>Payment Method:</td>
                        <td>{{ paymentData.payment_method }}</td>
                    </tr>
                    <tr height="60">
                        <td colspan="2" class="text-right">
                            <em style="margin-right: 180px">Signature:</em>
                        </td>
                    </tr>
                </tbody>
            </v-simple-table>
        </v-card>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../../mixins/CurrencyMixin";

export default {
    mixins: [CurrencyMixin],

    data() {
        return {
            paymentData: null,
        };
    },

    methods: {
        ...mapActions({
            getPayment: "payment/getPayment",
        }),

        formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                year: "numeric",
                month: "long",
                day: "numeric",
            };
            return date.toLocaleString("en-US", options);
        },
    },

    computed: {
        ...mapGetters({
            payment: "payment/payment",
            loading: "loading",
        }),
    },

    async mounted() {
        await this.getPayment(this.$route.params.id);

        this.paymentData = this.payment;

        this.print();
    },
};
</script>
