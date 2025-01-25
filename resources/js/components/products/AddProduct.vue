<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>New Product</v-card-title>
                        <v-card-subtitle>Add a New Product</v-card-subtitle>

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
                                                validation.getMessage('name')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="product-name"
                                            label="Product Name"
                                            id="product-name"
                                            v-model="data.name"
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
                                                validation.getMessage('type')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="product-type"
                                            label="Type"
                                            id="product-type"
                                            v-model="data.type"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
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
                                                    'per_unit_weight'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            type="number"
                                            steps=".01"
                                            name="product-per_unit_weight"
                                            label="Per Unit Weight"
                                            id="product-per_unit_weight"
                                            v-model="data.per_unit_weight"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
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
                                                    'per_kg_price'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            type="number"
                                            steps=".01"
                                            name="product-per_kg_price"
                                            label="Per Kg Price"
                                            id="product-per_kg_price"
                                            v-model="data.per_kg_price"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
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
                                                validation.getMessage('size')
                                            "
                                        ></small>
                                        <v-text-field
                                            type="number"
                                            steps=".01"
                                            name="product-size"
                                            label="Size"
                                            id="product-size"
                                            v-model="data.size"
                                            dense
                                            outlined
                                        ></v-text-field>
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
                name: "",
                per_kg_price: "",
                per_unit_weight: "",
                size: "",
                type: "",
            },
        };
    },

    methods: {
        ...mapActions({ addProduct: "product/addProduct" }),

        async add() {
            this.formLoading = true;

            await this.addProduct(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.name = "";
                this.data.size = "";
                this.data.type = "";
                this.data.per_kg_price = "";
                this.data.per_unit_weight = "";
                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
        }),
    },
};
</script>
