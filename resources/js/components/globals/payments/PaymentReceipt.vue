<template>
    <div v-if="paymentData">
        <v-card v-for="(item, i) in [1, 2]" :key="i" class="mt-5 mx-5">
            <v-card-title
                >Payment Receipt for
                {{ paymentData.model.split("\\")[2] }}</v-card-title
            >
            <v-simple-table dense>
                <tbody>
                    <template v-if="paymentData.transaction_type === 'Credit'">
                        <tr>
                            <td>Payee:</td>
                            <td
                                v-if="
                                    paymentData.model ===
                                    'App\\Models\\Purchase'
                                "
                            >
                                {{ paymentData.purchase.company.name }}
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
                        <td>Bank:</td>
                        <td>{{ paymentData.bank.name }}</td>
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
                hour: "numeric",
                minute: "numeric",
                second: "numeric",
                hour12: true,
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
