<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Transactions</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="displayedHeaders"
                        :items="transactions"
                        class="elevation-1"
                        item-key="id"
                        :search="search"
                        :items-per-page="perPage"
                        :loading="loading"
                        :show-select="can('transaction_delete') && !printMode"
                        loading-text="Loading transactions..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                        dense
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>

                        <!-- Amount -->
                        <template slot="item.amount" slot-scope="props">
                            <span> {{ money(props.item.amount) }} </span>
                        </template>

                        <!-- Cheque Columns Toggle -->
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_type"
                            slot-scope="props"
                            >{{ props.item.payment.cheque_type }}</template
                        >
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_no"
                            slot-scope="props"
                            >{{ props.item.payment.cheque_no }}</template
                        >
                        <template
                            v-if="showChequeColumns"
                            slot="item.payment.cheque_due_date"
                            slot-scope="props"
                            >{{ props.item.payment.cheque_due_date }}</template
                        >

                        <!-- Cheque Images (Button) -->
                        <template
                            slot="item.payment.cheque_images"
                            slot-scope="props"
                            v-if="
                                props.item.payment.cheque_images.length &&
                                showChequeColumns
                            "
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
                                ><v-icon>mdi-file-image-outline</v-icon></v-btn
                            >
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

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="blue-grey darken-3"
                                small
                                class="ma-2 text-right white--text"
                                @click="toggleChequeColumns"
                                v-if="can('transaction_delete')"
                                ><v-icon left>
                                    {{
                                        showChequeColumns
                                            ? "mdi-eye-off"
                                            : "mdi-eye"
                                    }}
                                </v-icon>
                                {{ showChequeColumns ? "Hide" : "Show" }} Cheque
                                Columns</v-btn
                            >

                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('transaction_delete')"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/transactions/add"
                                class="ma-2 text-right"
                                v-if="can('transaction_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Transaction</v-btn
                            >

                            <Excel
                                module="transactions"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <CSV
                                module="transactions"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <PDF
                                module="transactions"
                                :ids="selectedItems.map((item) => item.id)"
                            />

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
                                :to="`/transactions/edit/${props.item.id}`"
                                title="Edit"
                                v-if="can('transaction_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setTransactionId(props.item.id)"
                                title="Delete"
                                v-if="can('transaction_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Cheque Images -->
                    <v-dialog v-model="chequeImagesDialog" width="600">
                        <ChequeImages
                            :current-cheque-images="currentChequeImages"
                            @closeDialog="closeChequeImagesDialog"
                        />
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="transactionId"
                        @confirmDeletion="
                            transactionId
                                ? handleTransactionDelete()
                                : handleMultipleTransactionsDelete()
                        "
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
import EditTransaction from "./EditTransaction.vue";
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
        EditTransaction,
        Navbar,
        Confirmation,
        ChequeImages,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            showChequeColumns: false,
            currentChequeImages: null,
            chequeImagesDialog: false,
            headers: [
                { text: "S#", value: "sno", show: true },
                { text: "Type", value: "type", show: true },
                { text: "Title", value: "title", show: true },
                { text: "Amount", value: "amount", show: true },
                {
                    text: "Payment Method",
                    value: "payment.payment_method",
                    show: true,
                },
                { text: "Date", value: "payment.payment_date", show: true },
                { text: "Bank", value: "payment.bank.name", show: true },
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
                { text: "Description", value: "description", show: true },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            transactionId: null,
            currentPhoto: null,
        };
    },

    methods: {
        ...mapActions({
            getTransactions: "transaction/getTransactions",
            getTransaction: "transaction/getTransaction",
            deleteTransaction: "transaction/deleteTransaction",
            deleteMultipleTransactions:
                "transaction/deleteMultipleTransactions",
        }),

        toggleChequeColumns() {
            this.showChequeColumns = !this.showChequeColumns;

            this.headers.forEach((header) => {
                if (
                    [
                        "payment.cheque_type",
                        "payment.cheque_no",
                        "payment.cheque_due_date",
                        "payment.cheque_images",
                    ].includes(header.value)
                ) {
                    header.show = this.showChequeColumns;
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

        setTransactionId(id) {
            this.transactionId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleTransactionDelete() {
            await this.deleteTransaction(this.transactionId);
            this.transactionId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleTransactionsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleTransactions(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            transactions: "transaction/transactions",
            transaction: "transaction/transaction",
            loading: "loading",
        }),

        displayedHeaders() {
            return this.headers.filter(
                (header) =>
                    header.show ||
                    ![
                        "payment.cheque_type",
                        "payment.cheque_no",
                        "payment.cheque_due_date",
                        "payment.cheque_images",
                    ].includes(header.value)
            );
        },
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("transaction_edit") && !this.can("transaction_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
        this.getTransactions();
    },
};
</script>
