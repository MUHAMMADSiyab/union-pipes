<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Sells</h5>

            <v-row>
                <v-col cols="12">
                    <v-switch
                        color="red"
                        v-model="local"
                        label="Local Customers"
                        @change="handleLocalSwitch"
                    ></v-switch>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="sells"
                        class="elevation-1"
                        item-key="id"
                        :items-per-page="perPage"
                        :search="search"
                        :loading="loading"
                        :options.sync="options"
                        :server-items-length="total"
                        :show-select="can('sell_delete') && !printMode"
                        loading-text="Loading sells..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                        dense
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>

                        <template slot="item.customer.name" slot-scope="props">
                            <router-link
                                class="text-decoration-none"
                                :to="`/customers/${props.item.customer.id}/ledger_entries`"
                            >
                                {{ props.item.customer.name }}
                            </router-link>
                        </template>

                        <!-- Amounts -->
                        <template
                            slot="item.discounted_total_amount"
                            slot-scope="props"
                        >
                            <span>
                                {{ money(props.item.discounted_total_amount) }}
                            </span>
                            <small
                                class="d-block purple--text"
                                v-if="props.item.discount > 0"
                                >({{ props.item.discount }}% discount applied on
                                {{ money(props.item.total_amount) }})</small
                            >
                        </template>

                        <template slot="item.paid" slot-scope="props">
                            <span>
                                {{ money(props.item.discounted_paid) }}
                                <small
                                    v-if="props.item.discounts > 0"
                                    class="d-block purple--text"
                                >
                                    ({{ props.item.discounts }}% Discount Added)
                                </small>
                            </span>
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

                        <!-- Sold Items (Button) -->
                        <template slot="item.sold_items" slot-scope="props">
                            <v-btn
                                :color="`${
                                    props.item.returned_items.length
                                        ? 'orange'
                                        : 'light'
                                }`"
                                class="d-print-none"
                                v-if="props.item.sold_items.length"
                                x-small
                                :title="`Sold Items ${
                                    props.item.returned_items.length
                                        ? '(Some items have been returned, dont forget to adjust the payment amounts accordingly)'
                                        : ''
                                }`"
                                @click="
                                    setCurrentSoldItems(props.item.sold_items)
                                "
                            >
                                View
                                <v-icon
                                    v-if="props.item.returned_items.length"
                                    right
                                    small
                                    >mdi-keyboard-return</v-icon
                                >
                            </v-btn>
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
                                            setCurrentSoldItemsForReturn(
                                                props.item.sold_items
                                            )
                                        "
                                        title="Sell Return"
                                        v-if="
                                            can('sell_create') &&
                                            props.item.sold_items.length
                                        "
                                    >
                                        <v-list-item-title
                                            >Return Items</v-list-item-title
                                        >
                                    </v-list-item>

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
                                        :to="`/sells/edit/${props.item.id}`"
                                        title="Edit"
                                        v-if="can('sell_edit')"
                                    >
                                        <v-list-item-title
                                            >Edit</v-list-item-title
                                        >
                                    </v-list-item>
                                    <v-list-item
                                        link
                                        :to="`/sells/${props.item.id}`"
                                        title="Details"
                                        v-if="can('sell_show')"
                                    >
                                        <v-list-item-title
                                            >Details</v-list-item-title
                                        >
                                    </v-list-item>
                                    <v-list-item
                                        @click="setSellId(props.item.id)"
                                        title="Delete"
                                        v-if="can('sell_delete')"
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
                                to="/sells/add"
                                class="ma-2 text-right"
                                v-if="can('sell_create')"
                            >
                                <v-icon left>mdi-plus-thick</v-icon>
                                New Sell</v-btn
                            >

                            <!-- Export -->
                            <Excel
                                module="sells"
                                :ids="selectedItems.map((item) => item.id)"
                                :local="local"
                            />
                            <CSV
                                module="sells"
                                :ids="selectedItems.map((item) => item.id)"
                                :local="local"
                            />
                            <PDF
                                module="sells"
                                :ids="selectedItems.map((item) => item.id)"
                                :local="local"
                            />

                            <v-row>
                                <v-col>
                                    <v-text-field
                                        v-model="search"
                                        placeholder="Search"
                                        class="mx-4"
                                        append-icon="mdi-magnify"
                                    ></v-text-field>
                                </v-col>
                                <v-col>
                                    <input type="date" v-model="date" />
                                </v-col>
                            </v-row>
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
                            :entry="currentSell"
                            :entry-data="{
                                model: 'App\\Models\\Sell',
                                transaction_type: 'Debit',
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
                            entry-type="sell"
                            :entry="currentSell"
                            :payment-setting="paymentSetting"
                        />
                    </v-dialog>

                    <!-- Sold Items -->
                    <v-dialog v-model="soldItemsDialog" width="800">
                        <SoldItems
                            :sold-items="currentSoldItems"
                            :dialog="true"
                            @closeDialog="closeSoldItemsDialog"
                        />
                    </v-dialog>

                    <!-- Sold Items For Return -->
                    <v-dialog v-model="soldItemsForReturnDialog" width="1200">
                        <SoldItemsForReturn
                            :sold-items="currentSoldItemsForReturn"
                            :dialog="true"
                            @closeDialog="closeSoldItemsForReturnDialog"
                        />
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="sellId"
                        @confirmDeletion="
                            sellId
                                ? handleSellDelete()
                                : handleMultipleSellsDelete()
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
import EditSell from "./EditSell.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import SoldItems from "./partial/SoldItems.vue";
import SoldItemsForReturn from "./partial/SoldItemsForReturn.vue";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import AddPayment from "../globals/payments/AddPayment.vue";
import Payments from "../globals/payments/Payments.vue";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        EditSell,
        Navbar,
        Confirmation,
        SoldItems,
        SoldItemsForReturn,
        AddPayment,
        Payments,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            local: false,
            date: "",
            currentSoldItems: null,
            options: {},
            currentSoldItemsForReturn: null,
            soldItemsDialog: false,
            soldItemsForReturnDialog: false,
            headers: [
                { text: "#", value: "sno" },
                { text: "Date", value: "date" },
                { text: "Invoice #", value: "invoice_no" },
                { text: "Category", value: "category" },
                { text: "Customer", value: "customer.name" },
                {
                    text: "Sold Items",
                    value: "sold_items",
                    align: " d-print-none",
                },
                {
                    text: "Discount %",
                    value: "discount",
                    align: " d-print-none",
                },
                { text: "Total Amount", value: "discounted_total_amount" },
                { text: "Paid Amount", value: "paid" },
                { text: "Balance", value: "balance" },
                { text: "Status", value: "status" },
                { text: "🚀", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            sellId: null,
            addPaymentDialog: false,
            paymentsDialog: false,
            currentSell: null,
            currentPhoto: null,
        };
    },

    methods: {
        ...mapActions({
            getSells: "sell/getSells",
            searchSells: "sell/searchSells",
            getSell: "sell/getSell",
            deleteSell: "sell/deleteSell",
            deleteMultipleSells: "sell/deleteMultipleSells",
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

        handleLocalSwitch(local) {
            this.options.local = local;
            if (this.options.search || this.options.date) {
                this.searchSells(this.options);
            } else {
                this.getSells(this.options);
            }
        },

        setCurrentSoldItems(items) {
            this.currentSoldItems = items;
            this.soldItemsDialog = true;
        },

        closeSoldItemsDialog() {
            this.currentSoldItems = null;
            this.soldItemsDialog = false;
        },

        setCurrentSoldItemsForReturn(items) {
            this.currentSoldItemsForReturn = items;
            this.soldItemsForReturnDialog = true;
        },

        closeSoldItemsForReturnDialog() {
            this.currentSoldItemsForReturn = null;
            this.soldItemsForReturnDialog = false;
        },

        setSellId(id) {
            this.sellId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        showAddPaymentDialog(currentSell) {
            this.currentSell = currentSell;
            this.addPaymentDialog = true;
        },

        closeAddPaymentDialog() {
            this.currentSell = null;
            this.addPaymentDialog = false;
        },

        async showPaymentsDialog(currentSell) {
            this.currentSell = currentSell;

            await this.getPayments({
                model: "App\\Models\\Sell",
                paymentable_id: currentSell.id,
            });

            this.paymentsDialog = true;
        },

        closePaymentsDialog() {
            this.paymentsDialog = false;
        },

        async handleSellDelete() {
            await this.deleteSell(this.sellId);
            this.sellId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleSellsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleSells(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            sells: "sell/sells",
            sell: "sell/sell",
            paymentSetting: "paymentSetting",
            payments: "payment/payments",
            loading: "sell/loading",
            total: "sell/total",
        }),
    },

    watch: {
        options: {
            handler() {
                this.options.local = this.local;
                this.getSells(this.options);
            },
            deep: true,
        },

        search: {
            handler(newVal) {
                this.options.search = newVal;
                this.options.local = this.local;
                this.searchSells(this.options);
            },
        },

        date: {
            handler(newVal) {
                this.options.date = newVal;
                this.options.local = this.local;
                this.searchSells(this.options);
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
