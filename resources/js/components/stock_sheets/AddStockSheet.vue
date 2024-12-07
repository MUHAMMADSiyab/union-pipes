<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >Stock Sheets Entry</v-card-title
                        >
                        <v-card-subtitle
                            >Add Stock Sheet Entries</v-card-subtitle
                        >

                        <v-card-text class="mt-2">
                            <v-form @submit.prevent="add">
                                <v-row>
                                    <v-col
                                        xl="3"
                                        lg="3"
                                        md="3"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('month')
                                            "
                                        ></small>
                                        <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.month"
                                                    v-on="on"
                                                    label="Month"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    filled
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="data.month"
                                                label="Month"
                                                type="month"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                </v-row>

                                <div
                                    v-for="(entry, index) in data.entries"
                                    :key="index"
                                    class="d-flex align-center mb-2 mt-4"
                                >
                                    <v-select
                                        v-model="entry.product"
                                        :items="products"
                                        item-text="product_full_name"
                                        item-value="product_full_name"
                                        label="Product"
                                        dense
                                        filled
                                        class="mr-2"
                                        :search-input.sync="productSearch"
                                        @change="calculateTotals(entry)"
                                    >
                                        <template v-slot:no-data>
                                            <v-list-item>
                                                <v-list-item-title>
                                                    No products found
                                                </v-list-item-title>
                                            </v-list-item>
                                        </template>
                                    </v-select>
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.product`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model.number="entry.quantity"
                                        label="Quantity"
                                        type="number"
                                        dense
                                        filled
                                        class="mr-2"
                                        @input="calculateTotals(entry)"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.quantity`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model.number="entry.weight"
                                        label="Weight"
                                        type="number"
                                        dense
                                        filled
                                        class="mr-2"
                                        @input="calculateTotals(entry)"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.weight`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model.number="entry.total_weight"
                                        label="Total Weight"
                                        type="number"
                                        dense
                                        filled
                                        class="mr-2"
                                        readonly
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.total_weight`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model.number="entry.rate"
                                        label="Rate"
                                        type="number"
                                        dense
                                        filled
                                        class="mr-2"
                                        @input="calculateTotals(entry)"
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.rate`
                                            )
                                        "
                                    ></small>

                                    <v-text-field
                                        v-model.number="entry.total_amount"
                                        label="Total Amount"
                                        type="number"
                                        dense
                                        filled
                                        readonly
                                    />
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `entries.${index}.total_amount`
                                            )
                                        "
                                    ></small>

                                    <v-btn
                                        icon
                                        @click.prevent="removeEntry(index)"
                                    >
                                        <v-icon>mdi-minus-circle</v-icon>
                                    </v-btn>
                                </div>

                                <v-btn
                                    color="green white--text"
                                    @click.prevent="addEntry"
                                >
                                    <v-icon>mdi-plus</v-icon> Add Entry
                                </v-btn>
                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="float-end"
                                >
                                    Save
                                </v-btn>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            productSearch: null,
            data: {
                month: "",
                entries: [
                    {
                        product: null,
                        quantity: 0,
                        weight: 0,
                        rate: 0,
                        total_weight: 0,
                        total_amount: 0,
                    },
                ],
            },
        };
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            products: "product/products",
        }),
    },

    methods: {
        ...mapActions({
            addStockSheet: "stock_sheet/addStockSheet",
            fetchProducts: "product/getProducts",
        }),

        async add() {
            this.formLoading = true;

            await this.addStockSheet(this.data);

            this.formLoading = false;

            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.month = "";
                this.data.entries = [
                    {
                        product: null,
                        quantity: 0,
                        weight: 0,
                        rate: 0,
                        total_weight: 0,
                        total_amount: 0,
                    },
                ];
                this.validation.setMessages({});
            }
        },

        addEntry() {
            this.data.entries.push({
                product: null,
                quantity: 0,
                weight: 0,
                rate: 0,
                total_weight: 0,
                total_amount: 0,
            });
        },

        removeEntry(index) {
            this.data.entries.splice(index, 1);
        },

        calculateTotals(entry) {
            entry.total_weight = entry.quantity * entry.weight;
            entry.total_amount = entry.total_weight * entry.rate;
        },
    },

    created() {
        // Fetch products when component is created
        this.fetchProducts();
    },
};
</script>
