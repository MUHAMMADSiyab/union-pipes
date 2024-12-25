<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >Edit Partner Withdrawal</v-card-title
                        >
                        <v-card-subtitle>Update the Withdrawal</v-card-subtitle>

                        <v-card-text class="mt-2">
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
                                                    'partner_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="partners"
                                            label="Partner"
                                            id="expense_source"
                                            name="name"
                                            item-value="id"
                                            item-text="name"
                                            v-model="data.partner_id"
                                            search=""
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
                                                validation.getMessage(
                                                    'payment_date'
                                                )
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
                                                    data.payment.payment_date
                                                "
                                                label="Payment Date"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu> -->

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
                                        class="mt-1 mb-1"
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
                                    color="success"
                                    class="mt-3"
                                    type="submit"
                                    >Update</v-btn
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
                id: null,
                name: "",
                amount: "",
                expense_source_id: "",
                description: "",
                payment: {
                    id: null,
                    model: "App\\Models\\Partner Withdrawal",
                    paymentable_id: null,
                    amount: "",
                    discount: 0,
                    transaction_type: "Credit",
                    payment_date: "",
                    bank_id: "",
                    payment_method: "",
                    cheque_type: "",
                    cheque_no: "",
                    cheque_due_date: "",
                    cheque_images: [],
                    installment_month: "",
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
            updatePartnerWithdrawal:
                "partner_withdrawal/updatePartnerWithdrawal",
            getPartnerWithdrawal: "partner_withdrawal/getPartnerWithdrawal",
            editPayment: "payment/editPayment",
        }),

        handleFiles(files) {
            this.data.payment.cheque_images = files;
        },

        async update() {
            this.formLoading = true;

            this.data.amount =
                this.data.payment.amount || this.partner_withdrawal.amount;

            await this.updatePartnerWithdrawal(this.data);

            if (this.old_expense) {
                this.data.payment.description = this.data.description;
                this.data.payment.transaction_type = this.data.type;
                await this.editPayment(this.data.payment);
            }

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});

                return this.$router.push({ name: "partner_withdrawals" });
            }
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            partners: "partner/partners",
            banks: "bank/banks",
            paymentSetting: "paymentSetting",
            partner_withdrawal: "partner_withdrawal/partner_withdrawal",
            old_partner_withdrawal: "partner_withdrawal/old_partner_withdrawal",
        }),
    },

    async mounted() {
        await Promise.all([
            this.getPaymentSetting(),
            this.getPartners(),
            this.getBanks(),
            this.getPartnerWithdrawal(this.$route.params.id),
        ]);

        if (!this.partner_withdrawal) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.partner_withdrawal.id;
        this.data.partner_id = this.partner_withdrawal.partner_id;
        this.data.title = this.partner_withdrawal.title;
        this.data.amount = this.partner_withdrawal.amount;
        this.data.description = this.partner_withdrawal.description;
        this.data.payment.id = this.partner_withdrawal.payment.id;
        this.data.payment.paymentable_id = this.partner_withdrawal.id;
        this.data.payment.amount = this.partner_withdrawal.amount;
        this.data.payment.payment_date =
            this.partner_withdrawal.payment.payment_date;
        this.data.payment.bank_id = this.partner_withdrawal.payment.bank_id;
        this.data.payment.payment_method =
            this.partner_withdrawal.payment.payment_method;
        this.data.payment.cheque_type =
            this.partner_withdrawal.payment.cheque_type;
        this.data.payment.cheque_no = this.partner_withdrawal.payment.cheque_no;
        this.data.payment.cheque_due_date =
            this.partner_withdrawal.payment.cheque_due_date;
        this.data.payment.description =
            this.partner_withdrawal.payment.description;
    },
};
</script>
