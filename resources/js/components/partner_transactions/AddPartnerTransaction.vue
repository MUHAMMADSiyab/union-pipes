<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >New Partner Transaction</v-card-title
                        >
                        <v-card-subtitle>Add a New Transaction</v-card-subtitle>

                        <v-card-text class="mt-2">
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
                                                validation.getMessage(
                                                    'partner_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="partners"
                                            label="Partner"
                                            id="partner_id"
                                            name="name"
                                            item-value="id"
                                            item-text="name"
                                            v-model="data.partner_id"
                                            search=""
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
                                                validation.getMessage('title')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="title"
                                            label="Title"
                                            id="title"
                                            v-model="data.title"
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
                                                validation.getMessage('type')
                                            "
                                        ></small>
                                        <v-select
                                            :items="['Debit', 'Credit']"
                                            name="type"
                                            label="Type"
                                            id="type"
                                            v-model="data.type"
                                            dense
                                            outlined
                                        ></v-select>
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

                                <!-- Payment -->
                                <v-row v-if="paymentSetting">
                                    <v-col cols="12" class="py-0 mb-3">
                                        <h6
                                            class="text-subtitle-2 primary--text"
                                        >
                                            Payment Details
                                        </h6>
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
                                                validation.getMessage('amount')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="amount"
                                            :label="`Amount (${paymentSetting.currency})`"
                                            id="amount"
                                            v-model="data.payment.amount"
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
                                                    'payment_date'
                                                )
                                            "
                                        ></small>

                                        <v-datetime-picker
                                            label="Payment Date"
                                            v-model="data.payment.payment_date"
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
                                            v-model="
                                                data.payment.payment_method
                                            "
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
                                            v-model="data.payment.bank_id"
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <template
                                        v-if="
                                            data.payment.payment_method ===
                                            'Cheque'
                                        "
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
                                                v-model="
                                                    data.payment.cheque_type
                                                "
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
                                                v-model="data.payment.cheque_no"
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
                                                            data.payment
                                                                .cheque_due_date
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
                                                        data.payment
                                                            .cheque_due_date
                                                    "
                                                    label="Date"
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
                                </v-row>

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-3"
                                    >Add Transaction</v-btn
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

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            data: {
                title: "",
                type: "",
                amount: "",
                partner_id: "",
                description: "",
                payment: {
                    model: "App\\Models\\PartnerTransaction",
                    paymentable_id: null,
                    amount: "",
                    transaction_type: "",
                    payment_date: "",
                    bank_id: "",
                    payment_method: "",
                    cheque_type: "",
                    cheque_no: "",
                    cheque_due_date: "",
                    cheque_images: [],
                    description: "",
                },
            },
        };
    },

    methods: {
        ...mapActions({
            getPartners: "partner/getPartners",
            getBanks: "bank/getBanks",
            getPaymentSetting: "getPaymentSetting",
            addPartnerTransaction: "partner_transaction/addPartnerTransaction",
            addNewPayment: "payment/addNewPayment",
        }),

        handleFiles(files) {
            this.data.payment.cheque_images = files;
        },

        async add() {
            this.formLoading = true;

            this.data.amount = this.data.payment.amount;

            await this.addPartnerTransaction(this.data);

            if (this.recent_partner_transaction) {
                this.data.payment.paymentable_id =
                    this.recent_partner_transaction.id;
                this.data.payment.description = this.data.description;

                await this.addNewPayment(this.data.payment);
            }

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.title = "";
                this.data.partner_id = "";
                this.data.description = "";
                this.data.payment.amount = "";
                this.data.payment.payment_date = "";
                this.data.payment.bank_id = "";
                this.data.payment.payment_method = "";
                this.data.payment.cheque_type = "";
                this.data.payment.cheque_no = "";
                this.data.payment.cheque_due_date = "";
                this.data.payment.cheque_images = [];
                this.data.payment.description = "";

                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    watch: {
        "data.type": {
            handler(newVal) {
                this.data.payment.transaction_type = newVal;
            },
            deep: true,
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            partners: "partner/partners",
            banks: "bank/banks",
            paymentSetting: "paymentSetting",
            recent_partner_transaction:
                "partner_transaction/recent_partner_transaction",
        }),
    },

    mounted() {
        Promise.all([
            this.getPaymentSetting(),
            this.getPartners(),
            this.getBanks(),
        ]);
    },
};
</script>
