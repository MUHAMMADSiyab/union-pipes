<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Stock Items</h5>

            <v-row>
                <v-col
                    xl="12"
                    lg="12"
                    md="12"
                    sm="12"
                    cols="12"
                    v-for="(stock_item, i) in stock_items"
                    :key="i"
                >
                    <v-card class="mx-auto" ripple>
                        <v-card-title primary-title>
                            {{ stock_item.name }}
                        </v-card-title>

                        <v-card-subtitle>
                            {{ stock_item.description }}
                        </v-card-subtitle>

                        <v-card-text>
                            <h3>
                                Available Quantity (Weight)
                                <v-chip
                                    pill
                                    color="purple"
                                    class="white--text"
                                    >{{
                                        money(stock_item.available_quantity)
                                    }}</v-chip
                                >
                            </h3>
                        </v-card-text>

                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-btn
                                x-small
                                text
                                color="secondary"
                                :to="`/stock_items/edit/${stock_item.id}`"
                                title="Edit"
                                v-if="can('stock_item_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setStockItemId(stock_item.id)"
                                title="Delete"
                                v-if="can('stock_item_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>

                            <v-btn
                                small
                                color="info"
                                @click="showAddProductionDialog(stock_item.id)"
                                title="Add Production"
                            >
                                Add Stock
                            </v-btn>

                            <v-btn
                                small
                                color="info"
                                @click="showProductionsDialog(stock_item.id)"
                                title="View Productions"
                            >
                                View Stock History
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Add Production -->
            <v-dialog v-model="addProductionDialog" max-width="600" persistent>
                <AddProduction
                    :stock-item-id="currentStockItemId"
                    @closeDialog="closeAddProductionDialog"
                />
            </v-dialog>

            <!-- Productions -->
            <v-dialog v-model="productionsDialog" max-width="800" persistent>
                <Productions
                    :stock-item-id="currentStockItemId"
                    @closeDialog="closeProductionsDialog"
                />
            </v-dialog>

            <!-- Confirmation -->
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
import AddProduction from "./partial/AddProduction.vue";
import Productions from "./partial/Productions.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [CurrencyMixin],

    components: {
        EditStockItem,
        Navbar,
        Confirmation,
        AddProduction,
        Productions,
    },

    data() {
        return {
            addProductionDialog: false,
            productionsDialog: false,
            stockItemId: null,
            currentStockItemId: null,
        };
    },

    methods: {
        ...mapActions({
            getStockItems: "stock_item/getStockItems",
            getStockItem: "stock_item/getStockItem",
            getProductions: "production/getProductions",
            deleteStockItem: "stock_item/deleteStockItem",
            deleteMultipleStockItems: "stock_item/deleteMultipleStockItems",
        }),

        setStockItemId(id) {
            this.stockItemId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        showAddProductionDialog(currentStockItemId) {
            this.currentStockItemId = currentStockItemId;
            this.addProductionDialog = true;
        },

        closeAddProductionDialog() {
            this.currentStockItemId = null;
            this.addProductionDialog = false;
        },

        showProductionsDialog(stockItemId) {
            this.currentStockItemId = stockItemId;
            this.productionsDialog = true;
        },

        closeProductionsDialog() {
            this.productionsDialog = false;
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
