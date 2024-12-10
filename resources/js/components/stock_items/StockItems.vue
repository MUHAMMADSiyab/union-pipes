<template>
    <div>
        <Navbar v-if="!printMode" />
        <print-button />

        <v-container class="mt-4">
            <div class="d-flex align-center justify-space-between mb-4">
                <h5 class="text-subtitle-1 mb-0">Stock Items</h5>
                <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search stock items..."
                    single-line
                    hide-details
                    outlined
                    dense
                    class="ml-4"
                    style="max-width: 300px"
                    clearable
                />
            </div>

            <v-data-table
                :headers="headers"
                :items="stock_items"
                :loading="loading"
                :search="search"
                class="elevation-1"
                :custom-filter="customFilter"
            >
                <!-- Weight and Length Column Templates -->
                <template v-slot:item.available_quantity="{ item }">
                    <v-chip color="indigo" label outlined small>
                        <strong>{{ money(item.available_quantity) }}</strong>
                    </v-chip>
                </template>

                <template v-slot:item.available_length="{ item }">
                    <v-chip color="indigo" label outlined small>
                        <strong> {{ money(item.available_length) }}</strong>
                    </v-chip>
                </template>

                <!-- Actions Column Template -->
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        x-small
                        color="success"
                        @click="showAddStockDialog(item.id)"
                        title="Add Stock"
                        v-if="can('stock_item_create')"
                        class="mr-1"
                    >
                        <v-icon small>mdi-plus</v-icon>
                    </v-btn>

                    <v-btn
                        x-small
                        color="info"
                        @click="showStocksDialog(item.id)"
                        title="View Stocks"
                    >
                        <v-icon small>mdi-clock-outline</v-icon>
                    </v-btn>

                    <v-btn
                        x-small
                        text
                        color="secondary"
                        :to="`/stock_items/edit/${item.id}`"
                        title="Edit"
                        v-if="can('stock_item_edit')"
                        class="mr-1"
                    >
                        <v-icon small>mdi-pencil</v-icon>
                    </v-btn>

                    <v-btn
                        x-small
                        text
                        color="red darken-2"
                        @click="setStockItemId(item.id)"
                        title="Delete"
                        v-if="can('stock_item_delete')"
                        class="mr-1"
                    >
                        <v-icon small>mdi-delete</v-icon>
                    </v-btn>
                </template>

                <!-- No Data Template -->
                <template v-slot:no-data>
                    <v-alert type="info" class="ma-2" outlined>
                        No stock items found.
                    </v-alert>
                </template>

                <!-- No Results Template -->
                <template v-slot:no-results>
                    <v-alert type="info" class="ma-2" outlined>
                        No results found for "{{ search }}".
                    </v-alert>
                </template>
            </v-data-table>

            <!-- Add Stock Dialog -->
            <v-dialog v-model="addStockDialog" max-width="600" persistent>
                <AddStock
                    :stock-item-id="currentStockItemId"
                    @closeDialog="closeAddStockDialog"
                />
            </v-dialog>

            <!-- Stocks Dialog -->
            <v-dialog v-model="stocksDialog" max-width="800" persistent>
                <Stocks
                    :stock-item-id="currentStockItemId"
                    @closeDialog="closeStocksDialog"
                />
            </v-dialog>

            <!-- Confirmation Dialog -->
            <Confirmation
                ref="confirmationComponent"
                :id="stockItemId"
                @confirmDeletion="
                    stockItemId
                        ? handleStockItemDelete()
                        : handleMultipleStockItemsDelete()
                "
            />

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditStockItem from "./EditStockItem.vue";
import AddStock from "./partial/AddStock.vue";
import Stocks from "./partial/Stocks.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [CurrencyMixin],

    components: {
        EditStockItem,
        Navbar,
        Confirmation,
        AddStock,
        Stocks,
    },

    data() {
        return {
            search: "",
            addStockDialog: false,
            stocksDialog: false,
            stockItemId: null,
            currentStockItemId: null,
            headers: [
                {
                    text: "Name",
                    align: "start",
                    value: "name",
                },
                {
                    text: "Description",
                    align: "start",
                    value: "description",
                },
                {
                    text: "Available Weight",
                    align: "center",
                    value: "available_quantity",
                },
                {
                    text: "Available Length (Meter/Foot)",
                    align: "center",
                    value: "available_length",
                },
                {
                    text: "Actions",
                    align: "center",
                    value: "actions",
                    sortable: false,
                    filterable: false,
                },
            ],
        };
    },

    methods: {
        ...mapActions({
            getStockItems: "stock_item/getStockItems",
            getStockItem: "stock_item/getStockItem",
            getStocks: "stock/getStocks",
            deleteStockItem: "stock_item/deleteStockItem",
            deleteMultipleStockItems: "stock_item/deleteMultipleStockItems",
        }),

        setStockItemId(id) {
            this.stockItemId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        showAddStockDialog(currentStockItemId) {
            this.currentStockItemId = currentStockItemId;
            this.addStockDialog = true;
        },

        closeAddStockDialog() {
            this.currentStockItemId = null;
            this.addStockDialog = false;
        },

        showStocksDialog(stockItemId) {
            this.currentStockItemId = stockItemId;
            this.stocksDialog = true;
        },

        closeStocksDialog() {
            this.stocksDialog = false;
            this.currentStockItemId = null;
        },

        async handleStockItemDelete() {
            await this.deleteStockItem(this.stockItemId);
            this.stockItemId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleStockItemsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleStockItems(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },

        customFilter(value, search, item) {
            if (!search) return true;

            const searchText = search.toString().toLowerCase();
            const itemName = item.name.toString().toLowerCase();
            const itemDescription = (item.description || "")
                .toString()
                .toLowerCase();

            return (
                itemName.includes(searchText) ||
                itemDescription.includes(searchText) ||
                this.money(item.available_quantity).includes(searchText) ||
                this.money(item.available_length).includes(searchText)
            );
        },
    },

    computed: {
        ...mapGetters({
            stock_items: "stock_item/stock_items",
            stock_item: "stock_item/stock_item",
            loading: "loading",
        }),
    },

    mounted() {
        this.getStockItems();
    },
};
</script>
