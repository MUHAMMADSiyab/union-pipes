<template>
    <div>
        <v-card v-if="!editMode" elevation="1">
            <v-card-title primary-title>Stock History</v-card-title>

            <v-card-text class="mt-1">
                <v-data-table
                    :headers="headers"
                    :items="productions"
                    class="elevation-1"
                    item-key="id"
                    :items-per-page="perPage"
                    loading-text="Loading Productions..."
                    :footer-props="footerProps"
                    :show-select="!printMode"
                    v-model="selectedItems"
                >
                    <!-- Top -->
                    <template v-slot:top v-if="!printMode">
                        <Excel
                            module="productions"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                        <CSV
                            module="productions"
                            :ids="selectedItems.map((item) => item.id)"
                        />
                        <PDF
                            module="productions"
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
                                    v-if="can('production_edit')"
                                >
                                    <v-list-item-title>Edit</v-list-item-title>
                                </v-list-item>

                                <v-list-item
                                    title="Delete"
                                    @click="
                                        setProductionId(
                                            props.item.id,
                                            props.item.model
                                        )
                                    "
                                    v-if="can('production_delete')"
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
                    :id="productionId"
                    @confirmDeletion="handleProductionDelete()"
                />
            </v-card-text>
        </v-card>

        <!-- Edit Form -->
        <EditProduction
            :production="production"
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
import EditProduction from "./EditProduction.vue";
import Excel from "../../globals/exports/Excel.vue";
import CSV from "../../globals/exports/CSV.vue";
import PDF from "../../globals/exports/PDF.vue";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    props: ["stockItemId"],

    components: {
        EditProduction,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            stock_item_id: this.stockItemId,
            productionId: null,
            productionModel: null,
            selectedItems: [],
            editMode: false,
            headers: [
                { text: "S#", value: "sno" },
                { text: "Date", value: "date" },
                { text: "Weight", value: "quantity" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
        };
    },

    methods: {
        ...mapActions({
            getProductions: "production/getProductions",
            getProduction: "production/getProduction",
            deleteProduction: "production/deleteProduction",
        }),

        setProductionId(id, model) {
            this.productionId = id;
            this.productionModel = model;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async setEditMode(productionId) {
            await this.getProduction(productionId);
            this.editMode = true;
        },

        async handleProductionDelete() {
            await this.deleteProduction(this.productionId);

            this.productionId = null;
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
                this.stock_item_id = newStockItemId;
            },
            deep: true,
        },
    },

    computed: {
        ...mapGetters({
            productions: "production/productions",
            production: "production/production",
        }),
    },

    mounted() {
        this.getProductions(this.stock_item_id);
    },
};
</script>
