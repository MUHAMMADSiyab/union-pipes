<template>
    <div>
        <v-card v-if="!editMode" elevation="1">
            <v-card-title primary-title v-if="type !== 'nonDialog'"
                >Payments</v-card-title
            >
            <v-card-subtitle v-if="entryType && type !== 'nonDialog'"
                >View {{ entryType }} payments
            </v-card-subtitle>

            <v-card-text class="mt-1">
                <v-data-table
                    :headers="headers"
                    :items="allPayments"
                    class="elevation-1"
                    item-key="id"
                    :items-per-page="perPage"
                    loading-text="Loading Payments..."
                    :footer-props="footerProps"
                    v-if="!currentChequeImages"
                    :show-select="!printMode && type !== 'nonDialog'"
                    v-model="selectedItems"
                    :hide-default-footer="type === 'nonDialog'"
                >
                    <!-- Top -->
                    <template
                        v-slot:top
                        v-if="!printMode && type !== 'nonDialog'"
                    >
                        <Excel
                            module="payments"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                        <CSV
                            module="payments"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                        <PDF
                            module="payments"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                    </template>

                    <!-- S# -->
                    <template slot="item.sno" slot-scope="props">{{
                        props.index + 1
                    }}</template>

                    <!-- Amount -->
                    <template slot="item.payment_date" slot-scope="props">
                        <span> {{ formatDate(props.item.payment_date) }} </span>
                    </template>

                    <!-- Amounts -->
                    <template slot="item.amount" slot-scope="props">
                        <span> {{ money(props.item.amount) }} </span>
                    </template>

                    <!-- Description -->
                    <template
                        slot="item.description"
                        slot-scope="props"
                        v-if="props.item.description"
                    >
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <span v-bind="attrs" v-on="on">
                                    {{
                                        props.item.description &&
                                        props.item.description.slice(0, 35)
                                    }}...
                                </span>
                            </template>
                            <span>{{ props.item.description }}</span>
                        </v-tooltip>
                    </template>

                    <!-- Cheque Images (Button) -->
                    <template
                        slot="item.cheque_images"
                        slot-scope="props"
                        v-if="props.item.cheque_images.length"
                    >
                        <v-btn
                            color="light"
                            x-small
                            title="Cheque Image(s)"
                            @click="
                                setCurrentChequeImages(props.item.cheque_images)
                            "
                            ><v-icon>mdi-file-image-outline</v-icon></v-btn
                        >
                    </template>

                    <!-- Actions -->
                    <template slot="item.actions" slot-scope="props">
                        <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    x-small
                                    v-bind="attrs"
                                    v-on="on"
                                    text
                                    title="Action"
                                >
                                    <v-icon small>mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>

                            <v-list dense>
                                <v-list-item
                                    :to="`/payments/${props.item.id}/receipt`"
                                    title="Print Receipt"
                                >
                                    <v-list-item-title
                                        >Print Receipt</v-list-item-title
                                    >
                                </v-list-item>

                                <v-list-item
                                    @click="setEditMode(props.item.id)"
                                    title="Edit"
                                    v-if="can('payment_edit')"
                                >
                                    <v-list-item-title>Edit</v-list-item-title>
                                </v-list-item>

                                <v-list-item
                                    title="Delete"
                                    @click="
                                        setPaymentId(
                                            props.item.id,
                                            props.item.model
                                        )
                                    "
                                    v-if="can('payment_delete')"
                                >
                                    <v-list-item-title
                                        >Delete</v-list-item-title
                                    >
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </template>
                </v-data-table>

                <v-btn
                    color="info"
                    @click="currentChequeImages = null"
                    v-if="currentChequeImages"
                    class="mt-1 mb-3"
                    title="Back to Entries"
                    text
                    ><v-icon large>mdi-chevron-left</v-icon></v-btn
                >

                <!-- Cheque Images -->
                <div v-if="currentChequeImages" class="mt-1 mb-1">
                    <v-img
                        v-for="(image, i) in currentChequeImages"
                        :key="i"
                        :aspect-ratio="2"
                        class="mb-2"
                        :src="image"
                    >
                    </v-img>
                </div>

                <v-btn
                    color="secondary"
                    @click="closeDialog"
                    class="mt-3 d-print-none"
                    v-if="type !== 'nonDialog'"
                    >Close</v-btn
                >

                <!-- Confirmation -->
                <Confirmation
                    ref="confirmationComponent"
                    :id="paymentId"
                    @confirmDeletion="handlePaymentDelete()"
                />
            </v-card-text>
        </v-card>

        <!-- Edit Form -->
        <EditPayment
            :payment="payment"
            :payment-setting="paymentSetting"
            :entry="entry"
            @closeEditMode="closeEditMode"
            v-if="editMode"
            :employee-id="employeeId"
        />
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../../mixins/DatatableMixin";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import Confirmation from "../../globals/Confirmation";
import EditPayment from "./EditPayment.vue";
import Excel from "../exports/Excel.vue";
import CSV from "../exports/CSV.vue";
import PDF from "../exports/PDF.vue";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    props: [
        "payments",
        "paymentSetting",
        "entryType",
        "entry",
        "type",
        "employeeId",
    ],

    components: {
        EditPayment,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            allPayments: this.payments,
            paymentId: null,
            paymentModel: null,
            currentChequeImages: null,
            selectedItems: [],
            editMode: false,
            headers: [
                { text: "S#", value: "sno" },
                {
                    text: `Amount (${this.paymentSetting.currency})`,
                    value: "amount",
                },
                { text: "Payment Date", value: "payment_date" },
                { text: "Payment Method", value: "payment_method" },
                { text: "Bank", value: "bank.name" },
                { text: "Cheque Type", value: "cheque_type" },
                { text: "Cheque No.", value: "cheque_no" },
                { text: "Cheque Due Date", value: "cheque_due_date" },
                { text: "Description", value: "description" },
                { text: "Cheque Images", value: "cheque_images" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
        };
    },

    methods: {
        ...mapActions({
            getPayment: "payment/getPayment",
            deletePayment: "payment/deletePayment",
        }),

        setPaymentId(id, model) {
            this.paymentId = id;
            this.paymentModel = model;
            this.$refs.confirmationComponent.setDialog(true);
        },

        setCurrentChequeImages(images) {
            this.currentChequeImages = images;
        },

        async setEditMode(paymentId) {
            await this.getPayment(paymentId);
            this.editMode = true;
        },

        async handlePaymentDelete() {
            await this.deletePayment({
                id: this.paymentId,
                model: this.paymentModel,
                employeeId: this.employeeId,
            });

            this.allPayments = this.allPayments.filter(
                (payment) => payment.id !== this.paymentId
            );

            this.paymentId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString("en-US", {
                month: "short",
                day: "2-digit",
                year: "numeric",
            });
        },

        closeEditMode() {
            this.editMode = false;
        },

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    watch: {
        payments: {
            handler(newPayments) {
                this.allPayments = newPayments;
            },
            deep: true,
        },
    },

    computed: {
        ...mapGetters({ payment: "payment/payment" }),
    },
};
</script>
