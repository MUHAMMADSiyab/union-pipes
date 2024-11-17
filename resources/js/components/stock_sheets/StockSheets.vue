<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Stock Sheets</h5>
            <v-data-table
                :headers="headers"
                :items="stock_sheets"
                class="elevation-1"
                item-key="id"
                :search="search"
                :items-per-page="perPage"
                :loading="loading"
                :show-select="can('stock_sheet_delete') && !printMode"
                loading-text="Loading stock_sheets..."
                :footer-props="footerProps"
                v-model="selectedItems"
            >
                <!-- S# -->
                <template slot="item.sno" slot-scope="props">{{
                    props.index + 1
                }}</template>

                <!-- Month -->
                <template slot="item.month" slot-scope="props">
                    <span>
                        {{
                            new Date(props.item.month).toLocaleDateString(
                                "en-US",
                                { month: "long", year: "numeric" }
                            )
                        }}
                    </span>
                </template>
                <template slot="item.entries_sum_quantity" slot-scope="props">
                    <span>{{ money(props.item.entries_sum_quantity) }}</span>
                </template>
                <template
                    slot="item.entries_sum_total_weight"
                    slot-scope="props"
                >
                    <span>{{
                        money(props.item.entries_sum_total_weight)
                    }}</span>
                </template>
                <template
                    slot="item.entries_sum_total_amount"
                    slot-scope="props"
                >
                    <span>{{
                        money(props.item.entries_sum_total_amount)
                    }}</span>
                </template>

                <!-- Top -->
                <template v-slot:top v-if="!printMode">
                    <v-btn
                        color="error"
                        small
                        :disabled="!selectedItems.length"
                        class="ma-2 text-right"
                        @click="deleteMultiple"
                        v-if="can('stock_sheet_delete')"
                        ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                        Selected</v-btn
                    >

                    <v-btn
                        color="success"
                        small
                        link
                        to="/stock_sheets/add"
                        class="ma-2 text-right"
                        v-if="can('stock_sheet_create')"
                    >
                        <v-icon left>mdi-stock_sheet-plus</v-icon>
                        New Stock Sheet Entry</v-btn
                    >

                    <Excel
                        module="stock_sheets"
                        :ids="selectedItems.map((item) => item.id)"
                    />
                    <CSV
                        module="stock_sheets"
                        :ids="selectedItems.map((item) => item.id)"
                    />
                    <PDF
                        module="stock_sheets"
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
                        color="indigo"
                        :to="`/stock_sheets/${props.item.id}`"
                        title="Stock Sheet Entries"
                        v-if="can('stock_sheet_show')"
                    >
                        <v-icon small>mdi-format-list-checkbox</v-icon>
                    </v-btn>
                    <v-btn
                        x-small
                        text
                        color="primary"
                        :to="`/stock_sheets/edit/${props.item.id}`"
                        title="Edit"
                        v-if="can('stock_sheet_edit')"
                    >
                        <v-icon small>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        x-small
                        text
                        color="red darken-2"
                        @click="setStockSheetId(props.item.id)"
                        title="Delete"
                        v-if="can('stock_sheet_delete')"
                    >
                        <v-icon small>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>

            <!-- Confirmation -->
            <Confirmation
                ref="confirmationComponent"
                :id="stockSheetId"
                @confirmDeletion="
                    stockSheetId
                        ? handleStockSheetDelete()
                        : handleMultipleStockSheetsDelete()
                "
            />
        </v-container>
        <alert />
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import Navbar from "../navs/Navbar";
import Confirmation from "../globals/Confirmation";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        Navbar,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            headers: [
                { text: "S#", value: "sno" },
                { text: "Month", value: "month" },
                {
                    text: "Total Quantity (Length)",
                    value: "entries_sum_quantity",
                },
                { text: "Total Weight", value: "entries_sum_total_weight" },
                { text: "Total Amount", value: "entries_sum_total_amount" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],

            stockSheetId: null,
        };
    },

    methods: {
        ...mapActions({
            getStockSheets: "stock_sheet/getStockSheets",
            getStockSheet: "stock_sheet/getStockSheet",
            deleteStockSheet: "stock_sheet/deleteStockSheet",
            deleteMultipleStockSheets: "stock_sheet/deleteMultipleStockSheets",
        }),

        setStockSheetId(id) {
            this.stockSheetId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleStockSheetDelete() {
            await this.deleteStockSheet(this.stockSheetId);
            this.stockSheetId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleStockSheetsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleStockSheets(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            stock_sheets: "stock_sheet/stock_sheets",
            stock_sheet: "stock_sheet/stock_sheet",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("stock_sheet_edit") && !this.can("stock_sheet_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }

        this.getStockSheets();
    },
};
</script>
