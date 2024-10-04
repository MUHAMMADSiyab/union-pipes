<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Bulk Payments</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="displayedHeaders"
                        :items="bulk_payments"
                        class="elevation-1"
                        item-key="id"
                        :loading="loading"
                        :options.sync="options"
                        :server-items-length="total"
                        :search="search"
                        loading-text="Loading bulk_payments..."
                        :footer-props="footerProps"
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
                            slot="item.cheque_type"
                            slot-scope="props"
                            >{{ props.item.cheque_type }}</template
                        >
                        <template
                            v-if="showChequeColumns"
                            slot="item.cheque_no"
                            slot-scope="props"
                            >{{ props.item.cheque_no }}</template
                        >
                        <template
                            v-if="showChequeColumns"
                            slot="item.cheque_due_date"
                            slot-scope="props"
                            >{{ props.item.cheque_due_date }}</template
                        >

                        <!-- Cheque Images (Button) -->
                        <template
                            slot="item.cheque_images"
                            slot-scope="props"
                            v-if="
                                props.item.cheque_images.length &&
                                showChequeColumns
                            "
                        >
                            <v-btn
                                color="light"
                                x-small
                                title="Cheque Image(s)"
                                @click="
                                    setCurrentChequeImages(
                                        props.item.cheque_images
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
                                v-if="can('bulk_payment_delete')"
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
                                color="success"
                                small
                                link
                                to="/bulk_payments/add"
                                class="ma-2 text-right"
                                v-if="can('bulk_payment_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New BulkPayment</v-btn
                            >

                            <!-- <Excel
                                module="bulk_payments"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <CSV
                                module="bulk_payments"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <PDF
                                module="bulk_payments"
                                :ids="selectedItems.map((item) => item.id)"
                            /> -->

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
                                :to="`/bulk_payments/${props.item.id}`"
                                color="grey-lighten-1"
                                title="Bulk Payment Details"
                                v-if="can('bulk_payment_show')"
                            >
                                <v-icon small>mdi-view-agenda</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setBulkPaymentId(props.item.id)"
                                title="Delete"
                                v-if="can('bulk_payment_delete')"
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
                        :id="bulk_paymentId"
                        @confirmDeletion="handleBulkPaymentDelete()"
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
            showChequeColumns: false,
            currentChequeImages: null,
            chequeImagesDialog: false,
            headers: [
                { text: "S#", value: "sno", show: true },
                { text: "Customer", value: "customer.name", show: true },
                { text: "Amount", value: "amount", show: true },
                {
                    text: "Payment Method",
                    value: "payment_method",
                    show: true,
                },
                { text: "Date", value: "date", show: true },
                { text: "Bank", value: "bank.name", show: true },
                {
                    text: "Cheque Type",
                    value: "cheque_type",
                    show: false,
                },
                { text: "Cheque No.", value: "cheque_no", show: false },
                {
                    text: "Cheque Due Date",
                    value: "cheque_due_date",
                    show: false,
                },
                {
                    text: "Cheque Images",
                    value: "cheque_images",
                    show: false,
                },
                { text: "Description", value: "description", show: true },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            bulk_paymentId: null,
            currentPhoto: null,
        };
    },

    methods: {
        ...mapActions({
            getBulkPayments: "bulk_payment/getBulkPayments",
            // searchBulkPayments: "bulk_payment/searchBulkPayments",
            getBulkPayment: "bulk_payment/getBulkPayment",
            deleteBulkPayment: "bulk_payment/deleteBulkPayment",
        }),

        toggleChequeColumns() {
            this.showChequeColumns = !this.showChequeColumns;

            this.headers.forEach((header) => {
                if (
                    [
                        "cheque_type",
                        "cheque_no",
                        "cheque_due_date",
                        "cheque_images",
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

        setBulkPaymentId(id) {
            this.bulk_paymentId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleBulkPaymentDelete() {
            await this.deleteBulkPayment(this.bulk_paymentId);
            this.bulk_paymentId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleBulkPaymentsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleBulkPayments(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            bulk_payments: "bulk_payment/bulk_payments",
            bulk_payment: "bulk_payment/bulk_payment",
            loading: "bulk_payment/loading",
            total: "bulk_payment/total",
        }),

        displayedHeaders() {
            return this.headers.filter(
                (header) =>
                    header.show ||
                    ![
                        "cheque_type",
                        "cheque_no",
                        "cheque_due_date",
                        "cheque_images",
                    ].includes(header.value)
            );
        },
    },

    watch: {
        options: {
            handler() {
                this.getBulkPayments(this.options);
            },
            deep: true,
        },

        search: {
            handler(newVal) {
                this.options.search = newVal;
                this.getBulkPayments(this.options);
            },
        },
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("bulk_payment_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
    },
};
</script>
