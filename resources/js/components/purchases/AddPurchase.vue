<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>New Purchase</v-card-title>
                        <v-card-subtitle>Add a New Purchase</v-card-subtitle>

                        <v-card-text class="mt-3">
                            <v-form @submit.prevent="add">
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
                                                    'company_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="companies"
                                            item-text="name"
                                            item-value="id"
                                            v-model="data.company_id"
                                            placeholder="Select Supplier Company"
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
                                        v-if="
                                            data.category === 'Raw Material' ||
                                            data.category === 'Other'
                                        "
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
                                        :xl="
                                            data.category === 'Raw Material' ||
                                            data.category === 'Other'
                                                ? 8
                                                : 12
                                        "
                                        :lg="
                                            data.category === 'Raw Material' ||
                                            data.category === 'Other'
                                                ? 8
                                                : 12
                                        "
                                        :md="
                                            data.category === 'Raw Material' ||
                                            data.category === 'Other'
                                                ? 8
                                                : 12
                                        "
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
                                            placeholder="Select Purchase Category"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="py-0">
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
                                </v-row>

                                <!-- Purchase Items -->
                                <v-row
                                    class="mt-3"
                                    v-if="
                                        data.category === 'Raw Material' ||
                                        data.category === 'Other'
                                    "
                                >
                                    <v-col cols="12" class="py-0">
                                        <h3 class="ml-3 mb-4">
                                            Purchase Items
                                        </h3>

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
                                                                    `items.${i}.purchase_item_id`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-select
                                                            :items="
                                                                purchase_items
                                                            "
                                                            item-text="name"
                                                            item-value="id"
                                                            v-model="
                                                                item.purchase_item_id
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
                                                                    `items.${i}.sales_tax`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.sales_tax
                                                            "
                                                            type="number"
                                                            label="Sales Tax"
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

                                <v-btn color="primary" type="submit"
                                    >Add Purchase</v-btn
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
import moment from "moment";

export default {
    mixins: [ValidationMixin],

    components: {
        Navbar,
    },

    data() {
        return {
            formLoading: false,
            categories: [
                "Raw Material",
                "Opening Balance",
                "Advance Payment",
                "Other",
            ],
            data: {
                date: moment().format("Y-MM-DD HH:mm:ss"),
                company_id: "",
                invoice_no: "",
                category: "",
                sales_tax_percentage: 0,
                total_amount: 0,
                items: [
                    {
                        purchase_item_id: "",
                        quantity: 0,
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
            getCompanies: "company/getCompanies",
            getPurchaseItems: "purchase_item/getPurchaseItems",
            addPurchase: "purchase/addPurchase",
        }),

        addItemRow() {
            this.data.items = [
                ...this.data.items,
                {
                    purchase_item_id: "",
                    quantity: 0,
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

        async add() {
            this.formLoading = true;

            await this.addPurchase(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.date = "";
                this.data.invoice_no = "";
                this.data.company_id = "";
                this.data.category = "";
                this.data.sales_tax_percentage = 0;
                this.data.total_amount = "";
                this.data.items = [
                    {
                        purchase_item_id: "",
                        quantity: 0,
                        rate: 0,
                        total: 0,
                        sales_tax: 0,
                        grand_total: 0,
                    },
                ];

                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            companies: "company/companies",
            purchase_items: "purchase_item/purchase_items",
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

                if (
                    this.data.items.filter((item) => item.purchase_item_id)
                        .length
                ) {
                    this.data.total_amount = total_amount;
                }
            },
            deep: true,
        },
    },

    async mounted() {
        await Promise.all([this.getCompanies(), this.getPurchaseItems()]);
    },
};
</script>
