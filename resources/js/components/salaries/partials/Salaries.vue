<template>
    <v-row>
        <v-col cols="12">
            <v-card>
                <v-card-title primary-title v-if="employee"
                    >Salary Details of {{ employee.name }}</v-card-title
                >

                <v-card-text class="mt-1">
                    <v-row>
                        <v-col cols="12">
                            <table class="w-100" id="salries_table">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Month</th>
                                        <th>Additional Amount</th>
                                        <th>Deducted Amount</th>
                                        <th>Total Paid</th>
                                        <th>Date of Payment</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th class="d-print-none">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="text-center">
                                    <template v-for="(salary, i) in salaries">
                                        <tr :key="salary.id">
                                            <td>{{ i + 1 }}</td>
                                            <td>
                                                {{ salary.month_formatted }}
                                            </td>
                                            <td>
                                                {{
                                                    money(
                                                        salary.additional_amount
                                                    )
                                                }}
                                            </td>
                                            <td>
                                                {{
                                                    money(
                                                        salary.deducted_amount
                                                    )
                                                }}
                                            </td>
                                            <td>
                                                {{ money(salary.total_paid) }}
                                            </td>
                                            <td>
                                                {{ formatDate(salary.date) }}
                                            </td>
                                            <td>{{ money(salary.balance) }}</td>
                                            <td>
                                                <v-chip
                                                    :color="
                                                        getStatusType(
                                                            salary.status
                                                        )
                                                    "
                                                    x-small
                                                >
                                                    {{ salary.status }}
                                                </v-chip>
                                            </td>
                                            <td class="d-print-none">
                                                <!-- Actions -->
                                                <v-btn
                                                    x-small
                                                    color="light"
                                                    @click="
                                                        showAddPaymentDialog({
                                                            id: salary.id,
                                                            balance:
                                                                salary.balance,
                                                        })
                                                    "
                                                    v-if="can('payment_create')"
                                                    :disabled="
                                                        salary.balance == 0
                                                    "
                                                    ><v-icon x-small
                                                        >mdi-plus-thick</v-icon
                                                    ></v-btn
                                                >

                                                <!-- <v-btn
                          x-small
                          color="light"
                          @click="showEditSalaryDialog(salary.id)"
                          v-if="can('salary_edit')"
                          ><v-icon x-small>mdi-pencil</v-icon></v-btn
                        > -->

                                                <v-btn
                                                    x-small
                                                    text
                                                    color="red darken-2"
                                                    @click="
                                                        setSalaryId(salary.id)
                                                    "
                                                    title="Delete"
                                                    v-if="can('salary_delete')"
                                                >
                                                    <v-icon small
                                                        >mdi-delete</v-icon
                                                    >
                                                </v-btn>
                                            </td>
                                        </tr>

                                        <!-- Payments -->
                                        <tr :key="`${salary.id}-${i}`">
                                            <td colspan="9">
                                                <Payments
                                                    :payments="salary.payments"
                                                    :payment-setting="
                                                        paymentSetting
                                                    "
                                                    type="nonDialog"
                                                    :employee-id="employeeId"
                                                />
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Totals -->
                                    <tr id="table-footer">
                                        <td colspan="3">
                                            Additional:
                                            {{ money(totals.total_additional) }}
                                        </td>
                                        <td>
                                            Deducted
                                            {{ money(totals.total_deducted) }}
                                        </td>

                                        <td colspan="2">
                                            Paid
                                            {{ money(totals.total_paid) }}
                                        </td>
                                        <td colspan="3">
                                            Balance:
                                            {{ money(totals.total_balance) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Add payment dialog -->
                            <v-dialog
                                v-model="addPaymentDialog"
                                max-width="600"
                                persistent
                            >
                                <AddPayment
                                    @closeDialog="closeAddPaymentDialog"
                                    :entry="currentSalaryRecord"
                                    :entry-data="{
                                        model: 'App\\Models\\Salary',
                                        transaction_type: 'Credit',
                                    }"
                                    :payment-setting="paymentSetting"
                                    :employee-id="employeeId"
                                />
                            </v-dialog>

                            <!-- Edit dialog -->
                            <v-dialog
                                v-model="editDialog"
                                max-width="600"
                                persistent
                            >
                                <EditSalaryForm
                                    :salary="salary"
                                    v-if="salary"
                                    @closeDialog="closeEditDialog"
                                />
                            </v-dialog>

                            <!-- Confirmation -->
                            <Confirmation
                                ref="confirmationComponent"
                                :id="salaryId"
                                @confirmDeletion="handleSalaryDelete()"
                            />
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <alert />
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
import Payments from "../../globals/payments/Payments.vue";
import AddPayment from "../../globals/payments/AddPayment.vue";
import EditSalaryForm from "./EditSalaryForm.vue";
import Confirmation from "../../globals/Confirmation.vue";

export default {
    props: ["salaries", "totals", "employeeId"],

    mixins: [CurrencyMixin],

    components: { Payments, AddPayment, EditSalaryForm, Confirmation },

    data() {
        return {
            currentSalaryRecord: null,
            addPaymentDialog: false,
            editDialog: false,
            salaryId: null,
        };
    },

    methods: {
        ...mapActions({
            getPaymentSetting: "getPaymentSetting",
            getSalary: "salary/getSalary",
            getEmployee: "employee/getEmployee",
            deleteSalary: "salary/deleteSalary",
        }),

        showAddPaymentDialog(currentSalaryRecord) {
            this.currentSalaryRecord = currentSalaryRecord;
            this.addPaymentDialog = true;
        },

        closeAddPaymentDialog() {
            this.currentSalaryRecord = null;
            this.addPaymentDialog = false;
        },

        getStatusType(status) {
            switch (status) {
                case "Partial":
                    return "info";

                case "Unpaid":
                    return "error";

                case "Paid":
                    return "success";

                case "Advance":
                    return "purple";
            }
        },

        showEditSalaryDialog(salaryId) {
            this.editDialog = true;
            this.getSalary(salaryId);
        },
        closeEditDialog() {
            this.editDialog = false;
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString("en-US", {
                month: "short",
                day: "2-digit",
                year: "numeric",
            });
        },

        setSalaryId(id) {
            this.salaryId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleSalaryDelete() {
            await this.deleteSalary({
                id: this.salaryId,
                employeeId: this.employeeId,
            });
            this.salaryId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },
    },

    computed: {
        ...mapGetters({
            paymentSetting: "paymentSetting",
            salary: "salary/salary",
            employee: "employee/employee",
        }),
    },

    mounted() {
        Promise.all([
            this.getPaymentSetting(),
            this.getEmployee(this.$route.params.employee_id),
        ]);
    },
};
</script>

<style scoped>
#salries_table {
    width: 100%;
    font-size: small !important;
    border-collapse: collapse;
}

#salries_table tr th,
td {
    padding: 10px !important;
}

#salries_table thead tr {
    background: rgb(65, 64, 64);
    color: #fff;
}

#salries_table tbody tr {
    background: #eaf3fb;
    font-weight: bold;
}

#salries_table #table-footer {
    background: rgb(65, 64, 64);
    color: #fff;
    font-weight: bold;
}
</style>
