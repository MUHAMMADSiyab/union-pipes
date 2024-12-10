<template>
    <div>
        <v-card v-if="!editMode" elevation="1">
            <v-card-title primary-title>Stock History</v-card-title>

            <v-card-text class="mt-1">
                <v-data-table
                    :headers="headers"
                    :items="stocks"
                    class="elevation-1"
                    item-key="id"
                    :items-per-page="perPage"
                    loading-text="Loading Stocks..."
                    :footer-props="footerProps"
                    :show-select="!printMode"
                    v-model="selectedItems"
                >
                    <!-- Top -->
                    <template v-slot:top v-if="!printMode">
                        <Excel
                            module="stocks"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                        <CSV
                            module="stocks"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                        <PDF
                            module="stocks"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                    </template>

                    <!-- S# -->
                    <template slot="item.sno" slot-scope="props">{{
                        props.index + 1
                    }}</template>

                    <!-- Quantity -->
                    <template slot="item.quantity" slot-scope="props">
                        <span> {{ money(props.item.quantity) }} </span>
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
                                    @click="setEditMode(props.item.id)"
                                    title="Edit"
                                    v-if="can('stock_edit')"
                                >
                                    <v-list-item-title>Edit</v-list-item-title>
                                </v-list-item>

                                <v-list-item
                                    title="Delete"
                                    @click="
                                        setStockId(
                                            props.item.id,
                                            props.item.model
                                        )
                                    "
                                    v-if="can('stock_delete')"
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
                    color="secondary"
                    @click="closeDialog"
                    class="mt-3 d-print-none"
                    >Close</v-btn
                >

                <!-- Confirmation -->
                <Confirmation
                    ref="confirmationComponent"
                    :id="stockId"
                    @confirmDeletion="handleStockDelete()"
                />
            </v-card-text>
        </v-card>

        <!-- Edit Form -->
        <EditStock
            :stock="stock"
            @closeEditMode="closeEditMode"
            v-if="editMode"
        />
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../../mixins/DatatableMixin";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import Confirmation from "../../globals/Confirmation";
import EditStock from "./EditStock.vue";
import Excel from "../../globals/exports/Excel.vue";
import CSV from "../../globals/exports/CSV.vue";
import PDF from "../../globals/exports/PDF.vue";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    props: ["stockItemId"],

    components: {
        EditStock,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            stock_item_id: this.stockItemId,
            stockId: null,
            stockModel: null,
            selectedItems: [],
            editMode: false,
            headers: [
                { text: "S#", value: "sno" },
                { text: "Date", value: "date" },
                { text: "Length (Meter/Foot)", value: "length" },
                { text: "Weight", value: "quantity" },
                { text: "Description", value: "description" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
        };
    },

    methods: {
        ...mapActions({
            getStocks: "stock/getStocks",
            getStock: "stock/getStock",
            deleteStock: "stock/deleteStock",
        }),

        setStockId(id, model) {
            this.stockId = id;
            this.stockModel = model;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async setEditMode(stockId) {
            await this.getStock(stockId);
            this.editMode = true;
        },

        async handleStockDelete() {
            await this.deleteStock(this.stockId);

            this.stockId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        closeEditMode() {
            this.editMode = false;
        },

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    watch: {
        stockItemId: {
            handler(newStockItemId) {
                this.getStocks(newStockItemId);
            },
            deep: true,
        },
    },

    computed: {
        ...mapGetters({
            stocks: "stock/stocks",
            stock: "stock/stock",
        }),
    },

    mounted() {
        this.getStocks(this.stock_item_id);
    },
};
</script>
