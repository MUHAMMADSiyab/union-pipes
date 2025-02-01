<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >New Bulk Payment</v-card-title
                        >
                        <v-card-subtitle
                            >Add a New Bulk Payment</v-card-subtitle
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
                                        <v-switch
                                            v-model="data.is_advance"
                                            label="Advance Payment"
                                            color="primary"
                                        />
                                    </v-col>
                                </v-row>
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
                                                validation.getMessage('type')
                                            "
                                        ></small>
                                        <v-select
                                            :items="['Purchase', 'Sell']"
                                            v-model="data.type"
                                            placeholder="Select Type"
                                            autocomplete
                                            clearable
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
                                        <template v-if="data.type === 'Sell'">
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
                                                clearable
                                                dense
                                                outlined
                                            ></v-select>
                                        </template>

                                        <template
                                            v-if="data.type === 'Purchase'"
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
                                                placeholder="Select Company"
                                                autocomplete
                                                clearable
                                                dense
                                                outlined
                                            ></v-select>
                                        </template>
                                    </v-col>
                                </v-row>

                                <!-- Payment -->
                                <v-row v-if="paymentSetting">
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
                                                validation.getMessage('amount')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="amount"
                                            :label="`Amount (${paymentSetting.currency})`"
                                            id="amount"
                                            v-model="data.amount"
                                            max="purchase.amount"
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
                                                validation.getMessage('date')
                                            "
                                        ></small>
                                        <!-- <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="
                                                        data.payment
                                                            .payment_date
                                                    "
                                                    v-on="on"
                                                    label="Payment Date"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    outlined
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="
                                                    data.payment_date
                                                "
                                                label="Payment Date"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu> -->

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
                                                    'payment_method'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="
                                                paymentSetting.payment_methods
                                            "
                                            label="Payment Method"
                                            id="payment-method"
                                            name="payment-method"
                                            v-model="data.payment_method"
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
                                                validation.getMessage('bank_id')
                                            "
                                        ></small>
                                        <v-select
                                            :items="banks"
                                            name="bank"
                                            label="Select Bank"
                                            item-text="name"
                                            item-value="id"
                                            id="bank"
                                            v-model="data.bank_id"
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <template
                                        v-if="data.payment_method === 'Cheque'"
                                    >
                                        <v-col
                                            xl="6"
                                            lg="6"
                                            md="6"
                                            sm="12"
                                            xs="12"
                                            class="py-0"
                                        >
                                            <small
                                                class="red--text"
                                                v-if="validation.hasErrors()"
                                                v-text="
                                                    validation.getMessage(
                                                        'cheque_type'
                                                    )
                                                "
                                            ></small>
                                            <v-select
                                                :items="
                                                    paymentSetting.cheque_types
                                                "
                                                label="Cheque Type"
                                                id="cheque-type"
                                                name="cheque-type"
                                                v-model="data.cheque_type"
                                                dense
                                                outlined
                                            ></v-select>
                                        </v-col>
                                        <v-col
                                            xl="6"
                                            lg="6"
                                            md="6"
                                            sm="12"
                                            xs="12"
                                            class="py-0"
                                        >
                                            <small
                                                class="red--text"
                                                v-if="validation.hasErrors()"
                                                v-text="
                                                    validation.getMessage(
                                                        'cheque_no'
                                                    )
                                                "
                                            ></small>
                                            <v-text-field
                                                name="cheque-no"
                                                label="Cheque No."
                                                id="cheque-no"
                                                v-model="data.cheque_no"
                                                dense
                                                outlined
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            xl="6"
                                            lg="6"
                                            md="6"
                                            sm="12"
                                            xs="12"
                                            class="py-0"
                                        >
                                            <small
                                                class="red--text"
                                                v-if="validation.hasErrors()"
                                                v-text="
                                                    validation.getMessage(
                                                        'cheque_due_date'
                                                    )
                                                "
                                            ></small>
                                            <v-menu
                                                max-width="290px"
                                                min-width="auto"
                                            >
                                                <template
                                                    v-slot:activator="{ on }"
                                                >
                                                    <v-text-field
                                                        v-model="
                                                            data.cheque_due_date
                                                        "
                                                        v-on="on"
                                                        label="Cheque Due Date"
                                                        prepend-inner-icon="mdi-calendar"
                                                        dense
                                                        outlined
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="
                                                        data.cheque_due_date
                                                    "
                                                    label="Cheque Due Date"
                                                    no-title
                                                    outlined
                                                    dense
                                                    show-current
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col
                                            xl="6"
                                            lg="6"
                                            md="6"
                                            sm="12"
                                            xs="12"
                                            class="py-0"
                                        >
                                            <small
                                                class="red--text"
                                                v-if="validation.hasErrors()"
                                                v-text="
                                                    validation.getMessage(
                                                        'cheque_images',
                                                        true,
                                                        true
                                                    )
                                                "
                                            ></small>
                                            <v-file-input
                                                name="cheque-images"
                                                label="Cheque Image(s)"
                                                id="cheque-images"
                                                @change="handleFiles"
                                                prepend-inner-icon="mdi-camera"
                                                prepend-icon=""
                                                multiple
                                                dense
                                                :clearable="false"
                                                outlined
                                                hint="Only image files | Max. size 2MB"
                                            ></v-file-input>
                                        </v-col>
                                    </template>

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
                                            name="expense-description"
                                            label="Description"
                                            id="expense-description"
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
import moment from "moment";

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            data: {
                is_advance: false,
                type: "",
                customer_id: "",
                company_id: "",
                amount: "",
                description: "",
                amount: "",
                date: moment().format("Y-MM-DD HH:mm:ss"),
                bank_id: "",
                payment_method: "",
                cheque_type: "",
                cheque_no: "",
                cheque_due_date: "",
                cheque_images: [],
                description: "",
            },
        };
    },

    methods: {
        ...mapActions({
            getExpenseSources: "expense_source/getExpenseSources",
            getBanks: "bank/getBanks",
            getCustomers: "customer/getCustomers",
            getCompanies: "company/getCompanies",
            getPaymentSetting: "getPaymentSetting",
            addBulkPayment: "bulk_payment/addBulkPayment",
            addNewPayment: "payment/addNewPayment",
        }),

        handleFiles(files) {
            this.data.cheque_images = files;
        },

        async add() {
            this.formLoading = true;

            await this.addBulkPayment(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.type = "";
                this.data.is_advance = false;
                this.data.customer_id = "";
                this.data.company_id = "";
                this.data.description = "";
                this.data.amount = "";
                this.data.date = "";
                this.data.bank_id = "";
                this.data.payment_method = "";
                this.data.cheque_type = "";
                this.data.cheque_no = "";
                this.data.cheque_due_date = "";
                this.data.cheque_images = [];
                this.data.description = "";

                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            expense_sources: "expense_source/expense_sources",
            customers: "customer/customers",
            companies: "company/companies",
            banks: "bank/banks",
            paymentSetting: "paymentSetting",
            recent_expense: "expense/recent_expense",
        }),
    },

    mounted() {
        Promise.all([
            this.getPaymentSetting(),
            this.getExpenseSources(),
            this.getBanks(),
            this.getCustomers(),
            this.getCompanies(),
        ]);
    },
};
</script>
