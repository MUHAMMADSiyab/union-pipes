<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>Edit Stock</v-card-title>
        <v-card-subtitle>Edit a stock</v-card-subtitle>

        <v-card-text class="mt-1">
            <v-form @submit.prevent="update">
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
                    v-text="validation.getMessage('quantity')"
                ></small>
                <v-text-field
                    name="quantity"
                    label="Weight"
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

                <v-btn color="success" type="submit">Update</v-btn>
                <v-btn color="secondary" @click="$emit('closeEditMode')"
                    >Cancel</v-btn
                >
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
    props: ["stock"],

    mixins: [ValidationMixin],

    data() {
        const { id, quantity, length, date, stock_item_id, description } =
            this.stock;

        return {
            formLoading: false,
            data: {
                stock_item_id,
                id,
                length,
                quantity,
                date,
                description,
            },
        };
    },

    methods: {
        ...mapActions({
            updateStock: "stock/updateStock",
        }),

        async update() {
            this.formLoading = true;

            await this.updateStock(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});

                this.$emit("closeEditMode");
            }
        },

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
        }),
    },

    watch: {
        stock: {
            handler(newStock) {
                this.data.stock_item_id = newStock.stock_item_id;
                this.data.id = newStock.id;
                this.data.quantity = newStock.quantity;
                this.data.date = newStock.date;
                this.data.description = newStock.description;
            },
            deep: true,
        },
    },
};
</script>
