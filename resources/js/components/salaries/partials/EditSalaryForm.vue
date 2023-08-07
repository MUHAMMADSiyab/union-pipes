<template>
    <v-card :loading="formLoading" :disabled="formLoading">
        <v-card-title primary-title>Edit Salary</v-card-title>
        <v-card-subtitle>Edit the employee's salary</v-card-subtitle>

        <v-card-text class="mt-2">
            <v-form @submit.prevent="update" autocomplete="off">
                <v-row>
                    <v-col cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('month')"
                        ></small>
                        <v-menu max-width="290px" min-width="auto">
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    v-model="data.month"
                                    v-on="on"
                                    label="Salary Month"
                                    prepend-inner-icon="mdi-calendar"
                                    dense
                                    outlined
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                type="month"
                                v-model="data.month"
                                label="Salary Month"
                                no-title
                                outlined
                                dense
                                show-current
                            ></v-date-picker>
                        </v-menu>
                    </v-col>

                    <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('additional_amount')"
                        ></small>
                        <v-text-field
                            type="number"
                            name="additional_amount"
                            label="Additional Amount"
                            id="additional_amount"
                            v-model="data.additional_amount"
                            dense
                            outlined
                        ></v-text-field>
                    </v-col>

                    <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('deducted_amount')"
                        ></small>
                        <v-text-field
                            type="number"
                            name="deducted_amount"
                            label="Deducted Amount"
                            id="deducted_amount"
                            v-model="data.deducted_amount"
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
                        class="py-0 mb-2"
                    >
                        <v-switch
                            color="primary"
                            v-model="data.loan"
                            label="Taking Loan"
                            class="mb-2"
                        ></v-switch>
                    </v-col>
                </v-row>

                <!-- Payment -->
                <v-row v-if="paymentSetting" class="mb-2">
                    <v-col cols="12" class="py-0 mb-1">
                        <h6 class="text-subtitle-2 primary--text">
                            Payment Details
                        </h6>
                    </v-col>

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('amount')"
                        ></small>
                        <v-text-field
                            name="amount"
                            :label="`Amount Paid (${paymentSetting.currency})`"
                            id="amount"
                            v-model="data.payment.amount"
                            max="purchase.amount"
                            type="number"
                            dense
                            outlined
                        ></v-text-field>
                    </v-col>

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('payment_date')"
                        ></small>
                        <v-menu max-width="290px" min-width="auto">
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    v-model="data.payment.payment_date"
                                    v-on="on"
                                    label="Payment Date"
                                    prepend-inner-icon="mdi-calendar"
                                    dense
                                    outlined
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="data.payment.payment_date"
                                label="Payment Date"
                                no-title
                                outlined
                                dense
                                show-current
                            ></v-date-picker>
                        </v-menu>
                    </v-col>

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('payment_method')"
                        ></small>
                        <v-select
                            :items="paymentSetting.payment_methods"
                            label="Payment Method"
                            id="payment-method"
                            name="payment-method"
                            v-model="data.payment.payment_method"
                            dense
                            outlined
                        ></v-select>
                    </v-col>

                    <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('bank_id')"
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

                    <template v-if="data.payment.payment_method === 'Cheque'">
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
                                v-text="validation.getMessage('cheque_type')"
                            ></small>
                            <v-select
                                :items="paymentSetting.cheque_types"
                                label="Cheque Type"
                                id="cheque-type"
                                name="cheque-type"
                                v-model="data.payment.cheque_type"
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
                                v-text="validation.getMessage('cheque_no')"
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
                                    validation.getMessage('cheque_due_date')
                                "
                            ></small>
                            <v-menu max-width="290px" min-width="auto">
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="data.payment.cheque_due_date"
                                        v-on="on"
                                        label="Cheque Due Date"
                                        prepend-inner-icon="mdi-calendar"
                                        dense
                                        outlined
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="data.payment.cheque_due_date"
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

                <v-row class="mb-2">
                    <v-col cols="12" class="py-0">
                        <small
                            class="red--text"
                            v-if="validation.hasErrors()"
                            v-text="validation.getMessage('description')"
                        ></small>
                        <v-textarea
                            name="description"
                            label="Description"
                            id="description"
                            v-model="data.payment.description"
                            rows="3"
                            dense
                            outlined
                        ></v-textarea>
                    </v-col>
                </v-row>

                <v-btn color="primary" type="submit">Update</v-btn>
                <v-btn color="secondary" type="button" @click="closeDialog"
                    >Cancel</v-btn
                >
            </v-form>

            <alert />
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
    props: ["salary"],

    mixins: [ValidationMixin],

    data() {
        return {
            formLoading: false,
            data: {
                id: this.salary.id,
                month: this.salary.month,
                loan: this.salary.loan,
                additional_amount: this.salary.additional_amount,
                deducted_amount: this.salary.deducted_amount,
                payment: {
                    id: this.salary.payments[0].id,
                    amount: this.salary.payments[0].amount,
                    payment_date: this.salary.payments[0].payment_date,
                    bank_id: this.salary.payments[0].bank_id,
                    payment_method: this.salary.payments[0].payment_method,
                    cheque_type: this.salary.payments[0].cheque_type,
                    cheque_no: this.salary.payments[0].cheque_no,
                    cheque_due_date: this.salary.payments[0].cheque_due_date,
                    cheque_images: [],
                    description: this.salary.payments[0].description,
                },
            },
        };
    },

    methods: {
        ...mapActions({
            getBanks: "bank/getBanks",
            getPaymentSetting: "getPaymentSetting",
            updateSalary: "salary/updateSalary",
            editPayment: "payment/editPayment",
        }),

        async update() {
            this.formLoading = true;

            this.data.total_paid = this.data.payment.amount || 0;
            this.data.date = this.data.payment.payment_date;

            await this.updateSalary(this.data);

            if (this.old_salary) {
                await this.editPayment(this.data.payment);
            }

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});

                this.closeDialog();

                window.location.reload();
            }
        },
        closeDialog() {
            this.$emit("closeDialog");
        },
    },

    computed: {
        ...mapGetters({
            validationErrors: "validationErrors",
            alertData: "alert/data",
            paymentSetting: "paymentSetting",
            banks: "bank/banks",
            old_salary: "salary/old_salary",
        }),
    },

    watch: {
        salary: {
            handler({ id, month, additional_amount, deducted_amount }) {
                this.data.id = id;
                this.data.month = month;
                this.data.additional_amount = additional_amount;
                this.data.deducted_amount = deducted_amount;
            },
        },
    },

    mounted() {
        Promise.all([this.getPaymentSetting(), this.getBanks()]);
    },
};
</script>
