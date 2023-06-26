<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Purchases</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="purchases"
                        class="elevation-1"
                        item-key="id"
                        :search="search"
                        :loading="loading"
                        :options.sync="options"
                        :server-items-length="total"
                        :show-select="can('purchase_delete') && !printMode"
                        loading-text="Loading purchases..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                        dense
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            (options.page - 1) * options.itemsPerPage +
                            props.index +
                            1
                        }}</template>

                        <template slot="item.company.name" slot-scope="props">
                            <router-link
                                class="text-decoration-none"
                                :to="`/companies/${props.item.company.id}/ledger_entries`"
                            >
                                {{ props.item.company.name }}
                            </router-link>
                        </template>

                        <!-- Amounts -->
                        <template slot="item.total_amount" slot-scope="props">
                            <span> {{ money(props.item.total_amount) }} </span>
                        </template>

                        <template slot="item.paid" slot-scope="props">
                            <span> {{ money(props.item.paid) }} </span>
                        </template>

                        <template slot="item.balance" slot-scope="props">
                            <span> {{ money(props.item.balance) }} </span>
                        </template>

                        <!-- Status -->
                        <template slot="item.status" slot-scope="props">
                            <v-chip
                                :color="getStatusType(props.item.status)"
                                x-small
                            >
                                {{ props.item.status }}
                            </v-chip>
                        </template>

                        <!-- Purchased Items (Button) -->
                        <template
                            slot="item.purchased_items"
                            slot-scope="props"
                        >
                            <v-btn
                                color="light"
                                class="d-print-none"
                                x-small
                                title="Purchased Items"
                                v-if="props.item.purchased_items.length"
                                @click="
                                    setCurrentPurchasedItems(
                                        props.item.purchased_items
                                    )
                                "
                                >View</v-btn
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
                                        @click="
                                            showAddPaymentDialog({
                                                id: props.item.id,
                                                balance: props.item.balance,
                                            })
                                        "
                                        title="Add Payment"
                                        v-if="can('payment_create')"
                                    >
                                        <v-list-item-title
                                            >Add Payment</v-list-item-title
                                        >
                                    </v-list-item>

                                    <v-list-item
                                        @click="
                                            showPaymentsDialog({
                                                id: props.item.id,
                                            })
                                        "
                                        title="View Payments"
                                        v-if="can('payment_access')"
                                    >
                                        <v-list-item-title
                                            >Payments</v-list-item-title
                                        >
                                    </v-list-item>

                                    <v-list-item
                                        link
                                        :to="`/purchases/edit/${props.item.id}`"
                                        title="Edit"
                                        v-if="can('purchase_edit')"
                                    >
                                        <v-list-item-title
                                            >Edit</v-list-item-title
                                        >
                                    </v-list-item>
                                    <v-list-item
                                        link
                                        :to="`/purchases/${props.item.id}`"
                                        title="Details"
                                        v-if="can('purchase_show')"
                                    >
                                        <v-list-item-title
                                            >Details</v-list-item-title
                                        >
                                    </v-list-item>
                                    <v-list-item
                                        @click="setPurchaseId(props.item.id)"
                                        title="Delete"
                                        v-if="can('purchase_delete')"
                                    >
                                        <v-list-item-title
                                            >Delete</v-list-item-title
                                        >
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </template>

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/purchases/add"
                                class="ma-2 text-right"
                                v-if="can('purchase_create')"
                            >
                                <v-icon left>mdi-plus-thick</v-icon>
                                New Purchase</v-btn
                            >

                            <!-- Export -->
                            <Excel
                                module="purchases"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <CSV
                                module="purchases"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <PDF
                                module="purchases"
                                :ids="selectedItems.map((item) => item.id)"
                            />

                            <v-text-field
                                v-model="search"
                                placeholder="Search"
                                class="mx-4"
                                append-icon="mdi-magnify"
                            ></v-text-field>
                        </template>
                    </v-data-table>

                    <!-- Add payment dialog -->
                    <v-dialog
                        v-model="addPaymentDialog"
                        max-width="600"
                        persistent
                    >
                        <AddPayment
                            @closeDialog="closeAddPaymentDialog"
                            :entry="currentPurchase"
                            :entry-data="{
                                model: 'App\\Models\\Purchase',
                                transaction_type: 'Credit',
                            }"
                            :payment-setting="paymentSetting"
                        />
                    </v-dialog>

                    <!-- Payments dialog -->
                    <v-dialog
                        v-model="paymentsDialog"
                        max-width="1000"
                        persistent
                    >
                        <Payments
                            @closeDialog="closePaymentsDialog"
                            :payments="payments"
                            entry-type="purchase"
                            :entry="currentPurchase"
                            :payment-setting="paymentSetting"
                        />
                    </v-dialog>

                    <!-- Purchased Items -->
                    <v-dialog v-model="purchasedItemsDialog" width="800">
                        <PurchasedItems
                            :purchased-items="currentPurchasedItems"
                            :dialog="true"
                            @closeDialog="closePurchasedItemsDialog"
                        />
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="purchaseId"
                        @confirmDeletion="
                            purchaseId
                                ? handlePurchaseDelete()
                                : handleMultiplePurchasesDelete()
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
import EditPurchase from "./EditPurchase.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import PurchasedItems from "./partial/PurchasedItems.vue";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import AddPayment from "../globals/payments/AddPayment.vue";
import Payments from "../globals/payments/Payments.vue";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        EditPurchase,
        Navbar,
        Confirmation,
        PurchasedItems,
        AddPayment,
        Payments,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            currentPurchasedItems: null,
            purchasedItemsDialog: false,
            options: {},
            headers: [
                { text: "#", value: "sno" },
                { text: "Date", value: "date" },
                { text: "Invoice #", value: "invoice_no" },
                { text: "Category", value: "category" },
                { text: "Company", value: "company.name" },
                {
                    text: "Purchased Items",
                    value: "purchased_items",
                    align: " d-print-none",
                },
                {
                    text: "Sales Tax %",
                    value: "sales_tax_percentage",
                    align: " d-print-none",
                },
                { text: "Total Amount", value: "total_amount" },
                { text: "Paid Amount", value: "paid" },
                { text: "Balance", value: "balance" },
                { text: "Status", value: "status" },
                { text: "🚀", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            purchaseId: null,
            addPaymentDialog: false,
            paymentsDialog: false,
            currentPurchase: null,
            currentPhoto: null,
        };
    },

    methods: {
        ...mapActions({
            getPurchases: "purchase/getPurchases",
            searchPurchases: "purchase/searchPurchases",
            getPurchase: "purchase/getPurchase",
            deletePurchase: "purchase/deletePurchase",
            deleteMultiplePurchases: "purchase/deleteMultiplePurchases",
            getPayments: "payment/getPayments",
            getPaymentSetting: "getPaymentSetting",
        }),

        getStatusType(status) {
            switch (status) {
                case "Partial":
                    return "warning darken-2";

                case "Unpaid":
                    return "error";

                case "Paid":
                    return "success";

                case "Advance":
                    return "purple white--text";
            }
        },

        setCurrentPurchasedItems(items) {
            this.currentPurchasedItems = items;
            this.purchasedItemsDialog = true;
        },

        closePurchasedItemsDialog() {
            this.currentPurchasedItems = null;
            this.purchasedItemsDialog = false;
        },

        setPurchaseId(id) {
            this.purchaseId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        showAddPaymentDialog(currentPurchase) {
            this.currentPurchase = currentPurchase;
            this.addPaymentDialog = true;
        },

        closeAddPaymentDialog() {
            this.currentPurchase = null;
            this.addPaymentDialog = false;
        },

        async showPaymentsDialog(currentPurchase) {
            this.currentPurchase = currentPurchase;

            await this.getPayments({
                model: "App\\Models\\Purchase",
                paymentable_id: currentPurchase.id,
            });

            this.paymentsDialog = true;
        },

        closePaymentsDialog() {
            this.paymentsDialog = false;
        },

        async handlePurchaseDelete() {
            await this.deletePurchase(this.purchaseId);
            this.purchaseId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultiplePurchasesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultiplePurchases(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            purchases: "purchase/purchases",
            purchase: "purchase/purchase",
            paymentSetting: "paymentSetting",
            payments: "payment/payments",
            loading: "purchase/loading",
            total: "purchase/total",
        }),
    },

    watch: {
        options: {
            handler() {
                this.getPurchases(this.options);
            },
            deep: true,
        },

        search: {
            handler(newVal) {
                this.options.search = newVal;
                this.searchPurchases(this.options);
            },
        },
    },

    mounted() {
        this.getPaymentSetting();
    },
};
</script>

<style scoped>
table.v-table tbody td:first-child,
table.v-table tbody td:not(:first-child),
table.v-table tbody th:first-child,
table.v-table tbody th:not(:first-child),
table.v-table thead td:first-child,
table.v-table thead td:not(:first-child),
table.v-table thead th:first-child,
table.v-table thead th:not(:first-child) {
    padding: 0 10px;
}
</style>
