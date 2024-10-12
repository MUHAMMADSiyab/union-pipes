<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >New Stock Item</v-card-title
                        >
                        <v-card-subtitle>Add a New Stock Item</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="add">
                                <v-row>
                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'product_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="products"
                                            item-text="product_full_name"
                                            item-value="id"
                                            v-model="data.product_id"
                                            placeholder="Select Product"
                                            @change="
                                                (e) =>
                                                    (data.name = products.find(
                                                        (p) => p.id == e
                                                    )?.product_full_name)
                                            "
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('name')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="stock-item-name"
                                            label="Stock Item Name"
                                            id="stock-item-name"
                                            v-model="data.name"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <!-- <v-col
                                        xl="4"
                                        lg="4"
                                        md="4"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'per_unit_price'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="per_unit_price"
                                            label="Per Unit Price"
                                            id="per_unit_price"
                                            v-model="data.per_unit_price"
                                            type="number"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col> -->

                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'available_quantity'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="stock_item-available-quantity"
                                            label="Available Quantity (Weight)"
                                            id="stock_item-available-quantity"
                                            v-model="data.available_quantity"
                                            type="number"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'available_length'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="stock_item-available-length"
                                            label="Available Length (Meter/Foot)"
                                            id="stock_item-available-length"
                                            v-model="data.available_length"
                                            type="number"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col cols="12" class="py-0">
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'description'
                                                )
                                            "
                                        ></small>
                                        <v-textarea
                                            name="description"
                                            label="Description"
                                            id="description"
                                            v-model="data.description"
                                            rows="3"
                                            dense
                                            outlined
                                        ></v-textarea>
                                    </v-col>
                                </v-row>

                                <v-btn color="primary" type="submit">Add</v-btn>
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
            data: {
                product_id: "",
                name: "",
                // per_unit_price: 0,
                available_quantity: 0,
                available_length: 0,
                description: "",
            },
        };
    },

    methods: {
        ...mapActions({
            addStockItem: "stock_item/addStockItem",
            getProducts: "product/getProducts",
        }),

        async add() {
            this.formLoading = true;

            await this.addStockItem(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.product_id = "";
                this.data.name = "";
                // this.data.per_unit_price = 0;
                this.data.available_quantity = 0;
                this.data.available_length = 0;
                this.data.description = "";
                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            products: "product/products",
            validationErrors: "validationErrors",
        }),
    },

    // watch: {
    //     data: {
    //         handler(newVal) {
    //             data.
    //         },
    //         deep: true
    //     }
    // },

    mounted() {
        this.getProducts();
    },
};
</script>
