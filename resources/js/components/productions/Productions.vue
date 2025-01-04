<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Machines Productions</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="productions"
                        :loading="loading"
                        :options.sync="options"
                        :server-items-length="total"
                        :search="search"
                        :footer-props="footerProps"
                        item-key="id"
                        class="elevation-1"
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            (options.page - 1) * options.itemsPerPage +
                            props.index +
                            1
                        }}</template>

                        <!-- Weight -->
                        <template
                            slot="item.weight"
                            slot-scope="props"
                            v-if="props.item.weight"
                        >
                            {{ money(props.item.weight) }}
                        </template>

                        <template
                            slot="item.quantity"
                            slot-scope="props"
                            v-if="props.item.quantity"
                        >
                            {{ money(props.item.quantity) }}
                        </template>

                        <template
                            slot="item.total_weight"
                            slot-scope="props"
                            v-if="props.item.total_weight"
                        >
                            {{ money(props.item.total_weight) }}
                        </template>

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('production_delete')"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/productions/add"
                                class="ma-2 text-right"
                                v-if="can('production_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Production</v-btn
                            >

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
                                :to="`/productions/edit/${props.item.id}`"
                                title="Edit"
                                v-if="can('production_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setProductionId(props.item.id)"
                                title="Delete"
                                v-if="can('production_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="productionId"
                        @confirmDeletion="
                            productionId
                                ? handleProductionDelete()
                                : handleMultipleProductionsDelete()
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
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
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
            options: {},
            headers: [
                { text: "S#", value: "sno" },
                { text: "Machine", value: "machine.name" },
                { text: "Operator", value: "employee.name" },
                { text: "Product", value: "product.product_full_name" },
                { text: "Date", value: "date" },
                { text: "Shift", value: "shift" },
                { text: "Weight", value: "weight" },
                { text: "Quantity", value: "quantity" },
                { text: "Total Weight", value: "total_weight" },
                { text: "Description", value: "description" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            productionId: null,
        };
    },

    methods: {
        ...mapActions({
            getProductions: "production/getProductions",
            searchProductions: "production/searchProductions",
            getProduction: "production/getProduction",
            deleteProduction: "production/deleteProduction",
            deleteMultipleProductions: "production/deleteMultipleProductions",
        }),

        customFilter(val) {
            console.log(val);
        },

        setProductionId(id) {
            this.productionId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleProductionDelete() {
            await this.deleteProduction(this.productionId);
            this.productionId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleProductionsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleProductions(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            productions: "production/productions",
            loading: "production/loading",
            total: "production/total",
            production: "production/production",
        }),
    },

    watch: {
        options: {
            handler() {
                this.getProductions(this.options);
            },
            deep: true,
        },

        search: {
            handler(newVal) {
                this.options.search = newVal;
                this.searchProductions(this.options);
            },
        },
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("production_edit") && !this.can("production_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
    },
};
</script>
