<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Employees</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="employees"
                        class="elevation-1"
                        item-key="id"
                        :search="search"
                        :items-per-page="perPage"
                        :loading="loading"
                        :show-select="can('employee_delete') && !printMode"
                        loading-text="Loading employees..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>

                        <!-- Photo -->
                        <template
                            slot="item.photo"
                            slot-scope="props"
                            v-if="props.item.photo"
                        >
                            <v-img
                                :aspect-ratio="16 / 9"
                                width="50"
                                :src="props.item.photo"
                                class="rounded"
                                @click="showPhotoDialog(props.item.photo)"
                            ></v-img>
                        </template>

                        <!-- Salary -->
                        <template
                            slot="item.salary"
                            slot-scope="props"
                            v-if="props.item.salary"
                        >
                            <strong>{{ money(props.item.salary) }}</strong>
                        </template>

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('employee_delete')"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/employees/add"
                                class="ma-2 text-right"
                                v-if="can('employee_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Employee</v-btn
                            >

                            <Excel
                                module="employees"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <CSV
                                module="employees"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <PDF
                                module="employees"
                                :ids="selectedItems.map((item) => item.id)"
                            />

                            <v-text-field
                                v-model="search"
                                placeholder="Search"
                                class="mx-4"
                                append-icon="mdi-magnify"
                            ></v-text-field>
                        </template>

                        <!-- Actions -->
                        <template slot="item.actions" slot-scope="props">
                            <v-btn
                                x-small
                                text
                                color="primary"
                                @click="showEditDialog(props.item.id)"
                                title="Edit"
                                v-if="can('employee_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setEmployeeId(props.item.id)"
                                title="Delete"
                                v-if="can('employee_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="indigo"
                                @click="
                                    setCurrentEmployee({
                                        id: props.item.id,
                                        salary: props.item.salary,
                                    })
                                "
                                title="Pay Salary"
                                v-if="can('salary_create')"
                            >
                                <v-icon small>mdi-currency-usd</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="indigo"
                                :to="`/salaries/${props.item.id}`"
                                :title="`${props.item.name}'s Salaries`"
                                v-if="can('salary_access')"
                            >
                                <v-icon small>mdi-eye</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Edit dialog -->
                    <v-dialog v-model="editDialog" max-width="600" persistent>
                        <EditEmployee
                            :single-employee="employee"
                            v-if="employee"
                            @closeDialog="closeEditDialog"
                        />
                    </v-dialog>

                    <!-- Photo dialog -->
                    <v-dialog v-model="photoDialog" width="400">
                        <v-card>
                            <v-img
                                width="400"
                                :src="currentPhoto"
                                class="rounded"
                            ></v-img>
                        </v-card>
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="employeeId"
                        @confirmDeletion="
                            employeeId
                                ? handleEmployeeDelete()
                                : handleMultipleEmployeesDelete()
                        "
                    />

                    <!-- Pay salary dialog -->
                    <v-dialog
                        v-model="paySalaryDialog"
                        max-width="600"
                        persistent
                    >
                        <PaySalaryForm
                            @closeDialog="closePaySalaryDialog"
                            :current-employee="currentEmployee"
                        />
                    </v-dialog>
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditEmployee from "./EditEmployee.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import PaySalaryForm from "./partial/PaySalaryForm.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        EditEmployee,
        Navbar,
        Confirmation,
        Excel,
        CSV,
        PDF,
        PaySalaryForm,
    },

    data() {
        return {
            editDialog: false,
            photoDialog: false,
            headers: [
                { text: "S#", value: "sno" },
                { text: "Photo", value: "photo" },
                { text: "Employee Name", value: "name" },
                { text: "CNIC", value: "cnic" },
                { text: "Phone", value: "phone" },
                { text: "Salary", value: "salary" },
                { text: "Address", value: "address" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            employeeId: null,
            currentPhoto: null,
            currentEmployee: null,
            paySalaryDialog: false,
        };
    },

    methods: {
        ...mapActions({
            getEmployees: "employee/getEmployees",
            getEmployee: "employee/getEmployee",
            deleteEmployee: "employee/deleteEmployee",
            deleteMultipleEmployees: "employee/deleteMultipleEmployees",
        }),

        showEditDialog(id) {
            this.editDialog = true;

            this.getEmployee(id);
        },

        showPhotoDialog(currentPhoto) {
            this.photoDialog = true;
            this.currentPhoto = currentPhoto;
        },

        closeEditDialog() {
            this.editDialog = false;
        },

        setEmployeeId(id) {
            this.employeeId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleEmployeeDelete() {
            await this.deleteEmployee(this.employeeId);
            this.employeeId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleEmployeesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleEmployees(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },

        setCurrentEmployee(currentEmployee) {
            this.currentEmployee = currentEmployee;
            this.paySalaryDialog = true;
        },

        closePaySalaryDialog() {
            this.paySalaryDialog = false;
            this.currentEmployee = null;
        },
    },

    computed: {
        ...mapGetters({
            employees: "employee/employees",
            employee: "employee/employee",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("employee_edit") && !this.can("employee_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
        this.getEmployees();
    },
};
</script>
