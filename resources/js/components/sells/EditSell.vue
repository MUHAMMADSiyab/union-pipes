<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <SellGatePass v-if="data.gate_pass_id" class="mb-2" />

                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>Edit Sell</v-card-title>
                        <v-card-subtitle>Edit this sell</v-card-subtitle>

                        <v-card-text class="mt-3">
                            <v-form @submit.prevent="update">
                                <!-- Gate pass -->
                                <v-row>
                                    <v-col cols="12">
                                        <v-select
                                            :items="gate_passes"
                                            item-text="full_name"
                                            item-value="id"
                                            v-model="data.gate_pass_id"
                                            @change="handleGatePassChange"
                                            placeholder="Select Gate Pass"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>
                                </v-row>

                                <v-row>
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
                                                validation.getMessage('date')
                                            "
                                        ></small>
                                        <v-datetime-picker
                                            label="Date"
                                            v-model="data.date"
                                            :textFieldProps="{
                                                dense: true,
                                                outlined: true,
                                            }"
                                            :timePickerProps="{
                                                'use-seconds': true,
                                            }"
                                            timeFormat="HH:mm:ss"
                                        >
                                            <v-icon slot="timeIcon"
                                                >mdi-clock-time-three</v-icon
                                            >
                                            <v-icon slot="dateIcon"
                                                >mdi-calendar</v-icon
                                            >
                                        </v-datetime-picker>
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
                                                    'customer_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="customers"
                                            item-text="name"
                                            item-value="id"
                                            v-model="data.customer_id"
                                            placeholder="Select Customer"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
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
                                                    'invoice_no'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            v-model="data.invoice_no"
                                            label="Invoice No."
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
                                                    'sales_tax_percentage'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            type="number"
                                            steps=".01"
                                            v-model="data.sales_tax_percentage"
                                            label="Sales Tax %"
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
                                        v-if="data.category === 'Pipe'"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('unit')
                                            "
                                        ></small>
                                        <v-select
                                            :items="units"
                                            v-model="data.unit"
                                            placeholder="Select Unit"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
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
                                                    'category'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="categories"
                                            v-model="data.category"
                                            placeholder="Select Sell Category"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
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
                                                    'total_amount'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="total_amount"
                                            label="Total Amount"
                                            id="total_amount"
                                            v-model="data.total_amount"
                                            type="number"
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
                                                    'discount'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="discount"
                                            label="Discount %"
                                            id="discount"
                                            v-model="data.discount"
                                            type="number"
                                            :hint="
                                                (
                                                    data.total_amount *
                                                    (data.discount / 100)
                                                ).toString()
                                            "
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
                                        <v-text-field
                                            name="discounted_total_amount"
                                            label="Discounted Total Amount"
                                            id="discount"
                                            readonly
                                            :value="
                                                data.total_amount -
                                                data.total_amount *
                                                    (data.discount / 100)
                                            "
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

                                <!-- Sell Items -->
                                <v-row
                                    class="mt-3"
                                    v-if="data.category === 'Pipe'"
                                >
                                    <v-col cols="12" class="py-0">
                                        <h3 class="ml-3 mb-4">Sell Items</h3>

                                        <v-btn
                                            color="success"
                                            x-small
                                            @click="addItemRow"
                                            class="mb-5 ml-3"
                                            ><v-icon x-small
                                                >mdi-plus</v-icon
                                            ></v-btn
                                        >

                                        <v-list
                                            v-for="(item, i) in data.items"
                                            dense
                                            :key="i"
                                            no-action
                                        >
                                            <v-list-item>
                                                <v-btn
                                                    color="error"
                                                    x-small
                                                    @click="removeItemRow(i)"
                                                    class="mb-5 mr-2"
                                                    ><v-icon x-small
                                                        >mdi-minus</v-icon
                                                    ></v-btn
                                                >
                                                <v-row>
                                                    <v-col
                                                        xl="2"
                                                        lg="2"
                                                        md="2"
                                                        sm="12"
                                                        cols="12"
                                                        class="py-0"
                                                    >
                                                        <small
                                                            class="red--text"
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.product_id`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-select
                                                            :items="products"
                                                            item-text="product_full_name"
                                                            item-value="id"
                                                            v-model="
                                                                item.product_id
                                                            "
                                                            placeholder="Select Item"
                                                            autocomplete
                                                            filled
                                                        ></v-select>
                                                    </v-col>

                                                    <v-col
                                                        xl="2"
                                                        lg="2"
                                                        md="2"
                                                        sm="12"
                                                        cols="12"
                                                        class="py-0"
                                                    >
                                                        <small
                                                            class="red--text"
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.quantity`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.quantity
                                                            "
                                                            type="number"
                                                            label="Quantity"
                                                            dense
                                                            filled
                                                        ></v-text-field>
                                                    </v-col>

                                                    <v-col
                                                        xl="2"
                                                        lg="2"
                                                        md="2"
                                                        sm="12"
                                                        cols="12"
                                                        class="py-0"
                                                    >
                                                        <small
                                                            class="red--text"
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.rate`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="item.rate"
                                                            type="number"
                                                            label="Rate"
                                                            dense
                                                            filled
                                                        ></v-text-field>
                                                    </v-col>

                                                    <v-col
                                                        xl="2"
                                                        lg="2"
                                                        md="2"
                                                        sm="12"
                                                        cols="12"
                                                        class="py-0"
                                                    >
                                                        <small
                                                            class="red--text"
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.weight`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.weight
                                                            "
                                                            type="number"
                                                            label="Weight"
                                                            dense
                                                            filled
                                                        ></v-text-field>
                                                    </v-col>

                                                    <v-col
                                                        xl="2"
                                                        lg="2"
                                                        md="2"
                                                        sm="12"
                                                        cols="12"
                                                        class="py-0"
                                                    >
                                                        <small
                                                            class="red--text"
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.total`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="item.total"
                                                            type="number"
                                                            label="Total"
                                                            dense
                                                            filled
                                                        ></v-text-field>
                                                    </v-col>

                                                    <v-col
                                                        xl="2"
                                                        lg="2"
                                                        md="2"
                                                        sm="12"
                                                        cols="12"
                                                        class="py-0"
                                                    >
                                                        <small
                                                            class="red--text"
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.grand_total`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.grand_total
                                                            "
                                                            type="number"
                                                            label="Grand Total"
                                                            dense
                                                            filled
                                                        ></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-list-item>
                                            <v-divider class="mb-3"></v-divider>
                                        </v-list>
                                    </v-col>
                                </v-row>

                                <v-btn color="success" type="submit"
                                    >Update Sell</v-btn
                                >
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
import SellGatePass from "./partial/SellGatePass.vue";

