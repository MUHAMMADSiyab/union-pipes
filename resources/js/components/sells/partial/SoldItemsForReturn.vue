<template>
    <v-card class="mt-2">
        <v-card-title primary-title>
            <h6 class="text-uppercase">Return Sold Items</h6>
        </v-card-title>
        <v-card-text>
            <form @submit.prevent="save">
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
                <v-simple-table dense>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>🚀</th>
                                <th>Item</th>
                                <th>Rate</th>
                                <th>Weight</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, i) in data.returned_items"
                                :key="i"
                            >
                                <td>
                                    <v-btn
                                        color="error"
                                        x-small
                                        @click="removeItemRow(i)"
                                        ><v-icon x-small
                                            >mdi-minus</v-icon
                                        ></v-btn
                                    >
                                </td>
                                <td>
                                    <v-select
                                        class="mt-3"
                                        :items="products"
                                        item-text="product_full_name"
                                        item-value="id"
                                        v-model="item.product_id"
                                        placeholder="Select Item"
                                        autocomplete
                                        readonly
                                        dense
                                        outlined
                                    ></v-select>
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `returned_items.${i}.product_id`
                                            )
                                        "
                                    ></small>
                                </td>
                                <td>
                                    <v-text-field
                                        class="mt-3"
                                        v-model="item.rate"
                                        type="number"
                                        label="Rate"
                                        dense
                                        outlined
                                    ></v-text-field>
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `returned_items.${i}.rate`
                                            )
                                        "
                                    ></small>
                                </td>
                                <td>
                                    <v-text-field
                                        v-model="item.weight"
                                        type="number"
                                        label="Weight"
                                        dense
                                        outlined
                                    ></v-text-field>
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `returned_items.${i}.weight`
                                            )
                                        "
                                    ></small>
                                </td>
                                <td>
                                    <v-text-field
                                        v-model="item.quantity"
                                        type="number"
                                        label="Quantity"
                                        dense
                                        outlined
                                    ></v-text-field>
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `returned_items.${i}.quantity`
                                            )
                                        "
                                    ></small>
                                </td>
                                <td>
                                    <v-text-field
                                        v-model="item.total"
                                        type="number"
                                        label="Total"
                                        dense
                                        outlined
                                    ></v-text-field>
                                    <small
                                        class="red--text"
                                        v-if="validation.hasErrors()"
                                        v-text="
                                            validation.getMessage(
                                                `returned_items.${i}.total`
                                            )
                                        "
                                    ></small>
                                </td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>

                <v-btn color="success" type="submit" class="mt-3">Return</v-btn>
            </form>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
            <v-btn color="secondary" right @click="closeDialog">Close</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
    props: ["soldItems", "dialog"],

    mixins: [CurrencyMixin, ValidationMixin],

    data() {
        return {
            formLoading: false,
            data: {
                date: "",
                returned_items: this.soldItems.map((item) => ({
                    sell_id: item.sell_id,
                    product_id: item.product_id,
                    product: item.product,
                    quantity: item.quantity,
                    weight: item.weight,
                    rate: item.rate,
                    total: item.total,
                })),
            },
        };
    },

    methods: {
        ...mapActions({
            getProducts: "product/getProducts",
            returnSoldItems: "sell/returnSoldItems",
        }),

        removeItemRow(index) {
            this.data.returned_items = this.data.returned_items.filter(
                (item, i) => i !== index
            );
        },

        async save() {
            this.formLoading = true;

            this.data.returned_items = this.data.returned_items.map((item) => ({
                ...item,
                date: this.data.date,
            }));

            await this.returnSoldItems(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});

                this.closeDialog();

                this.$router.go(this.$router.currentRoute)
            }
        },

        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            products: "product/products",
        }),
    },

    watch: {
        "data.returned_items": {
            handler(items) {
                this.data.returned_items.forEach((item, index) => {
                    item.total = items[index].rate * items[index].quantity;
                });
            },
            deep: true,
        },
    },

    mounted() {
        this.getProducts();
    },
};
</script>
