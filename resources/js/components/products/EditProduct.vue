<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>New Product</v-card-title>
        <v-card-subtitle>Edit Product</v-card-subtitle>

        <v-card-text class="mt-1">
            <v-form @submit.prevent="update">
                <v-row>
                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('name')"
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

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('type')"
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

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('per_kg_price')"
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

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('size')"
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

                <v-btn color="success" type="submit">Update</v-btn>
                <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
    props: ["singleProduct"],

    mixins: [ValidationMixin],

    data() {
        const { id, name, per_kg_price, type, size } = this.singleProduct;

        return {
            formLoading: false,
            data: {
                id,
                name,
                per_kg_price,
                type,
                size,
            },
        };
    },

    methods: {
        ...mapActions({ updateProduct: "product/updateProduct" }),

        async update() {
            this.formLoading = true;

            await this.updateProduct(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});
                this.closeDialog();
            }
        },

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    watch: {
        singleProduct: {
            handler({ id, name, per_kg_price, size, type }) {
                this.data.id = id;
                this.data.name = name;
                this.data.per_kg_price = per_kg_price;
                this.data.size = size;
                this.data.type = type;
            },
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
        }),
    },
};
</script>