export default {
    mixins: [ValidationMixin],

    components: {
        Navbar,
        SellGatePass,
    },

    data() {
        return {
            formLoading: false,
            units: ["Meter", "Kilogram", "Inch", "Foot", "Feet", "Centimeter"],
            categories: ["Pipe", "Return", "Misc", "Advance Payment"],
            data: {
                date: "",
                gate_pass_id: "",
                customer_id: "",
                invoice_no: "",
                category: "",
                unit: "",
                description: "",
                sales_tax_percentage: 0,
                total_amount: 0,
                discount: 0,
                items: [
                    {
                        product_id: "",
                        quantity: 0,
                        weight: 0,
                        rate: 0,
                        total: 0,
                        sales_tax: 0,
                        grand_total: 0,
                    },
                ],
            },
        };
    },

    methods: {
        ...mapActions({
            getAllCustomers: "customer/getAllCustomers",
            getProducts: "product/getProducts",
            getGatePasses: "gate_pass/getGatePasses",
            getSell: "sell/getSell",
            updateSell: "sell/updateSell",
        }),

        addItemRow() {
            this.data.items = [
                ...this.data.items,
                {
                    product_id: "",
                    quantity: 0,
                    weight: 0,
                    rate: 0,
                    total: 0,
                    sales_tax: 0,
                    grand_total: 0,
                },
            ];
        },

        removeItemRow(index) {
            this.data.items = this.data.items.filter((item, i) => i !== index);
        },

        handleGatePassChange(gate_pass_id) {
            if ("URLSearchParams" in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.set("gate_pass_id", gate_pass_id);
                window.location.search = searchParams.toString();
            }
        },

        async update() {
            this.formLoading = true;

            await this.updateSell({
                ...this.data,
                old_items: this.sell.sold_items,
            });

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // redirect
                return this.$router.push({ name: "sells" });
            }
        },
    },

    computed: {
        ...mapGetters({
            customers: "customer/customers",
            products: "product/products",
            sell: "sell/sell",
            gate_passes: "gate_pass/gate_passes",
            validationErrors: "validationErrors",
        }),
    },

    watch: {
        data: {
            handler(data) {
                let total_amount = 0;

                this.data.items.forEach((item, index) => {
                    const sales_tax_percentage =
                        data.sales_tax_percentage / 100;
                    item.total =
                        data.items[index].rate * data.items[index].quantity;
                    const sales_tax_amount = item.total * sales_tax_percentage;
                    item.sales_tax = sales_tax_amount;
                    item.grand_total = item.total + item.sales_tax;

                    total_amount += item.grand_total;
                });

                if (this.data.items.filter((item) => item.product_id).length) {
                    this.data.total_amount = total_amount;
                }
            },
            deep: true,
        },
    },

    async mounted() {
        await Promise.all([
            this.getGatePasses(),
            this.getAllCustomers(),
            this.getProducts(),
            this.getSell(this.$route.params.id),
        ]);

        if (!this.sell) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.sell.id;
        this.data.date = this.sell.date;
        this.data.sales_tax_percentage = this.sell.sales_tax_percentage;
        this.data.invoice_no = this.sell.invoice_no;
        this.data.unit = this.sell.unit;
        this.data.discount = this.sell.discount;
        this.data.description = this.sell.description;
        this.data.category = this.sell.category;
        this.data.customer_id = this.sell.customer_id;
        this.data.gate_pass_id = this.sell.gate_pass_id;
        this.data.items = this.sell.sold_items;
    },
};
</script>
