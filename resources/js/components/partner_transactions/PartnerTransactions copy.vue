<template>
    <div>
        <Navbar v-if="!printMode" />
        <print-button />
        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Partner Transactions</h5>
            <!-- Partner Selection -->
            <v-row>
                <v-col cols="12">
                    <v-select
                        v-model="selectedPartner"
                        :items="partners"
                        item-text="name"
                        item-value="id"
                        label="Select Partner"
                        dense
                        filled
                    >
                        <template v-slot:selection="{ item }">
                            <v-list-item-content>
                                <v-list-item-title>{{
                                    item.name
                                }}</v-list-item-title>
                            </v-list-item-content>
                        </template>
                    </v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="displayedHeaders"
                        :items="partner_transactions.data"
                        class="elevation-1"
                        item-key="id"
                        :loading="loading"
                        :options.sync="options"
                        :server-items-length="total"
                        :search="search"
                        :show-select="
                            can('partner_transaction_delete') && !printMode
                        "
                        loading-text="Loading partner transactions..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                        dense
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>
                        <!-- Amounts -->
                        <template
                            slot="item.payment.payment_date"
                            slot-scope="props"
                        >
                            <span>{{
                                formatDate(props.item.payment.payment_date)
                            }}</span>
                        </template>
                        <template slot="item.debit" slot-scope="props">
                            <span>{{ money(props.item.debit) }}</span>
                        </template>
                        <template slot="item.credit" slot-scope="props">
                            <span>{{ money(props.item.credit) }}</span>
                        </template>
                        <template slot="item.balance" slot-scope="props">
                            <span>{{ money(props.item.balance) }}</span>
                        </template>

                        <!-- Payments Columns Toggle -->
                        <template
                            v-if="paymentColumns"
                            slot="item.payment.cheque_type"
                            slot-scope="props"
                        >
                            {{ props.item.payment.cheque_type }}
                        </template>
                        <template
                            v-if="paymentColumns"
                            slot="item.payment.cheque_no"
                            slot-scope="props"
                        >
                            {{ props.item.payment.cheque_no }}
                        </template>
                        <template
                            v-if="paymentColumns"
                            slot="item.payment.cheque_due_date"
                            slot-scope="props"
                        >
                            {{ props.item.payment.cheque_due_date }}
                        </template>
                        <!-- Cheque Images (Button) -->
                        <template
                            v-if="
                                props.item.payment.cheque_images.length &&
                                paymentColumns
                            "
                            slot="item.payment.cheque_images"
                            slot-scope="props"
                        >
                            <v-btn
                                color="light"
                                x-small
                                title="Cheque Image(s)"
                                @click="
                                    setCurrentChequeImages(
                                        props.item.payment.cheque_images
                                    )
                                "
                            >
                                <v-icon>mdi-file-image-outline</v-icon>
                            </v-btn>
                        </template>
                        <!-- Description -->
                        <template
                            v-if="props.item.description"
                            slot="item.description"
                            slot-scope="props"
                        >
                            <small>{{ props.item.description }}</small>
                        </template>
                        <!-- Top Actions -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="blue-grey darken-3"
                                small
                                class="ma-2 text-right white--text"
                                @click="togglePaymentColumns"
                                v-if="can('partner_transaction_delete')"
                            >
                                <v-icon left>{{
                                    paymentColumns ? "mdi-eye-off" : "mdi-eye"
                                }}</v-icon>
                                {{ paymentColumns ? "Hide" : "Show" }} Payment
                                Columns
                            </v-btn>
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('partner_transaction_delete')"
                            >
                                <v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected
                            </v-btn>
                            <v-btn
                                color="success"
                                small
                                link
                                to="/partner_transactions/add"
                                class="ma-2 text-right"
                                v-if="can('partner_transaction_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Partner Transaction
                            </v-btn>
                            <v-text-field
                                v-model="search"
                                placeholder="Search"
                                class="mx-4"
                                append-icon="mdi-magnify"
                            ></v-text-field>
                        </template>
                        <!-- Actions -->
                        <template slot="item.actions" slot-scope="props">
                            <v-btn
                                x-small
                                text
                                color="primary"
                                :to="`/partner_transactions/edit/${props.item.id}`"
                                title="Edit"
                                v-if="can('partner_transaction_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setPartnerTransactionId(props.item.id)"
                                title="Delete"
                                v-if="can('partner_transaction_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Cheque Images Dialog -->
                    <v-dialog v-model="chequeImagesDialog" width="600">
                        <ChequeImages
                            :current-cheque-images="currentChequeImages"
                            @closeDialog="closeChequeImagesDialog"
                        />
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="partner_transactionId"
                        @confirmDeletion="
                            partner_transactionId
                                ? handlePartnerTransactionDelete()
                                : handleMultiplePartnerTransactionsDelete()
                        "
                    />
                </v-col>
            </v-row>

            <!-- Alert Component -->
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
import ChequeImages from "../globals/ChequeImages.vue";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],
    components: {
        EditPartnerTransaction,
        Navbar,
        Confirmation,
        ChequeImages,
        Excel,
        CSV,
        PDF,
    },
    data() {
        return {
            options: {},
            paymentColumns: false,
            currentChequeImages: null,
            chequeImagesDialog: false,
            selectedPartner: null,
            headers: [
                { text: "S#", value: "sno", show: true },
                { text: "Date", value: "payment.payment_date", show: false },
                { text: "Title", value: "title", show: true },
                { text: "Description", value: "description", show: true },
                { text: "Debit", value: "debit", show: true },
                { text: "Credit", value: "credit", show: true },
                { text: "Balance", value: "balance", show: true },
                {
                    text: "Payment Method",
                    value: "payment.payment_method",
                    show: false,
                },
                { text: "Bank", value: "payment.bank.name", show: false },
                {
                    text: "Cheque Type",
                    value: "payment.cheque_type",
                    show: false,
                },
                { text: "Cheque No.", value: "payment.cheque_no", show: false },
                {
                    text: "Cheque Due Date",
                    value: "payment.cheque_due_date",
                    show: false,
                },
                {
                    text: "Cheque Images",
                    value: "payment.cheque_images",
                    show: false,
                },
                { text: "Actions", value: "actions", align: "d-print-none" },
            ],
            selectedItems: [],
            partner_transactionId: null,
            currentPhoto: null,
        };
    },
    methods: {
        ...mapActions({
            getPartnerTransactions:
                "partner_transaction/getPartnerTransactions",
            searchPartnerTransactions:
                "partner_transaction/searchPartnerTransactions",
            getPartners: "partner/getPartners",
            deletePartnerTransaction:
                "partner_transaction/deletePartnerTransaction",
            deleteMultiplePartnerTransactions:
                "partner_transaction/deleteMultiplePartnerTransactions",
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

        togglePaymentColumns() {
            this.paymentColumns = !this.paymentColumns;
            this.headers.forEach((header) => {
                if (
                    [
                        "payment.payment_method",
                        "payment.bank.name",
                        "payment.cheque_type",
                        "payment.cheque_no",
                        "payment.cheque_due_date",
                        "payment.cheque_images",
                    ].includes(header.value)
                ) {
                    header.show = this.paymentColumns;
                }
            });
        },
        setCurrentChequeImages(images) {
            this.currentChequeImages = images;
            this.chequeImagesDialog = true;
        },
        closeChequeImagesDialog() {
            this.currentChequeImages = null;
            this.chequeImagesDialog = false;
        },
        setPartnerTransactionId(id) {
            this.partner_transactionId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },
        async handlePartnerTransactionDelete() {
            await this.deletePartnerTransaction(this.partner_transactionId);
            this.partner_transactionId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },
        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },
        async handleMultiplePartnerTransactionsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultiplePartnerTransactions(ids);
            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },

        getSelectedPartnerFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get("partner_id");
        },
    },
    computed: {
        ...mapGetters({
            partner_transactions: "partner_transaction/partner_transactions",
            loading: "partner_transaction/loading",
            total: "partner_transaction/total",
            partners: "partner/partners",
        }),

        displayedHeaders() {
            return this.headers.filter(
                (header) =>
                    header.show ||
                    ![
                        "payment.payment_method",
                        "payment.bank.name",
                        "payment.cheque_type",
                        "payment.cheque_no",
                        "payment.cheque_due_date",
                        "payment.cheque_images",
                    ].includes(header.value)
            );
        },
    },
    watch: {
        options: {
            handler() {
                this.getPartnerTransactions({
                    ...this.options,
                    partnerId: this.selectedPartner,
                });
            },
            deep: true,
        },

        search: {
            handler(newVal) {
                this.options.search = newVal;
                this.searchPartnerTransactions({
                    ...this.options,
                    partnerId: this.selectedPartner,
                });
            },
        },
    },

    async mounted() {
        // Fetch partners when component mounts
        await this.getPartners();

        this.selectedPartner =
            this.partners.find(
                (partner) =>
                    partner.id === parseInt(this.getSelectedPartnerFromUrl())
            )?.id || null;

        this.getPartnerTransactions({
            ...this.options,
            partnerId: this.selectedPartner,
        });

        // Remove actions if no access is given
        if (
            !this.can("partner_transaction_edit") &&
            !this.can("partner_transaction_delete")
        ) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
    },
};
</script>
