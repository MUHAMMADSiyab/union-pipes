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
                        <v-card-subtitle>Edit Stock Item</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="update">
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
                                            name="stock-item-name"
                                            label="Stock Item Name"
                                            id="stock-item-name"
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

                                <v-btn color="success" type="submit"
                                    >Update</v-btn
                                >
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
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
                id: "",
                name: "",
                available_quantity: "",
                description: "",
            },
        };
    },

    methods: {
        ...mapActions({
            getStockItem: "stock_item/getStockItem",
            updateStockItem: "stock_item/updateStockItem",
        }),

        async update() {
            this.formLoading = true;

            await this.updateStockItem(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});
                return this.$router.push({ name: "stock_items" });
            }
        },
    },

    computed: {
        ...mapGetters({
            stock_item: "stock_item/stock_item",
            validationErrors: "validationErrors",
        }),
    },

    async mounted() {
        await this.getStockItem(this.$route.params.id);

        if (!this.stock_item) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.stock_item.id;
        this.data.name = this.stock_item.name;
        this.data.available_quantity = this.stock_item.available_quantity;
        this.data.description = this.stock_item.description;
    },
};
</script>
