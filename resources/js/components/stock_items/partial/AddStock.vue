<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>Add Stock</v-card-title>
        <v-card-subtitle>Add a new Stock</v-card-subtitle>

        <v-card-text class="mt-1">
            <v-form @submit.prevent="add">
                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('length')"
                ></small>
                <v-text-field
                    name="length"
                    label="Length (Meter/Foot)"
                    id="length"
                    v-model="data.length"
                    type="number"
                    dense
                    outlined
                ></v-text-field>

                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('per_unit_weight')"
                ></small>
                <v-text-field
                    name="per_unit_weight"
                    label="Per Unit Weight"
                    id="per_unit_weight"
                    v-model="data.per_unit_weight"
                    type="number"
                    dense
                    outlined
                ></v-text-field>

                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('quantity')"
                ></small>
                <v-text-field
                    name="quantity"
                    label="Total Weight"
                    id="quantity"
                    v-model="data.quantity"
                    type="number"
                    dense
                    outlined
                ></v-text-field>

                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('date')"
                ></small>
                <v-menu max-width="290px" min-width="auto">
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="data.date"
                            v-on="on"
                            label="Date"
                            prepend-inner-icon="mdi-calendar"
                            dense
                            outlined
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="data.date"
                        label="Date"
                        no-title
                        outlined
                        dense
                        show-current
                    ></v-date-picker>
                </v-menu>

                <small
                    class="red--text"
                    v-if="validation.hasErrors()"
                    v-text="validation.getMessage('description')"
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

                <v-btn color="success" type="submit">Add Stock</v-btn>
                <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
    props: ["stockItem"],

    mixins: [ValidationMixin],

    data() {
        return {
            formLoading: false,
            data: {
                stock_item_id: this.stockItem.id,
                length: 0,
                per_unit_weight: this.stockItem.product?.per_unit_weight || 0,
                quantity: 0,
                date: "",
                description: "",
            },
        };
    },

    methods: {
        ...mapActions({
            addStock: "stock/addStock",
        }),

        async add() {
            this.formLoading = true;

            await this.addStock(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.length = 0;
                this.data.quantity = 0;
                this.data.per_unit_weight = 0;
                this.data.date = "";
                this.data.description = "";
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
        stockItem: {
            handler(newVal) {
                this.data.stock_item_id = newVal.id;
                this.data.per_unit_weight =
                    newVal.product?.per_unit_weight || 0;
            },
        },

        data: {
            handler(newVal) {
                this.data.quantity =
                    this.data.length * this.data.per_unit_weight;
            },
            deep: true,
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
        }),
    },
};
</script>
